<?php
// Dashboard Statistics Controller

class DashboardController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function getDashboardStats() {
        $stats = [];
        
        // Total voters
        $votersResult = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'voter'");
        if ($votersResult) {
            $stats['total_voters'] = $votersResult->fetch_assoc()['count'];
            $votersResult->free();
        } else {
            $stats['total_voters'] = 0;
        }
        
        // Total candidates (accepted only)
        $candidatesMainResult = $this->conn->query("SELECT COUNT(*) as count FROM main_org_candidates WHERE status = 'Accepted'");
        $candidatesSubResult = $this->conn->query("SELECT COUNT(*) as count FROM sub_org_candidates WHERE status = 'Accepted'");
        
        if ($candidatesMainResult && $candidatesSubResult) {
            $mainCount = $candidatesMainResult->fetch_assoc()['count'];
            $subCount = $candidatesSubResult->fetch_assoc()['count'];
            $stats['total_candidates'] = $mainCount + $subCount;
            $candidatesMainResult->free();
            $candidatesSubResult->free();
        } else {
            $stats['total_candidates'] = 0;
        }
        
        // Students who voted
        $votesResult = $this->conn->query("SELECT COUNT(DISTINCT user_id) as count FROM votes");
        if ($votesResult) {
            $stats['students_voted'] = $votesResult->fetch_assoc()['count'];
            $votesResult->free();
        } else {
            $stats['students_voted'] = 0;
        }
        
        // Calculate voting percentage
        $stats['voting_percentage'] = $stats['total_voters'] > 0 
            ? round(($stats['students_voted'] / $stats['total_voters']) * 100, 1) 
            : 0;
        
        return $stats;
    }
}
?>