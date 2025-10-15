<?php
// User Management Controller

class UserController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function addUser($email, $student_id, $password, $role, $college) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->conn->prepare("INSERT INTO users (email, student_id, password, role, college) VALUES (?, ?, ?, ?, ?)");
        
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
    
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
        
        if (!$stmt) {
            return ['success' => false, 'message' => "Prepare failed: " . $this->conn->error];
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            $result = ['success' => false, 'message' => "Error deleting user: " . $stmt->error];
        } else {
            $result = ['success' => true, 'message' => "User deleted successfully!"];
        }
        
        $stmt->close();
        return $result;
    }
    
    public function getAllUsers() {
        $result = $this->conn->query("SELECT * FROM users");
        
        if (!$result) {
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getVoters() {
        $sql = "SELECT u.id, u.email, u.student_id, u.role, u.college, 
                       MAX(v.vote_date) as voted_at 
                FROM users u 
                LEFT JOIN votes v ON u.id = v.users_id 
                WHERE u.role = 'voter' 
                GROUP BY u.id, u.email, u.student_id, u.role, u.college
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
                WHERE u.role = 'voter'
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

}
?>