<?php
// Reporting Module Controller

class ReportController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function generateReport($reportType) {
        $reportData = [];
        $reportTitle = '';
        
        switch($reportType) {
            case 'voters_summary':
                $reportTitle = 'Voters Summary Report';
                $sql = "SELECT 
                            u.id, 
                            u.student_id, 
                            u.email, 
                            CASE WHEN v.vote_date IS NOT NULL THEN 'Voted' ELSE 'Not Voted' END as vote_status,
                            v.vote_date as voted_at
                        FROM users u 
                        LEFT JOIN votes v ON u.id = v.voter_id 
                        WHERE u.role = 'voter' 
                        ORDER BY u.student_id ASC";
                break;
                
            case 'candidates_summary':
                $reportTitle = 'Candidates Summary Report';
                $sql = "SELECT 'Main Organization' as org_type, id, 
                               CONCAT(last_name, ', ', first_name, ' ', middle_name) as full_name,
                               organization as organization, position, status, filing_date
                        FROM main_org_candidates
                        UNION ALL
                        SELECT 'Sub Organization' as org_type, id,
                               CONCAT(last_name, ', ', first_name, ' ', middle_name) as full_name,
                               organization as organization, year as position, status, filing_date
                        FROM sub_org_candidates
                        ORDER BY filing_date DESC";
                break;
                
            case 'voting_activity':
                $reportTitle = 'Voting Activity Report';
                $sql = "SELECT 
                            DATE(v.vote_date) as vote_date,
                            COUNT(*) as votes_count,
                            HOUR(v.vote_date) as vote_hour
                        FROM votes v 
                        GROUP BY DATE(v.vote_date), HOUR(v.vote_date)
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
        
        // Voters statistics
        $votersResult = $this->conn->query("SELECT 
            COUNT(*) as total_voters,
            COUNT(CASE WHEN v.vote_date IS NOT NULL THEN 1 END) as voted_count
            FROM users u 
            LEFT JOIN votes v ON u.id = v.voter_id 
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
        
        // Daily voting activity
        $activityResult = $this->conn->query("SELECT 
            DATE(vote_date) as vote_date, 
            COUNT(*) as votes_count 
            FROM votes 
            GROUP BY DATE(vote_date) 
            ORDER BY vote_date DESC");
        $data['daily_activity'] = $activityResult->fetch_all(MYSQLI_ASSOC);
        
        return $data;
    }
}
?>