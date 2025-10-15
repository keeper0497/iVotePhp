<?php
// Commissioner Management Controller

class CommissionerController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Approve a candidate
     */
    public function approveCandidate($candidateId, $table) {
        if ($candidateId <= 0) {
            return ['success' => false, 'message' => 'Invalid candidate ID'];
        }
        
        if (!in_array($table, ['main_org_candidates', 'sub_org_candidates'])) {
            return ['success' => false, 'message' => 'Invalid table'];
        }
        
        $stmt = $this->conn->prepare("UPDATE $table SET status = 'Accepted' WHERE id = ?");
        $stmt->bind_param("i", $candidateId);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Candidate approved successfully!'];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => 'Error approving candidate: ' . $error];
        }
    }
    
    /**
     * Reject a candidate
     */
    public function rejectCandidate($candidateId, $table, $reason) {
        if ($candidateId <= 0) {
            return ['success' => false, 'message' => 'Invalid candidate ID'];
        }
        
        if (!in_array($table, ['main_org_candidates', 'sub_org_candidates'])) {
            return ['success' => false, 'message' => 'Invalid table'];
        }
        
        if (empty($reason)) {
            return ['success' => false, 'message' => 'Rejection reason is required'];
        }
        
        $stmt = $this->conn->prepare("UPDATE $table SET status = 'Rejected', comment = ? WHERE id = ?");
        $stmt->bind_param("si", $reason, $candidateId);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Candidate rejected successfully!'];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => 'Error rejecting candidate: ' . $error];
        }
    }
    
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats() {
        $stats = [];
        
        // Total candidates
        $mainCount = $this->conn->query("SELECT COUNT(*) as count FROM main_org_candidates")->fetch_assoc()['count'];
        $subCount = $this->conn->query("SELECT COUNT(*) as count FROM sub_org_candidates")->fetch_assoc()['count'];
        $stats['totalCandidates'] = $mainCount + $subCount;
        
        // Pending candidates
        $pendingMain = $this->conn->query("SELECT COUNT(*) as count FROM main_org_candidates WHERE status='Pending'")->fetch_assoc()['count'];
        $pendingSub = $this->conn->query("SELECT COUNT(*) as count FROM sub_org_candidates WHERE status='Pending'")->fetch_assoc()['count'];
        $stats['pendingCandidates'] = $pendingMain + $pendingSub;
        
        // Accepted candidates
        $acceptedMain = $this->conn->query("SELECT COUNT(*) as count FROM main_org_candidates WHERE status='Accepted'")->fetch_assoc()['count'];
        $acceptedSub = $this->conn->query("SELECT COUNT(*) as count FROM sub_org_candidates WHERE status='Accepted'")->fetch_assoc()['count'];
        $stats['acceptedCandidates'] = $acceptedMain + $acceptedSub;
        
        // Total votes (unique users who voted)
        $votesResult = $this->conn->query("SELECT COUNT(DISTINCT user_id) as count FROM votes");
        $stats['totalVotes'] = $votesResult ? $votesResult->fetch_assoc()['count'] : 0;
        
        return $stats;
    }
    
    /**
     * Get all accepted main organization candidates
     */
    public function getAcceptedMainCandidates() {
        $sql = "SELECT * FROM main_org_candidates WHERE status='Accepted' ORDER BY organization, position";
        $result = $this->conn->query($sql);
        
        if (!$result) {
            return [];
        }
        
        $candidates = [];
        while ($row = $result->fetch_assoc()) {
            $candidates[] = $row;
        }
        
        return $candidates;
    }
    
    /**
     * Get all accepted sub organization candidates
     */
    public function getAcceptedSubCandidates() {
        $sql = "SELECT * FROM sub_org_candidates WHERE status='Accepted' ORDER BY organization, year";
        $result = $this->conn->query($sql);
        
        if (!$result) {
            return [];
        }
        
        $candidates = [];
        while ($row = $result->fetch_assoc()) {
            $candidates[] = $row;
        }
        
        return $candidates;
    }
    
    /**
     * Get vote tallies (only when voting is closed)
     */
    public function getVoteTallies() {
        $tallies = ['main' => [], 'sub' => []];
        
        // Main organization tallies
        $mainQuery = "SELECT 
                        m.organization,
                        m.first_name,
                        m.middle_name,
                        m.last_name,
                        m.position,
                        COUNT(v.id) as vote_count
                      FROM main_org_candidates m
                      LEFT JOIN votes v ON m.id = v.candidate_id 
                        AND v.organization_type = 'Main'
                        AND v.position = m.position
                      WHERE m.status = 'Accepted'
                      GROUP BY m.id
                      ORDER BY m.organization, m.position, vote_count DESC";
        
        $mainResult = $this->conn->query($mainQuery);
        if ($mainResult) {
            while ($row = $mainResult->fetch_assoc()) {
                $org = $row['organization'];
                if (!isset($tallies['main'][$org])) {
                    $tallies['main'][$org] = [];
                }
                $tallies['main'][$org][] = $row;
            }
        }
        
        // Sub organization tallies
        $subQuery = "SELECT 
                        s.organization,
                        s.first_name,
                        s.middle_name,
                        s.last_name,
                        COALESCE(s.year, 'Representative') as position,
                        COUNT(v.id) as vote_count
                     FROM sub_org_candidates s
                     LEFT JOIN votes v ON s.id = v.candidate_id 
                        AND v.organization_type = 'Sub'
                        AND v.position = s.organization
                     WHERE s.status = 'Accepted'
                     GROUP BY s.id
                     ORDER BY s.organization, vote_count DESC";
        
        $subResult = $this->conn->query($subQuery);
        if ($subResult) {
            while ($row = $subResult->fetch_assoc()) {
                $org = $row['organization'];
                if (!isset($tallies['sub'][$org])) {
                    $tallies['sub'][$org] = [];
                }
                $tallies['sub'][$org][] = $row;
            }
        }
        
        return $tallies;
    }
}
?>