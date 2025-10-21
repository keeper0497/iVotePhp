<?php
class VoterController {
    private $conn;
    
    // Position limits configuration
    private $positionLimits = [
        'Senators' => 8,
        'Legislator' => 2,
        'Representative' => 8
    ];
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    private function isMultiSelectPosition($position) {
        return isset($this->positionLimits[$position]) && $this->positionLimits[$position] > 1;
    }
    
    private function getPositionLimit($position) {
        return $this->positionLimits[$position] ?? 1;
    }
    
    public function submitVote($userId, $postData) {
        error_log("=== VOTE SUBMISSION DEBUG ===");
        error_log("User ID: " . $userId);
        error_log("POST Data: " . print_r($postData, true));
        
        if ($this->hasVoted($userId)) {
            error_log("User $userId has already voted");
            return ['success' => false, 'message' => "Voting failed: You have already cast your vote."];
        }

        // Validate multi-select positions before processing
        $validationResult = $this->validateMultiSelectPositions($postData);
        if (!$validationResult['valid']) {
            return ['success' => false, 'message' => $validationResult['message']];
        }

        $this->conn->begin_transaction();
        
        try {
            // For multi-select positions, we need to handle the unique constraint differently
            // We'll use a modified approach that stores position with an index for multi-select
            $stmt = $this->conn->prepare("INSERT INTO votes (user_id, candidate_id, position, organization_type) VALUES (?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Database prepare error: " . $this->conn->error);
            }
            
            $successCount = 0;
            $processedVotes = [];
            
            foreach ($postData as $key => $value) {
                // Skip non-voting fields
                if (!$this->isVotingField($key)) {
                    continue;
                }
                
                // Handle both single values and arrays (for multi-select)
                $candidateIds = is_array($value) ? $value : [$value];
                
                $parts = explode('_', $key, 2);
                $orgType = $parts[0] ?? '';
                $identifier = $parts[1] ?? '';
                
                $basePosition = '';
                $organizationType = '';

                if ($orgType === 'main') {
                    $organizationType = 'Main';
                    $basePosition = str_replace('_', ' ', $identifier); 
                } elseif ($orgType === 'sub') {
                    $organizationType = 'Sub';
                    $basePosition = str_replace('_', ' ', $identifier); 
                } else {
                    error_log("Unrecognized org type: $orgType");
                    continue;
                }
                
                $isMultiSelect = $this->isMultiSelectPosition($basePosition);
                $voteIndex = 1;
                
                foreach ($candidateIds as $candidateId) {
                    if (empty($candidateId) || !is_numeric($candidateId)) {
                        error_log("Skipping invalid candidate ID: $candidateId for key: $key");
                        continue;
                    }

                    error_log("Processing: Key=$key, OrgType=$orgType, Identifier=$identifier, CandidateID=$candidateId");
                    
                    // For multi-select positions, append an index to make position unique
                    $position = $isMultiSelect ? $basePosition . "_" . $voteIndex : $basePosition;
                    
                    $candidateId = intval($candidateId);

                    if ($candidateId > 0) {
                        error_log("Inserting vote: UserID=$userId, CandidateID=$candidateId, Position=$position, OrgType=$organizationType");
                        
                        $stmt->bind_param("iiss", $userId, $candidateId, $position, $organizationType);
                        
                        if ($stmt->execute()) {
                            $successCount++;
                            $processedVotes[] = [
                                'candidate_id' => $candidateId,
                                'position' => $position,
                                'base_position' => $basePosition,
                                'org_type' => $organizationType
                            ];
                            error_log("Vote inserted successfully. Affected rows: " . $stmt->affected_rows);
                            
                            if ($isMultiSelect) {
                                $voteIndex++;
                            }
                        } else {
                            error_log("Execute failed: " . $stmt->error);
                            throw new Exception("Vote insertion failed: " . $stmt->error);
                        }
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
    
    private function isVotingField($key) {
        return (strpos($key, 'main_') === 0 || strpos($key, 'sub_') === 0) && $key !== 'submitVote';
    }
    
    private function validateMultiSelectPositions($postData) {
        $errors = [];
        
        foreach ($postData as $key => $value) {
            if (!$this->isVotingField($key)) {
                continue;
            }
            
            $parts = explode('_', $key, 2);
            $orgType = $parts[0] ?? '';
            $identifier = $parts[1] ?? '';
            
            if ($orgType === 'main') {
                $position = str_replace('_', ' ', $identifier);
                
                if ($this->isMultiSelectPosition($position)) {
                    $limit = $this->getPositionLimit($position);
                    $candidateIds = is_array($value) ? $value : [$value];
                    
                    // Filter out empty values
                    $validCandidates = array_filter($candidateIds, function($id) {
                        return !empty($id) && is_numeric($id);
                    });
                    
                    if (count($validCandidates) > $limit) {
                        $errors[] = "Too many candidates selected for {$position}. Maximum allowed: {$limit}, Selected: " . count($validCandidates);
                    }
                    
                    error_log("Position validation: {$position} - Limit: {$limit}, Selected: " . count($validCandidates));
                }
            }
        }
        
        if (!empty($errors)) {
            return [
                'valid' => false,
                'message' => "Validation failed: " . implode('; ', $errors)
            ];
        }
        
        return ['valid' => true];
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
    
    public function getUserVotes($userId) {
        $votes = [];
        
        $stmt = $this->conn->prepare("
            SELECT v.*, 
                   COALESCE(m.first_name, s.first_name) as candidate_first_name,
                   COALESCE(m.last_name, s.last_name) as candidate_last_name,
                   COALESCE(m.position, s.position_sub) as candidate_position,
                   COALESCE(m.organization, s.organization) as candidate_organization
            FROM votes v
            LEFT JOIN main_org_candidates m ON v.candidate_id = m.id AND v.organization_type = 'Main'
            LEFT JOIN sub_org_candidates s ON v.candidate_id = s.id AND v.organization_type = 'Sub'
            WHERE v.user_id = ?
            ORDER BY v.organization_type, v.position
        ");
        
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                // Clean up position names for display (remove _1, _2, etc. for multi-select)
                if (preg_match('/^(.+)_\d+$/', $row['position'], $matches)) {
                    $row['display_position'] = $matches[1];
                    $row['is_multi_select'] = true;
                } else {
                    $row['display_position'] = $row['position'];
                    $row['is_multi_select'] = false;
                }
                $votes[] = $row;
            }
            
            $stmt->close();
        }
        
        return $votes;
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
    
    public function getVotingStatsByPosition() {
        $stats = [];
        
        // Get vote counts by position for main organization (handling multi-select positions)
        $stmt = $this->conn->query("
            SELECT 
                CASE 
                    WHEN position REGEXP '^(.+)_[0-9]+$' THEN REGEXP_REPLACE(position, '_[0-9]+$', '')
                    ELSE position 
                END as base_position,
                COUNT(*) as vote_count 
            FROM votes 
            WHERE organization_type = 'Main' 
            GROUP BY base_position 
            ORDER BY base_position
        ");
        
        if ($stmt) {
            while ($row = $stmt->fetch_assoc()) {
                $stats['main'][$row['base_position']] = $row['vote_count'];
            }
            $stmt->free();
        }
        
        // Get vote counts by organization for sub organization
        $stmt = $this->conn->query("
            SELECT 
                CASE 
                    WHEN position REGEXP '^(.+)_[0-9]+$' THEN REGEXP_REPLACE(position, '_[0-9]+$', '')
                    ELSE position 
                END as base_position,
                COUNT(*) as vote_count 
            FROM votes 
            WHERE organization_type = 'Sub' 
            GROUP BY base_position 
            ORDER BY base_position
        ");
        
        if ($stmt) {
            while ($row = $stmt->fetch_assoc()) {
                $stats['sub'][$row['base_position']] = $row['vote_count'];
            }
            $stmt->free();
        }
        
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
    
    public function getPositionLimits() {
        return $this->positionLimits;
    }
}
?>