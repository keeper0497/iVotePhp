<?php
// Vote Tally API - Works for Admin & Commissioner (always), Voter (only when closed)

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized access. Please login.']);
    exit;
}

$conn = getConnection();

// Get organization from request
$organization = $_GET['organization'] ?? '';

if (empty($organization)) {
    echo json_encode(['error' => 'Organization parameter is required']);
    exit;
}

// Check if this is a privileged view request (admin or commissioner)
$isPrivilegedView = isset($_GET['privileged_view']) && $_GET['privileged_view'] == '1';
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isCommissioner = isset($_SESSION['role']) && $_SESSION['role'] === 'commissioner';
$isPrivilegedUser = $isAdmin || $isCommissioner;

// CHECK VOTING STATUS
$scheduleQuery = "SELECT status FROM voting_schedule ORDER BY updated_at DESC LIMIT 1";
$scheduleResult = $conn->query($scheduleQuery);

if ($scheduleResult && $scheduleResult->num_rows > 0) {
    $schedule = $scheduleResult->fetch_assoc();
    $votingStatus = $schedule['status'];
} else {
    $votingStatus = 'closed';
}

// ONLY ALLOW TALLY VIEWING WHEN VOTING IS CLOSED (UNLESS USER IS ADMIN/COMMISSIONER)
if ($votingStatus !== 'closed' && !$isPrivilegedUser && !$isPrivilegedView) {
    echo json_encode([
        'error' => 'Vote tally is not available while voting is active',
        'voting_status' => 'open',
        'message' => 'Results will be available after voting closes'
    ]);
    exit;
}

// If privileged user is viewing while voting is open, add a flag to the response
$isRealTimeView = ($votingStatus === 'open' && ($isPrivilegedUser || $isPrivilegedView));

// Determine if main or sub organization
$isMainOrg = in_array($organization, ['USC', 'CSC']);

if ($isMainOrg) {
    // MAIN ORGANIZATION QUERY
    $sql = "SELECT 
                m.id,
                m.first_name,
                m.middle_name,
                m.last_name,
                m.position,
                m.organization,
                COUNT(v.id) as total_votes
            FROM main_org_candidates m
            LEFT JOIN votes v ON m.id = v.candidate_id 
                AND v.organization_type = 'Main'
                AND v.position = m.position
            WHERE m.status = 'Accepted' AND m.organization = ?
            GROUP BY m.id
            ORDER BY m.position, total_votes DESC";
} else {
    // SUB ORGANIZATION QUERY
    // FIXED: Use position_sub field and proper vote counting
    $sql = "SELECT 
                s.id,
                s.first_name,
                s.middle_name,
                s.last_name,
                s.position_sub as position,
                s.organization,
                COALESCE(vote_counts.total_votes, 0) as total_votes
            FROM sub_org_candidates s
            LEFT JOIN (
                SELECT 
                    candidate_id,
                    COUNT(*) as total_votes
                FROM votes 
                WHERE organization_type = 'Sub'
                GROUP BY candidate_id
            ) vote_counts ON s.id = vote_counts.candidate_id
            WHERE s.status = 'Accepted' AND s.organization = ?
            ORDER BY s.position_sub, s.last_name";
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        'error' => 'Database error: ' . $conn->error,
        'debug' => 'Check if sub_org_candidates table structure matches query'
    ]);
    exit;
}

$stmt->bind_param("s", $organization);
$stmt->execute();
$result = $stmt->get_result();

$candidates = [];
while ($row = $result->fetch_assoc()) {
    $candidates[] = $row;
}

$stmt->close();
$conn->close();

// Build response
$response = [
    'success' => true,
    'voting_status' => $votingStatus,
    'organization' => $organization,
    'is_main_org' => $isMainOrg,
    'candidates' => $candidates,
    'total_candidates' => count($candidates)
];

// Add privileged user metadata
if ($isRealTimeView) {
    $response['real_time_view'] = true;
    $response['privileged_access'] = true;
    $userType = $isAdmin ? 'Admin' : 'Commissioner';
    $response['note'] = "$userType viewing real-time results while voting is open";
}

echo json_encode($response);
?>