<?php
// User Management Controller

class UserController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function addUser($email, $student_id, $password, $role, $college) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->conn->prepare("INSERT INTO users (email, student_id, password, role, college, status) VALUES (?, ?, ?, ?, ?, 'active')");
        
        if (!$stmt) {
            return ['success' => false, 'message' => "Prepare failed: " . $this->conn->error];
        }
        
        $stmt->bind_param("sssss", $email, $student_id, $hashedPassword, $role, $college);
        
        if (!$stmt->execute()) {
            $result = ['success' => false, 'message' => "Error adding user: " . $stmt->error];
        } else {
            $result = ['success' => true, 'message' => "User added successfully!"];
        }
        
        $stmt->close();
        return $result;
    }
    
    public function updateUser($id, $email, $student_id, $password, $role, $college) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->conn->prepare("UPDATE users SET email=?, student_id=?, password=?, role=?, college=? WHERE id=?");
        
        if (!$stmt) {
            return ['success' => false, 'message' => "Prepare failed: " . $this->conn->error];
        }
        
        $stmt->bind_param("sssssi", $email, $student_id, $hashedPassword, $role, $college, $id);
        
        if (!$stmt->execute()) {
            $result = ['success' => false, 'message' => "Error updating user: " . $stmt->error];
        } else {
            $result = ['success' => true, 'message' => "User updated successfully!"];
        }
        
        $stmt->close();
        return $result;
    }
    
    public function deactivateUser($id) {
        $stmt = $this->conn->prepare("UPDATE users SET status='deactivated' WHERE id=?");
        
        if (!$stmt) {
            return ['success' => false, 'message' => "Prepare failed: " . $this->conn->error];
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            $result = ['success' => false, 'message' => "Error deactivating user: " . $stmt->error];
        } else {
            $result = ['success' => true, 'message' => "User deactivated successfully!"];
        }
        
        $stmt->close();
        return $result;
    }
    
    public function reactivateUser($id) {
        $stmt = $this->conn->prepare("UPDATE users SET status='active' WHERE id=?");
        
        if (!$stmt) {
            return ['success' => false, 'message' => "Prepare failed: " . $this->conn->error];
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            $result = ['success' => false, 'message' => "Error reactivating user: " . $stmt->error];
        } else {
            $result = ['success' => true, 'message' => "User reactivated successfully!"];
        }
        
        $stmt->close();
        return $result;
    }
    
    public function getAllUsers($includeDeactivated = true) {
        if ($includeDeactivated) {
            $sql = "SELECT * FROM users ORDER BY status ASC, id ASC";
        } else {
            $sql = "SELECT * FROM users WHERE status = 'active' ORDER BY id ASC";
        }
        
        $result = $this->conn->query($sql);
        
        if (!$result) {
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getVoters() {
        $sql = "SELECT u.id, u.email, u.student_id, u.role, u.college, u.status,
                       MAX(v.vote_date) as voted_at 
                FROM users u 
                LEFT JOIN votes v ON u.id = v.user_id 
                WHERE u.role = 'voter' AND u.status = 'active'
                GROUP BY u.id, u.email, u.student_id, u.role, u.college, u.status
                ORDER BY u.student_id ASC";
        
        $result = $this->conn->query($sql);
        
        if (!$result) {
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getVotersWithStatus() {
        $sql = "SELECT 
                    u.id,
                    u.student_id,
                    u.email,
                    u.college,
                    u.role,
                    u.status,
                    CASE 
                        WHEN v.user_id IS NOT NULL THEN 1 
                        ELSE 0 
                    END as has_voted,
                    MIN(v.voted_at) as voted_at
                FROM users u
                LEFT JOIN (
                    SELECT DISTINCT user_id, MIN(voted_at) as voted_at
                    FROM votes
                    GROUP BY user_id
                ) v ON u.id = v.user_id
                WHERE u.role = 'voter' AND u.status = 'active'
                GROUP BY u.id
                ORDER BY u.id ASC";
        
        $result = $this->conn->query($sql);
        
        if (!$result) {
            error_log("Error fetching voters with status: " . $this->conn->error);
            return [];
        }
        
        $voters = [];
        while ($row = $result->fetch_assoc()) {
            $voters[] = $row;
        }
        
        return $voters;
    }
    
    public function getUserStats() {
        $stats = [];
        
        // Total active users
        $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
        $stats['active_users'] = $result ? $result->fetch_assoc()['count'] : 0;
        
        // Total deactivated users
        $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'deactivated'");
        $stats['deactivated_users'] = $result ? $result->fetch_assoc()['count'] : 0;
        
        // Active voters
        $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'voter' AND status = 'active'");
        $stats['active_voters'] = $result ? $result->fetch_assoc()['count'] : 0;
        
        // Active admins
        $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin' AND status = 'active'");
        $stats['active_admins'] = $result ? $result->fetch_assoc()['count'] : 0;
        
        return $stats;
    }
}
?>