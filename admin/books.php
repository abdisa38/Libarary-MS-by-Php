<?php
/**
 * Books Management
 */

session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian')) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../config/database.php';
require_once '../models/Book.php';
require_once '../models/Author.php';
require_once '../models/Category.php';
require_once '../models/Publisher.php';

define('PAGE_TITLE', 'Books Management');

$bookModel = new Book($db);
$authorModel = new Author($db);
$categoryModel = new Category($db);
$publisherModel = new Publisher($db);

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $data = [
                    'isbn' => sanitize($_POST['isbn']),
                    'title' => sanitize($_POST['title']),
                    'author_id' => $_POST['author_id'],
                    'category_id' => $_POST['category_id'],
                    'publisher_id' => $_POST['publisher_id'],
                    'quantity' => $_POST['quantity'],
                    'shelf_number' => sanitize($_POST['shelf_number']),
                    'description' => sanitize($_POST['description']),
                    'date_added' => date('Y-m-d')
                ];
                
                // Handle book cover upload
                if (isset($_FILES['book_cover']) && $_FILES['book_cover']['error'] === 0) {
                    $upload = uploadFile($_FILES['book_cover'], 'books');
                    if ($upload['success']) {
                        $data['book_cover'] = $upload['path'];
                    }
                }
                
                if ($bookModel->create($data)) {
                    logActivity($db, $_SESSION['user_id'], 'add_book', "Added book: {$data['title']}");
                    $_SESSION['message'] = 'Book added successfully';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Failed to add book';
                    $_SESSION['message_type'] = 'error';
                }
                header('Location: books.php');
                exit();
                
            case 'edit':
                $id = $_POST['id'];
                $data = [
                    'isbn' => sanitize($_POST['isbn']),
                    'title' => sanitize($_POST['title']),
                    'author_id' => $_POST['author_id'],
                    'category_id' => $_POST['category_id'],
                    'publisher_id' => $_POST['publisher_id'],
                    'quantity' => $_POST['quantity'],
                    'shelf_number' => sanitize($_POST['shelf_number']),
                    'description' => sanitize($_POST['description'])
                ];
                
                if (isset($_FILES['book_cover']) && $_FILES['book_cover']['error'] === 0) {
                    $upload = uploadFile($_FILES['book_cover'], 'books');
                    if ($upload['success']) {
                        $bookModel->updateCover($id, $upload['path']);
                    }
                }
                
                if ($bookModel->update($id, $data)) {
                    logActivity($db, $_SESSION['user_id'], 'edit_book', "Edited book ID: $id");
                    $_SESSION['message'] = 'Book updated successfully';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Failed to update book';
                    $_SESSION['message_type'] = 'error';
                }
                header('Location: books.php');
                exit();
                
            case 'delete':
                $id = $_POST['id'];
                if ($bookModel->delete($id)) {
                    logActivity($db, $_SESSION['user_id'], 'delete_book', "Deleted book ID: $id");
                    $_SESSION['message'] = 'Book deleted successfully';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Failed to delete book';
                    $_SESSION['message_type'] = 'error';
                }
                header('Location: books.php');
                exit();
        }
    }
}

// Get filters
$search = $_GET['search'] ?? '';
$category_id = $_GET['category'] ?? null;
$author_id = $_GET['author'] ?? null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;

// Get books
$books = $bookModel->getAll($search, $category_id, $author_id, $page, $limit);
$totalBooks = $bookModel->count($search, $category_id, $author_id);
$totalPages = ceil($totalBooks / $limit);

// Get authors, categories, publishers for dropdowns
$authors = $authorModel->getAll();
$categories = $categoryModel->getAll();
$publishers = $publisherModel->getAll();

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Books Management</h1>
    <p class="page-subtitle">Manage your library book collection</p>
    <div class="page-actions">
        <button class="btn btn-primary btn-icon" onclick="openModal('addBookModal')">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add New Book
        </button>
    </div>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
        <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    </div>
<?php endif; ?>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" class="form-control" placeholder="Title, ISBN, Author..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="category">Category</label>
                <select id="category" name="category" class="form-control">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="author">Author</label>
                <select id="author" name="author" class="form-control">
                    <option value="">All Authors</option>
                    <?php foreach ($authors as $author): ?>
                        <option value="<?php echo $author['id']; ?>" <?php echo $author_id == $author['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($author['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="books.php" class="btn btn-outline">Clear</a>
        </form>
    </div>
</div>

<!-- Books Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Available</th>
                        <th>Shelf</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="empty-state">
                                    <p>No books found</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($books as $book): ?>
                        <tr>
                            <td>
                                <?php if ($book['book_cover']): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($book['book_cover']); ?>" alt="Cover" style="width: 40px; height: 60px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <div style="width: 40px; height: 60px; background: var(--gray-lightest); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                            <td><strong><?php echo htmlspecialchars($book['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($book['author_name']); ?></td>
                            <td><span class="badge badge-secondary"><?php echo htmlspecialchars($book['category_name']); ?></span></td>
                            <td><?php echo $book['quantity']; ?></td>
                            <td>
                                <span class="badge <?php echo $book['available_copies'] > 0 ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $book['available_copies']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($book['shelf_number']); ?></td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn btn-sm btn-outline" onclick='editBook(<?php echo json_encode($book); ?>)' title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this book?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <ul class="pagination">
            <li class="<?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_id; ?>&author=<?php echo $author_id; ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="<?php echo $i == $page ? 'active' : ''; ?>">
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_id; ?>&author=<?php echo $author_id; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="<?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_id; ?>&author=<?php echo $author_id; ?>">Next</a>
            </li>
        </ul>
        <?php endif; ?>
    </div>
</div>

<!-- Add Book Modal -->
<div class="modal" id="addBookModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Book</h2>
            <button class="modal-close" onclick="closeModal('addBookModal')">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="isbn">ISBN *</label>
                        <input type="text" id="isbn" name="isbn" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="title">Title *</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="author_id">Author *</label>
                        <select id="author_id" name="author_id" class="form-control" required>
                            <option value="">Select Author</option>
                            <?php foreach ($authors as $author): ?>
                                <option value="<?php echo $author['id']; ?>"><?php echo htmlspecialchars($author['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category *</label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="publisher_id">Publisher *</label>
                        <select id="publisher_id" name="publisher_id" class="form-control" required>
                            <option value="">Select Publisher</option>
                            <?php foreach ($publishers as $pub): ?>
                                <option value="<?php echo $pub['id']; ?>"><?php echo htmlspecialchars($pub['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity *</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="shelf_number">Shelf Number</label>
                    <input type="text" id="shelf_number" name="shelf_number" class="form-control" placeholder="e.g., A-01">
                </div>
                
                <div class="form-group">
                    <label for="book_cover">Book Cover</label>
                    <input type="file" id="book_cover" name="book_cover" class="form-control" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addBookModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Book</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Book Modal -->
<div class="modal" id="editBookModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Book</h2>
            <button class="modal-close" onclick="closeModal('editBookModal')">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data" id="editBookForm">
            <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_isbn">ISBN *</label>
                        <input type="text" id="edit_isbn" name="isbn" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_title">Title *</label>
                        <input type="text" id="edit_title" name="title" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_author_id">Author *</label>
                        <select id="edit_author_id" name="author_id" class="form-control" required>
                            <?php foreach ($authors as $author): ?>
                                <option value="<?php echo $author['id']; ?>"><?php echo htmlspecialchars($author['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_category_id">Category *</label>
                        <select id="edit_category_id" name="category_id" class="form-control" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_publisher_id">Publisher *</label>
                        <select id="edit_publisher_id" name="publisher_id" class="form-control" required>
                            <?php foreach ($publishers as $pub): ?>
                                <option value="<?php echo $pub['id']; ?>"><?php echo htmlspecialchars($pub['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_quantity">Quantity *</label>
                        <input type="number" id="edit_quantity" name="quantity" class="form-control" min="1" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_shelf_number">Shelf Number</label>
                    <input type="text" id="edit_shelf_number" name="shelf_number" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="edit_book_cover">Change Book Cover</label>
                    <input type="file" id="edit_book_cover" name="book_cover" class="form-control" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('editBookModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Book</button>
            </div>
        </form>
    </div>
</div>

<script>
function editBook(book) {
    document.getElementById('edit_id').value = book.id;
    document.getElementById('edit_isbn').value = book.isbn;
    document.getElementById('edit_title').value = book.title;
    document.getElementById('edit_author_id').value = book.author_id;
    document.getElementById('edit_category_id').value = book.category_id;
    document.getElementById('edit_publisher_id').value = book.publisher_id;
    document.getElementById('edit_quantity').value = book.quantity;
    document.getElementById('edit_shelf_number').value = book.shelf_number || '';
    document.getElementById('edit_description').value = book.description || '';
    openModal('editBookModal');
}
</script>

<?php include '../includes/footer.php'; ?>
