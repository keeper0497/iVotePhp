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

// Get position limits for multi-select validation
$positionLimits = $voterController->getPositionLimits();

// ------------------- HANDLE POST REQUESTS -------------------

if (!empty($_POST)) {
    error_log("=== POST REQUEST RECEIVED ===");
    error_log("POST Data: " . print_r($_POST, true));
    error_log("User ID: " . $userId);
}

// Enhanced vote submission detection - check for main_ or sub_ fields (including arrays)
$isVoteSubmission = false;
foreach ($_POST as $key => $value) {
    if (strpos($key, 'main_') === 0 || strpos($key, 'sub_') === 0) {
        // Additional check to ensure it's not empty
        if (is_array($value)) {
            // For checkbox arrays, check if any value is not empty
            $hasValidValue = false;
            foreach ($value as $v) {
                if (!empty($v) && is_numeric($v)) {
                    $hasValidValue = true;
                    break;
                }
            }
            if ($hasValidValue) {
                $isVoteSubmission = true;
                break;
            }
        } else {
            // For single values (radio buttons)
            if (!empty($value) && is_numeric($value)) {
                $isVoteSubmission = true;
                break;
            }
        }
    }
}

if ($isVoteSubmission && $_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Vote submission detected via field detection");
    
    $votingStatus = $scheduleController->canVote();
    
    if (!$votingStatus['can_vote']) {
        $_SESSION['message'] = "Voting is currently closed.";
        $_SESSION['messageType'] = 'error';
    } else {
        // Pre-process POST data to clean up array values
        $cleanedPostData = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'main_') === 0 || strpos($key, 'sub_') === 0) {
                if (is_array($value)) {
                    // Filter out empty values from arrays
                    $cleanedValue = array_filter($value, function($v) {
                        return !empty($v) && is_numeric($v);
                    });
                    if (!empty($cleanedValue)) {
                        $cleanedPostData[$key] = array_values($cleanedValue); // Re-index array
                    }
                } else {
                    // Single value
                    if (!empty($value) && is_numeric($value)) {
                        $cleanedPostData[$key] = $value;
                    }
                }
            }
        }
        
        error_log("Cleaned POST data: " . print_r($cleanedPostData, true));
        
        if (empty($cleanedPostData)) {
            $_SESSION['message'] = "Please select at least one candidate before submitting your vote.";
            $_SESSION['messageType'] = 'error';
        } else {
            $result = $voterController->submitVote($userId, $cleanedPostData);
            error_log("Vote result: " . print_r($result, true));
            
            $_SESSION['message'] = $result['message'];
            $_SESSION['messageType'] = $result['success'] ? 'success' : 'error';
        }
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

// Get user's votes if they have voted (for display purposes)
$userVotes = [];
if ($hasVoted) {
    $userVotes = $voterController->getUserVotes($userId);
}

error_log("Has voted status for user $userId: " . ($hasVoted ? 'YES' : 'NO'));
error_log("Position limits: " . print_r($positionLimits, true));

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'] ?? 'info';
    unset($_SESSION['message'], $_SESSION['messageType']);
}

require_once __DIR__ . '/views/voter_view.php';
$conn->close();
?>