<?php
// Public API for fetching organization candidates - CORRECTED for actual database structure
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Include necessary files
require_once __DIR__ . '/../config/database.php';

try {
    // Get database connection
    $conn = getConnection();
    
    // Check voting status first
    $scheduleQuery = "SELECT status FROM voting_schedule ORDER BY id DESC LIMIT 1";
    $scheduleResult = $conn->query($scheduleQuery);
    
    if ($scheduleResult && $scheduleResult->num_rows > 0) {
        $schedule = $scheduleResult->fetch_assoc();
        $votingStatus = $schedule['status'];
    } else {
        $votingStatus = 'closed';
    }
    
    // Only show results when voting is closed
    if ($votingStatus !== 'closed') {
        echo json_encode([
            'error' => 'Vote results are not yet available. Voting is still in progress.',
            'voting_status' => 'open',
            'message' => 'Results will be displayed once voting has officially ended.'
        ]);
        exit;
    }
    
    // Get the organization parameter
    $organization = isset($_GET['organization']) ? trim($_GET['organization']) : '';
    
    if (empty($organization)) {
        echo json_encode(['error' => 'Organization parameter is required']);
        exit;
    }
    
    // Determine if main or sub organization
    $isMainOrg = in_array($organization, ['USC', 'CSC']);
    $candidates = [];
    
    if ($isMainOrg) {
        // MAIN ORGANIZATION - Get candidates with vote counts
        $sql = "SELECT 
                    m.id,
                    m.first_name, 
                    m.middle_name, 
                    m.last_name, 
                    m.position, 
                    m.organization,
                    COUNT(v.id) as total_votes
                FROM main_org_candidates m
                LEFT JOIN votes v ON m.id = v.candidate_id AND v.organization_type = 'Main'
                WHERE m.status = 'Accepted' AND m.organization = ?
                GROUP BY m.id, m.first_name, m.middle_name, m.last_name, m.position, m.organization
                ORDER BY m.position, total_votes DESC";
        
    } else {
        // SUB ORGANIZATION - Get candidates with vote counts
        $sql = "SELECT 
                    s.id,
                    s.first_name, 
                    s.middle_name, 
                    s.last_name, 
                    s.position_sub as position, 
                    s.organization,
                    COUNT(v.id) as total_votes
                FROM sub_org_candidates s
                LEFT JOIN votes v ON s.id = v.candidate_id AND v.organization_type = 'Sub'
                WHERE s.status = 'Accepted' AND s.organization = ?
                GROUP BY s.id, s.first_name, s.middle_name, s.last_name, s.position_sub, s.organization
                ORDER BY s.position_sub, total_votes DESC";
    }
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode([
            'error' => 'Database prepare error: ' . $conn->error,
            'sql' => $sql
        ]);
        exit;
    }
    
    $stmt->bind_param('s', $organization);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'candidates' => $candidates,
        'organization' => $organization,
        'voting_status' => 'closed',
        'is_main_org' => $isMainOrg,
        'total_candidates' => count($candidates)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>