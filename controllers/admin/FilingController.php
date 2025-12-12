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

    /**
     * Update filing schedule
     * Similar to voting schedule but for filing periods
     */
    public function updateFilingSchedule($filing_status, $start_date, $end_date, $description = '', $resetFilings = false) {
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
            // Check if we're closing filing and should reset filings
            if ($filing_status === 'closed' && $resetFilings) {
                $deleteResult = $this->resetAllFilings();
                if (!$deleteResult['success']) {
                    throw new Exception($deleteResult['message']);
                }
            }
            
            // Update or insert filing schedule
            $checkStmt = $this->conn->query("SELECT id FROM filing_schedule LIMIT 1");
            
            if ($checkStmt->num_rows > 0) {
                $stmt = $this->conn->prepare("UPDATE filing_schedule SET status=?, start_date=?, end_date=?, description=?, updated_at=NOW() WHERE id=(SELECT id FROM (SELECT id FROM filing_schedule LIMIT 1) as temp)");
                $stmt->bind_param("ssss", $filing_status, $start_date, $end_date, $description);
            } else {
                $stmt = $this->conn->prepare("INSERT INTO filing_schedule (status, start_date, end_date, description, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                $stmt->bind_param("ssss", $filing_status, $start_date, $end_date, $description);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating filing schedule: " . $stmt->error);
            }
            
            $stmt->close();
            
            // Commit transaction
            $this->conn->commit();
            
            $message = 'Filing schedule updated successfully!';
            if ($filing_status === 'closed' && $resetFilings) {
                $message .= ' All filings have been reset.';
            }
            
            return ['success' => true, 'message' => $message];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Update filing schedule error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Reset all filings in the database
     */
    public function resetAllFilings() {
        try {
            $this->conn->begin_transaction();
            
            // Get counts before deletion
            $mainCount = $this->conn->query("SELECT COUNT(*) as total FROM main_org_candidates")->fetch_assoc()['total'];
            $subCount = $this->conn->query("SELECT COUNT(*) as total FROM sub_org_candidates")->fetch_assoc()['total'];
            $totalCount = $mainCount + $subCount;
            
            // Delete all filings
            $mainResult = $this->conn->query("DELETE FROM main_org_candidates");
            $subResult = $this->conn->query("DELETE FROM sub_org_candidates");
            
            if ($mainResult && $subResult) {
                $this->conn->commit();
                error_log("Reset all filings: Deleted $mainCount main org filings and $subCount sub org filings");
                return [
                    'success' => true, 
                    'message' => "Successfully reset $totalCount filings ($mainCount main org + $subCount sub org)",
                    'count' => $totalCount,
                    'main_count' => $mainCount,
                    'sub_count' => $subCount
                ];
            } else {
                throw new Exception("Error deleting filings: " . $this->conn->error);
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Reset filings error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Reset a specific user's filings
     */
    public function resetUserFilings($userId) {
        try {
            $this->conn->begin_transaction();
            
            $stmt1 = $this->conn->prepare("DELETE FROM main_org_candidates WHERE user_id = ?");
            $stmt1->bind_param("i", $userId);
            $stmt1->execute();
            $mainAffected = $stmt1->affected_rows;
            $stmt1->close();
            
            $stmt2 = $this->conn->prepare("DELETE FROM sub_org_candidates WHERE user_id = ?");
            $stmt2->bind_param("i", $userId);
            $stmt2->execute();
            $subAffected = $stmt2->affected_rows;
            $stmt2->close();
            
            $totalAffected = $mainAffected + $subAffected;
            
            $this->conn->commit();
            
            return [
                'success' => true, 
                'message' => "Successfully reset user's filings ($totalAffected filings deleted: $mainAffected main org + $subAffected sub org)",
                'count' => $totalAffected,
                'main_count' => $mainAffected,
                'sub_count' => $subAffected
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Reset user filings error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get current filing schedule
     */
    public function getCurrentFilingSchedule() {
        $result = $this->conn->query("SELECT * FROM filing_schedule ORDER BY updated_at DESC LIMIT 1");
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Check if filing is currently active
     */
    public function isFilingActive($schedule) {
        if (!$schedule || $schedule['status'] !== 'open') {
            return false;
        }
        
        $now = new DateTime();
        $startDate = new DateTime($schedule['start_date']);
        $endDate = new DateTime($schedule['end_date']);
        
        return ($now >= $startDate && $now <= $endDate);
    }
    
    /**
     * Get total filings count
     */
    public function getTotalFilingsCount() {
        $mainResult = $this->conn->query("SELECT COUNT(*) as total FROM main_org_candidates");
        $subResult = $this->conn->query("SELECT COUNT(*) as total FROM sub_org_candidates");
        
        $mainCount = $mainResult ? $mainResult->fetch_assoc()['total'] : 0;
        $subCount = $subResult ? $subResult->fetch_assoc()['total'] : 0;
        
        return [
            'total' => $mainCount + $subCount,
            'main_count' => $mainCount,
            'sub_count' => $subCount
        ];
    }
    
    /**
     * Check if filing is currently allowed
     */
    public function canFile() {
        $result = $this->conn->query("SELECT * FROM filing_schedule WHERE status='open' ORDER BY updated_at DESC LIMIT 1");
        
        if (!$result || $result->num_rows === 0) {
            return [
                'can_file' => false, 
                'message' => 'Filing is currently closed by administrator.',
                'schedule' => null
            ];
        }
        
        $schedule = $result->fetch_assoc();
        $now = new DateTime();
        $startDate = new DateTime($schedule['start_date']);
        $endDate = new DateTime($schedule['end_date']);
        
        if ($now < $startDate) {
            return [
                'can_file' => false, 
                'message' => 'Filing will start on ' . $startDate->format('F j, Y g:i A'),
                'schedule' => $schedule
            ];
        }
        
        if ($now > $endDate) {
            return [
                'can_file' => false, 
                'message' => 'Filing period ended on ' . $endDate->format('F j, Y g:i A'),
                'schedule' => $schedule
            ];
        }
        
        return [
            'can_file' => true, 
            'message' => 'Filing is currently active until ' . $endDate->format('F j, Y g:i A'),
            'schedule' => $schedule
        ];
    }

    
}
?>