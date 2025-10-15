<?php
// Reporting Module Controller

class ReportController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Check if results can be viewed (only when voting is closed)
     */
    public function canViewResults() {
        $scheduleQuery = "SELECT status FROM voting_schedule ORDER BY updated_at DESC LIMIT 1";
        $result = $this->conn->query($scheduleQuery);
        
        if ($result && $result->num_rows > 0) {
            $schedule = $result->fetch_assoc();
            return $schedule['status'] === 'closed';
        }
        
        return true; // Default to true if no schedule exists
    }
    
    public function generateReport($reportType) {
        // Check if results can be viewed
        if (!$this->canViewResults()) {
            return [
                'title' => 'Report Not Available',
                'data' => [],
                'error' => 'Reports are only available when voting is closed to ensure result integrity.'
            ];
        }
        
        $reportData = [];
        $reportTitle = '';
        
        switch($reportType) {
            case 'voters_summary':
                $reportTitle = 'Voters Summary Report';
                // FIXED: Changed voter_id to user_id and vote_date to voted_at
                $sql = "SELECT 
                            u.id, 
                            u.student_id, 
                            u.email,
                            u.college,
                            CASE WHEN v.user_id IS NOT NULL THEN 'Voted' ELSE 'Not Voted' END as vote_status,
                            MIN(v.voted_at) as voted_at
                        FROM users u 
                        LEFT JOIN votes v ON u.id = v.user_id 
                        WHERE u.role = 'voter'
                        GROUP BY u.id 
                        ORDER BY u.student_id ASC";
                break;
                
            case 'candidates_summary':
                $reportTitle = 'Candidates Summary Report';
                $sql = "SELECT 'Main Organization' as org_type, id, 
                               CONCAT(last_name, ', ', first_name, ' ', COALESCE(middle_name, '')) as full_name,
                               organization as organization, position, status, filing_date
                        FROM main_org_candidates
                        UNION ALL
                        SELECT 'Sub Organization' as org_type, id,
                               CONCAT(last_name, ', ', first_name, ' ', COALESCE(middle_name, '')) as full_name,
                               organization as organization, position, status, filing_date
                        FROM sub_org_candidates
                        ORDER BY filing_date DESC";
                break;
                
            case 'voting_activity':
                $reportTitle = 'Voting Activity Report';
                // FIXED: Changed vote_date to voted_at
                $sql = "SELECT 
                            DATE(v.voted_at) as vote_date,
                            COUNT(DISTINCT v.user_id) as unique_voters,
                            COUNT(*) as total_votes,
                            HOUR(v.voted_at) as vote_hour
                        FROM votes v 
                        GROUP BY DATE(v.voted_at), HOUR(v.voted_at)
                        ORDER BY vote_date DESC, vote_hour ASC";
                break;
                
            case 'filing_status':
                $reportTitle = 'Filing Status Report';
                $sql = "SELECT 'Main Organization' as org_type, status, COUNT(*) as count
                        FROM main_org_candidates
                        GROUP BY status
                        UNION ALL
                        SELECT 'Sub Organization' as org_type, status, COUNT(*) as count
                        FROM sub_org_candidates
                        GROUP BY status
                        ORDER BY org_type, status";
                break;
                
            case 'complete_election':
                $reportTitle = 'Complete Election Report';
                return [
                    'title' => $reportTitle,
                    'data' => $this->generateCompleteElectionReport()
                ];
                
            default:
                return ['title' => '', 'data' => []];
        }
        
        $result = $this->conn->query($sql);
        if ($result) {
            $reportData = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
        }
        
        return ['title' => $reportTitle, 'data' => $reportData];
    }
    
    private function generateCompleteElectionReport() {
        $data = [];
        
        // Voters statistics - FIXED: Changed voter_id to user_id and vote_date to voted_at
        $votersResult = $this->conn->query("SELECT 
            COUNT(DISTINCT u.id) as total_voters,
            COUNT(DISTINCT v.user_id) as voted_count
            FROM users u 
            LEFT JOIN votes v ON u.id = v.user_id 
            WHERE u.role = 'voter'");
        $data['voters_stats'] = $votersResult->fetch_assoc();
        
        // Candidates statistics
        $mainCandidatesResult = $this->conn->query("SELECT status, COUNT(*) as count FROM main_org_candidates GROUP BY status");
        $subCandidatesResult = $this->conn->query("SELECT status, COUNT(*) as count FROM sub_org_candidates GROUP BY status");
        $data['main_candidates'] = $mainCandidatesResult->fetch_all(MYSQLI_ASSOC);
        $data['sub_candidates'] = $subCandidatesResult->fetch_all(MYSQLI_ASSOC);
        
        // Voting schedule
        $scheduleResult = $this->conn->query("SELECT * FROM voting_schedule ORDER BY updated_at DESC LIMIT 1");
        $data['schedule'] = $scheduleResult ? $scheduleResult->fetch_assoc() : null;
        
        // Daily voting activity - FIXED: Changed vote_date to voted_at
        $activityResult = $this->conn->query("SELECT 
            DATE(voted_at) as vote_date, 
            COUNT(DISTINCT user_id) as unique_voters,
            COUNT(*) as total_votes 
            FROM votes 
            GROUP BY DATE(voted_at) 
            ORDER BY vote_date DESC");
        $data['daily_activity'] = $activityResult ? $activityResult->fetch_all(MYSQLI_ASSOC) : [];
        
        return $data;
    }
}
?>