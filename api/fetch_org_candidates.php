<?php
// API endpoint to fetch candidates by organization with vote tally

header('Content-Type: application/json');

require_once '../config/database.php';

if (!isset($_GET['organization'])) {
    echo json_encode(['error' => 'Organization parameter is required']);
    exit;
}

$organization = $_GET['organization'];
$conn = getConnection();

// Determine if this is a main or sub organization
$mainOrgs = ['USC', 'CSC'];
$isMainOrg = in_array($organization, $mainOrgs);

if ($isMainOrg) {
    // Query for main organization candidates with vote count
    $sql = "SELECT 
                c.id,
                c.first_name,
                c.middle_name,
                c.last_name,
                c.position,
                c.organization,
                COUNT(v.id) as total_votes
            FROM main_org_candidates c
            LEFT JOIN votes v ON v.candidate_id = c.id AND v.candidate_type = 'main'
            WHERE c.organization = ? AND c.status = 'Accepted'
            GROUP BY c.id, c.first_name, c.middle_name, c.last_name, c.position, c.organization
            ORDER BY c.position, c.last_name";
} else {
    // Query for sub organization candidates with vote count
    $sql = "SELECT 
                c.id,
                c.first_name,
                c.middle_name,
                c.last_name,
                c.year as position,
                c.organization,
                COUNT(v.id) as total_votes
            FROM sub_org_candidates c
            LEFT JOIN votes v ON v.candidate_id = c.id AND v.candidate_type = 'sub'
            WHERE c.organization = ? AND c.status = 'Accepted'
            GROUP BY c.id, c.first_name, c.middle_name, c.last_name, c.year, c.organization
            ORDER BY c.year, c.last_name";
}

$stmt = $conn->prepare($sql);
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
    'organization' => $organization,
    'candidates' => $candidates,
    'total_candidates' => count($candidates)
]);
?>