<?php
/**
 * Application Constants
 */

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_LIBRARIAN', 'librarian');
define('ROLE_STUDENT', 'student');

// Borrowing status
define('STATUS_BORROWED', 'borrowed');
define('STATUS_RETURNED', 'returned');
define('STATUS_OVERDUE', 'overdue');

// Book availability
define('BOOK_AVAILABLE', 'available');
define('BOOK_UNAVAILABLE', 'unavailable');

// Fine status
define('FINE_UNPAID', 'unpaid');
define('FINE_PAID', 'paid');

// Notification types
define('NOTIFICATION_SUCCESS', 'success');
define('NOTIFICATION_ERROR', 'error');
define('NOTIFICATION_WARNING', 'warning');
define('NOTIFICATION_INFO', 'info');

// Pagination
define('RECORDS_PER_PAGE', 10);

// Date formats
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'F d, Y');
define('DISPLAY_DATETIME_FORMAT', 'F d, Y h:i A');

// Borrow duration (days)
define('BORROW_DURATION', 14);
?>
