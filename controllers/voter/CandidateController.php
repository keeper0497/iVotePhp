<?php
// Candidate Controller

class CandidateController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function getMainOrgCandidates($organization = null) {
        if ($organization) {
            $stmt = $this->conn->prepare("SELECT * FROM main_org_candidates WHERE status='Accepted' AND organization=? ORDER BY filing_date DESC");
            $stmt->bind_param("s", $organization);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM main_org_candidates WHERE status='Accepted' ORDER BY filing_date DESC");
        }
        
        if (!$stmt) {
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $candidates = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $candidates;
    }
    
    public function getSubOrgCandidates($organization = null) {
        if ($organization) {
            $stmt = $this->conn->prepare("SELECT * FROM sub_org_candidates WHERE status='Accepted' AND organization=? ORDER BY filing_date DESC");
            $stmt->bind_param("s", $organization);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM sub_org_candidates WHERE status='Accepted' ORDER BY filing_date DESC");
        }
        
        if (!$stmt) {
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $candidates = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $candidates;
    }
}
?>