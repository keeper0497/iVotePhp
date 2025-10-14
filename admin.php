<?php
// Main Admin Panel File

// Include configuration files (using absolute paths)
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';

// Check admin authentication
checkAdminAuth();

// Include controllers (make sure filenames match exactly)
require_once __DIR__ . '/controllers/admin/UserController.php';
require_once __DIR__ . '/controllers/admin/FilingController.php';
require_once __DIR__ . '/controllers/admin/VotingController.php';
require_once __DIR__ . '/controllers/admin/ReportController.php';
require_once __DIR__ . '/controllers/admin/DashboardController.php';

// Include helpers (with absolute paths)
require_once __DIR__ . '/helpers/admin/FileHelper.php';
require_once __DIR__ . '/helpers/admin/ExportHelper.php';

// Get database connection
$conn = getConnection();

// Initialize controllers
$userController = new UserController($conn);
$filingController = new FilingController($conn);
$votingController = new VotingController($conn);
$reportController = new ReportController($conn);
$dashboardController = new DashboardController($conn);

// Initialize message variables
$message = '';
$messageType = '';

// ------------------- HANDLE POST REQUESTS -------------------

// User Management
if (isset($_POST['addUser'])) {
    $result = $userController->addUser(
        $_POST['email'],
        $_POST['student_id'],
        $_POST['password'],
        $_POST['role'],
        $_POST['college']
    );
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
}

if (isset($_POST['editUser'])) {
    $result = $userController->updateUser(
        $_POST['id'],
        $_POST['email'],
        $_POST['student_id'],
        $_POST['password'],
        $_POST['role'],
        $_POST['college']
    );
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
}

if (isset($_POST['deleteUser'])) {
    $result = $userController->deleteUser($_POST['id']);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
}

// Filing Management
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['filing_id'], $_POST['type'])) {
    $result = $filingController->updateFilingStatus(
        intval($_POST['filing_id']),
        $_POST['type'],
        $_POST['action'],
        $_POST['comment'] ?? ''
    );
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
}

// Voting Schedule Management
if (isset($_POST['updateVotingSchedule'])) {
    $result = $votingController->updateSchedule(
        $_POST['voting_status'],
        $_POST['start_date'] ?? null,
        $_POST['end_date'] ?? null,
        $_POST['description'] ?? ''
    );
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
}

// Report Generation
$reportData = [];
$reportType = '';
$reportTitle = '';

if (isset($_POST['generateReport'])) {
    $reportType = $_POST['report_type'];
    $report = $reportController->generateReport($reportType);
    $reportTitle = $report['title'];
    $reportData = $report['data'];
    
    // Store in session for export
    $_SESSION['report_data'] = serialize($reportData);
    $_SESSION['report_type'] = $reportType;
    $_SESSION['report_title'] = $reportTitle;
}

// Export Report
if (isset($_POST['exportReport']) && isset($_SESSION['report_data'])) {
    $exportType = $_POST['export_type'];
    $reportData = unserialize($_SESSION['report_data']);
    $reportType = $_SESSION['report_type'];
    $reportTitle = $_SESSION['report_title'];
    
    if ($exportType === 'csv') {
        ExportHelper::exportToCSV($reportData, $reportType, $reportTitle);
    } elseif ($exportType === 'excel') {
        ExportHelper::exportToExcel($reportData, $reportType, $reportTitle);
    }
}

// ------------------- FETCH DATA FOR VIEWS -------------------

// Get all data needed for views
$allUsers = $userController->getAllUsers();
$mainFilings = $filingController->getMainFilings();
$subFilings = $filingController->getSubFilings();
// $voters = $userController->getVoters(); // Keep this for vote status tracking
$votersWithStatus = $userController->getVotersWithStatus(); // <- ADD THIS NEW LINE
$votingSchedule = $votingController->getCurrentSchedule();
$dashboardStats = $dashboardController->getDashboardStats();

// Include the view file
require_once __DIR__ . '/views/admin_view.php';

// Close database connection
$conn->close();
?>