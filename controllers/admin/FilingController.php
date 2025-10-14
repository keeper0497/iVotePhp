<?php
// Filing Management Controller

class FilingController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function updateFilingStatus($filing_id, $type, $action, $comment = '') {
        if (!in_array($action, ['accept', 'reject']) || !in_array($type, ['main', 'sub'])) {
            return ['success' => false, 'message' => 'Invalid action or type'];
        }
        
        $status = $action === 'accept' ? 'Accepted' : 'Rejected';
        $table = $type === 'main' ? 'main_org_candidates' : 'sub_org_candidates';
        
        $stmt = $this->conn->prepare("UPDATE $table SET status=?, comment=? WHERE id=?");
        
        if (!$stmt) {
            return ['success' => false, 'message' => "Prepare failed: " . $this->conn->error];
        }
        
        $stmt->bind_param("ssi", $status, $comment, $filing_id);
        
        if (!$stmt->execute()) {
            $result = ['success' => false, 'message' => "Error updating filing: " . $stmt->error];
        } else {
            $result = ['success' => true, 'message' => "Filing #$filing_id has been $status."];
        }
        
        $stmt->close();
        return $result;
    }
    
    public function getMainFilings() {
        $result = $this->conn->query("SELECT * FROM main_org_candidates ORDER BY filing_date DESC");
        
        if (!$result) {
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getSubFilings() {
        $result = $this->conn->query("SELECT * FROM sub_org_candidates ORDER BY filing_date DESC");
        
        if (!$result) {
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>