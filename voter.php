
<?php
// Main Voter Panel File

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'voter') {
    header('Location: home.php');
    exit;
}

require_once __DIR__ . '/controllers/voter/VoterController.php';
require_once __DIR__ . '/controllers/voter/VotingScheduleController.php';
require_once __DIR__ . '/controllers/voter/CandidateController.php';
require_once __DIR__ . '/controllers/voter/FilingSubmissionController.php';

$conn = getConnection();

$voterController = new VoterController($conn);
$scheduleController = new VotingScheduleController($conn);
$candidateController = new CandidateController($conn);
$filingController = new FilingSubmissionController($conn);

$message = '';
$messageType = '';
$userId = $_SESSION['user_id'];

// ------------------- HANDLE POST REQUESTS -------------------

if (!empty($_POST)) {
    error_log("=== POST REQUEST RECEIVED ===");
    error_log("POST Data: " . print_r($_POST, true));
    error_log("User ID: " . $userId);
}

// FIXED: Check for vote submission by looking for main_ or sub_ fields instead of submitVote button
$isVoteSubmission = false;
foreach ($_POST as $key => $value) {
    if (strpos($key, 'main_') === 0 || strpos($key, 'sub_') === 0) {
        $isVoteSubmission = true;
        break;
    }
}

if ($isVoteSubmission && $_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Vote submission detected via field detection");
    
    $votingStatus = $scheduleController->canVote();
    
    if (!$votingStatus['can_vote']) {
        $_SESSION['message'] = "Voting is currently closed.";
        $_SESSION['messageType'] = 'error';
    } else {
        $result = $voterController->submitVote($userId, $_POST);
        error_log("Vote result: " . print_r($result, true));
        
        $_SESSION['message'] = $result['message'];
        $_SESSION['messageType'] = $result['success'] ? 'success' : 'error';
    }
    
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit;
}

// Handle Main Organization Filing
if (isset($_POST['submitFiling'])) {
    $result = $filingController->submitMainOrgFiling($userId, $_POST, $_FILES);
    $_SESSION['message'] = $result['message'];
    $_SESSION['messageType'] = $result['success'] ? 'success' : 'error';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle Sub Organization Filing
if (isset($_POST['submitFilingSub'])) {
    $result = $filingController->submitSubOrgFiling($userId, $_POST);
    $_SESSION['message'] = $result['message'];
    $_SESSION['messageType'] = $result['success'] ? 'success' : 'error';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ------------------- FETCH DATA FOR VIEWS -------------------

$votingStatus = $scheduleController->canVote();
$dashboardStats = $voterController->getDashboardStats();
$notifications = $voterController->getNotifications($userId);
$notificationCount = count($notifications);
$mainCandidates = $candidateController->getMainOrgCandidates();
$subCandidates = $candidateController->getSubOrgCandidates();
$hasVoted = $voterController->hasVoted($userId);

error_log("Has voted status for user $userId: " . ($hasVoted ? 'YES' : 'NO'));

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'] ?? 'info';
    unset($_SESSION['message'], $_SESSION['messageType']);
}

require_once __DIR__ . '/views/voter_view.php';
$conn->close();
?>