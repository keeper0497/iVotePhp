<?php
// Voting Schedule Controller

class VotingScheduleController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
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