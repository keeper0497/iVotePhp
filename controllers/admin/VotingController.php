<?php
class VotingController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Update voting schedule
     * If status is changed to 'closed', optionally reset all votes
     */
    public function updateSchedule($voting_status, $start_date, $end_date, $description = '', $resetVotes = false) {
        if (!$start_date || !$end_date) {
            return ['success' => false, 'message' => 'Start and end dates are required.'];
        }
        
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        
        if ($end <= $start) {
            return ['success' => false, 'message' => 'End date must be after start date.'];
        }
        
        // Start transaction
        $this->conn->begin_transaction();
        
        try {
            // Check if we're closing voting and should reset votes
            if ($voting_status === 'closed' && $resetVotes) {
                $deleteResult = $this->resetAllVotes();
                if (!$deleteResult['success']) {
                    throw new Exception($deleteResult['message']);
                }
            }
            
            // Update or insert voting schedule
            $checkStmt = $this->conn->query("SELECT id FROM voting_schedule LIMIT 1");
            
            if ($checkStmt->num_rows > 0) {
                $stmt = $this->conn->prepare("UPDATE voting_schedule SET status=?, start_date=?, end_date=?, description=?, updated_at=NOW() WHERE id=(SELECT id FROM (SELECT id FROM voting_schedule LIMIT 1) as temp)");
                $stmt->bind_param("ssss", $voting_status, $start_date, $end_date, $description);
            } else {
                $stmt = $this->conn->prepare("INSERT INTO voting_schedule (status, start_date, end_date, description, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                $stmt->bind_param("ssss", $voting_status, $start_date, $end_date, $description);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating schedule: " . $stmt->error);
            }
            
            $stmt->close();
            
            // Commit transaction
            $this->conn->commit();
            
            $message = 'Voting schedule updated successfully!';
            if ($voting_status === 'closed' && $resetVotes) {
                $message .= ' All votes have been reset.';
            }
            
            return ['success' => true, 'message' => $message];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Update schedule error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Reset all votes in the database
     */
    public function resetAllVotes() {
        try {
            // Get count before deletion
            $countResult = $this->conn->query("SELECT COUNT(*) as total FROM votes");
            $count = $countResult->fetch_assoc()['total'];
            
            // Delete all votes
            $result = $this->conn->query("DELETE FROM votes");
            
            if ($result) {
                error_log("Reset all votes: Deleted $count votes");
                return [
                    'success' => true, 
                    'message' => "Successfully reset $count votes",
                    'count' => $count
                ];
            } else {
                throw new Exception("Error deleting votes: " . $this->conn->error);
            }
        } catch (Exception $e) {
            error_log("Reset votes error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Reset a single user's votes
     */
    public function resetUserVote($userId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM votes WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            
            if ($stmt->execute()) {
                $affected = $stmt->affected_rows;
                $stmt->close();
                return [
                    'success' => true, 
                    'message' => "Successfully reset user's votes ($affected votes deleted)",
                    'count' => $affected
                ];
            } else {
                throw new Exception("Error deleting user votes: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Reset user vote error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
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
    
    /**
     * Get total votes count
     */
    public function getTotalVotesCount() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM votes");
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        return 0;
    }

    /**
     * Check if voting is currently active
     */
    public function canVote() {
        $result = $this->conn->query("SELECT * FROM voting_schedule WHERE status='open' ORDER BY updated_at DESC LIMIT 1");
        
        if (!$result || $result->num_rows === 0) {
            return [
                'can_vote' => false, 
                'message' => 'Voting is currently closed by administrator.',
                'schedule' => null
            ];
        }
        
        $schedule = $result->fetch_assoc();
        $now = new DateTime();
        $startDate = new DateTime($schedule['start_date']);
        $endDate = new DateTime($schedule['end_date']);
        
        if ($now < $startDate) {
            return [
                'can_vote' => false, 
                'message' => 'Voting will start on ' . $startDate->format('F j, Y g:i A'),
                'schedule' => $schedule
            ];
        }
        
        if ($now > $endDate) {
            return [
                'can_vote' => false, 
                'message' => 'Voting period ended on ' . $endDate->format('F j, Y g:i A'),
                'schedule' => $schedule
            ];
        }
        
        return [
            'can_vote' => true, 
            'message' => 'Voting is currently active until ' . $endDate->format('F j, Y g:i A'),
            'schedule' => $schedule
        ];
    }
}
?>