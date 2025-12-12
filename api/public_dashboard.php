<?php
// Public API for dashboard statistics
// This endpoint can be accessed without authentication

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration and controller
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/public/PublicDashboardController.php';

// Get database connection
$conn = getConnection();

if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    // Initialize the public dashboard controller
    $dashboardController = new PublicDashboardController($conn);
    
    // Check if specific action is requested
    $action = $_GET['action'] ?? 'stats';
    
    switch ($action) {
        case 'stats':
            $stats = $dashboardController->getPublicDashboardStats();
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            break;
            
        case 'organizations':
            $orgs = $dashboardController->getOrganizationList();
            echo json_encode([
                'success' => true,
                'data' => $orgs
            ]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action parameter']);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>