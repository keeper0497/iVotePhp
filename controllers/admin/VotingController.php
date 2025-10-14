<?php
// Voting Schedule Management Controller

class VotingController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function updateSchedule($voting_status, $start_date, $end_date, $description = '') {
        if (!$start_date || !$end_date) {
            return ['success' => false, 'message' => 'Start and end dates are required.'];
        }
        
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        
        if ($end <= $start) {
            return ['success' => false, 'message' => 'End date must be after start date.'];
        }
        
        $checkStmt = $this->conn->query("SELECT id FROM voting_schedule LIMIT 1");
        
        if ($checkStmt->num_rows > 0) {
            $stmt = $this->conn->prepare("UPDATE voting_schedule SET status=?, start_date=?, end_date=?, description=?, updated_at=NOW() WHERE id=(SELECT id FROM (SELECT id FROM voting_schedule LIMIT 1) as temp)");
            $stmt->bind_param("ssss", $voting_status, $start_date, $end_date, $description);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO voting_schedule (status, start_date, end_date, description, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->bind_param("ssss", $voting_status, $start_date, $end_date, $description);
        }
        
        if ($stmt->execute()) {
            $result = ['success' => true, 'message' => 'Voting schedule updated successfully!'];
        } else {
            $result = ['success' => false, 'message' => "Error updating schedule: " . $stmt->error];
        }
        
        $stmt->close();
        return $result;
    }
    
    public function getCurrentSchedule() {
        $result = $this->conn->query("SELECT * FROM voting_schedule ORDER BY updated_at DESC LIMIT 1");
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    public function isVotingActive($schedule) {
        if (!$schedule || $schedule['status'] !== 'open') {
            return false;
        }
        
        $now = new DateTime();
        $startDate = new DateTime($schedule['start_date']);
        $endDate = new DateTime($schedule['end_date']);
        
        return ($now >= $startDate && $now <= $endDate);
    }
}
?>