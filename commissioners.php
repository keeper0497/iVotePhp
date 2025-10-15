<?php
// Main Commissioner Panel File

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';

// Check commissioner authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'commissioner') {
    header('Location: home.php');
    exit;
}

// Include controllers
require_once __DIR__ . '/controllers/commissioner/CommissionerController.php';
require_once __DIR__ . '/controllers/admin/VotingController.php'; // Shared with admin

// Get database connection
$conn = getConnection();

// Initialize controllers
$commissionerController = new CommissionerController($conn);
$scheduleController = new VotingController($conn);

// Initialize message variables
$message = '';
$messageType = '';

// Get session data
$userId = $_SESSION['user_id'];

// ------------------- HANDLE POST REQUESTS -------------------

// Handle Candidate Approval
if (isset($_POST['approveCandidate'])) {
    $result = $commissionerController->approveCandidate(
        intval($_POST['candidate_id']),
        $_POST['table']
    );
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
}

// Handle Candidate Rejection
if (isset($_POST['rejectCandidate'])) {
    $result = $commissionerController->rejectCandidate(
        intval($_POST['candidate_id']),
        $_POST['table'],
        $_POST['rejection_reason'] ?? ''
    );
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
}

// ------------------- FETCH DATA FOR VIEWS -------------------

// FIXED: Get voting status using VotingController
$votingStatus = $scheduleController->canVote();
$votingSchedule = $scheduleController->getCurrentSchedule();

// Get dashboard statistics
$dashboardStats = $commissionerController->getDashboardStats();

// Get all accepted candidates
$mainCandidates = $commissionerController->getAcceptedMainCandidates();
$subCandidates = $commissionerController->getAcceptedSubCandidates();

// Determine if voting is closed
$canViewTally = !$votingStatus['can_vote'];

// Get vote tallies (only if voting is closed)
if ($canViewTally) {
    $voteTallies = $commissionerController->getVoteTallies();
} else {
    $voteTallies = ['main' => [], 'sub' => []];
}

// Include the view file
require_once __DIR__ . '/views/commissioner_view.php';

// Close database connection
$conn->close();
?>
