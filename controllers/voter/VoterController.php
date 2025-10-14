
<?php
class VoterController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function submitVote($userId, $postData) {
        error_log("=== VOTE SUBMISSION DEBUG ===");
        error_log("User ID: " . $userId);
        error_log("POST Data: " . print_r($postData, true));
        
        if ($this->hasVoted($userId)) {
            error_log("User $userId has already voted");
            return ['success' => false, 'message' => "Voting failed: You have already cast your vote."];
        }

        $this->conn->begin_transaction();
        
        try {
            $stmt = $this->conn->prepare("INSERT INTO votes (user_id, candidate_id, position, organization_type) VALUES (?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Database prepare error: " . $this->conn->error);
            }
            
            $successCount = 0;
            $processedVotes = [];
            
            foreach ($postData as $key => $candidateId) {
                // FIXED: Removed the submitVote check since it's not in POST data
                if (empty($candidateId) || !is_numeric($candidateId)) {
                    error_log("Skipping key: $key (empty or non-numeric)");
                    continue;
                }

                $parts = explode('_', $key, 2);
                $orgType = $parts[0] ?? '';
                $identifier = $parts[1] ?? '';
                
                error_log("Processing: Key=$key, OrgType=$orgType, Identifier=$identifier, CandidateID=$candidateId");
                
                $position = '';
                $organizationType = '';

                if ($orgType === 'main') {
                    $organizationType = 'Main';
                    $position = str_replace('_', ' ', $identifier); 
                } elseif ($orgType === 'sub') {
                    $organizationType = 'Sub';
                    $position = str_replace('_', ' ', $identifier); 
                } else {
                    error_log("Unrecognized org type: $orgType");
                    continue;
                }

                $candidateId = intval($candidateId);

                if ($candidateId > 0) {
                    error_log("Inserting vote: UserID=$userId, CandidateID=$candidateId, Position=$position, OrgType=$organizationType");
                    
                    $stmt->bind_param("iiss", $userId, $candidateId, $position, $organizationType);
                    
                    if ($stmt->execute()) {
                        $successCount++;
                        $processedVotes[] = [
                            'candidate_id' => $candidateId,
                            'position' => $position,
                            'org_type' => $organizationType
                        ];
                        error_log("Vote inserted successfully. Affected rows: " . $stmt->affected_rows);
                    } else {
                        error_log("Execute failed: " . $stmt->error);
                        throw new Exception("Vote insertion failed: " . $stmt->error);
                    }
                }
            }
            
            $stmt->close();
            
            error_log("Total votes processed: $successCount");
            error_log("Processed votes: " . print_r($processedVotes, true));
            
            if ($successCount > 0) {
                $this->conn->commit();
                error_log("Transaction committed successfully");
                return ['success' => true, 'message' => "Successfully cast {$successCount} vote(s)! Thank you for voting."];
            } else {
                $this->conn->rollback();
                error_log("No votes recorded - transaction rolled back");
                return ['success' => false, 'message' => "No votes were recorded. Please select at least one candidate."];
            }
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Vote Submission Error: " . $e->getMessage());
            return ['success' => false, 'message' => "An error occurred while submitting your vote. Please try again."];
        }
    }
    
    public function hasVoted($userId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as vote_count FROM votes WHERE user_id = ?");
        if (!$stmt) {
            error_log("HasVoted DB Prepare Error: " . $this->conn->error);
            return true;
        }
        
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $hasVoted = ($row['vote_count'] > 0);
        
        error_log("User $userId vote check: " . ($hasVoted ? "HAS VOTED" : "NOT VOTED") . " (Count: {$row['vote_count']})");
        
        $stmt->close();
        return $hasVoted;
    }
    
    public function getDashboardStats() {
        $stats = [];
        
        $result = $this->conn->query("SELECT COUNT(*) as total FROM users WHERE role='voter'");
        $stats['totalVoters'] = $result ? intval($result->fetch_assoc()['total']) : 0;
        if ($result) $result->free();
        
        $result = $this->conn->query("SELECT COUNT(DISTINCT user_id) as voted FROM votes");
        $stats['voted'] = $result ? intval($result->fetch_assoc()['voted']) : 0;
        if ($result) $result->free();
        
        $stats['notVoted'] = $stats['totalVoters'] - $stats['voted'];
        
        $result = $this->conn->query("SELECT COUNT(*) as total FROM main_org_candidates WHERE status='Accepted'");
        $mainCount = $result ? intval($result->fetch_assoc()['total']) : 0;
        if ($result) $result->free();
        
        $result = $this->conn->query("SELECT COUNT(*) as total FROM sub_org_candidates WHERE status='Accepted'");
        $subCount = $result ? intval($result->fetch_assoc()['total']) : 0;
        if ($result) $result->free();
        
        $stats['totalCandidates'] = $mainCount + $subCount;
        
        return $stats;
    }
    
    public function getNotifications($userId) {
        $notifications = [];
        
        $stmt = $this->conn->prepare("SELECT id, status, comment FROM main_org_candidates WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                if ($row['status'] === 'Accepted') {
                    $notifications[] = "Your filing for Main Organization has been approved.";
                } elseif ($row['status'] === 'Rejected' && !empty($row['comment'])) {
                    $notifications[] = "Your Main Organization filing was rejected: " . $row['comment'];
                }
            }
            $stmt->close();
        }
        
        $stmt = $this->conn->prepare("SELECT id, status, comment FROM sub_org_candidates WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                if ($row['status'] === 'Accepted') {
                    $notifications[] = "Your filing for Sub Organization has been approved.";
                } elseif ($row['status'] === 'Rejected' && !empty($row['comment'])) {
                    $notifications[] = "Your Sub Organization filing was rejected: " . $row['comment'];
                }
            }
            $stmt->close();
        }
        
        return $notifications;
    }
}
?>