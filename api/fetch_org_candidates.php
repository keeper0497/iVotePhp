<?php
// Vote Tally API - Works for Admin, Voter, and Commissioner

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

// CHECK VOTING STATUS FIRST
$scheduleQuery = "SELECT status FROM voting_schedule ORDER BY updated_at DESC LIMIT 1";
$scheduleResult = $conn->query($scheduleQuery);

if ($scheduleResult && $scheduleResult->num_rows > 0) {
    $schedule = $scheduleResult->fetch_assoc();
    $votingStatus = $schedule['status'];
} else {
    $votingStatus = 'closed';
}

// ONLY ALLOW TALLY VIEWING WHEN VOTING IS CLOSED
if ($votingStatus !== 'closed') {
    echo json_encode([
        'error' => 'Vote tally is not available while voting is active',
        'voting_status' => 'open',
        'message' => 'Results will be available after voting closes'
    ]);
    exit;
}

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
    // Key: votes.position stores the ORGANIZATION NAME for sub orgs
    $sql = "SELECT 
                s.id,
                s.first_name,
                s.middle_name,
                s.last_name,
                COALESCE(s.year, 'Representative') as position,
                s.organization,
                COUNT(v.id) as total_votes
            FROM sub_org_candidates s
            LEFT JOIN votes v ON s.id = v.candidate_id 
                AND v.organization_type = 'Sub'
                AND v.position = s.organization
            WHERE s.status = 'Accepted' AND s.organization = ?
            GROUP BY s.id
            ORDER BY s.organization, total_votes DESC";
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

echo json_encode([
    'success' => true,
    'voting_status' => 'closed',
    'organization' => $organization,
    'is_main_org' => $isMainOrg,
    'candidates' => $candidates,
    'total_candidates' => count($candidates)
]);
?>