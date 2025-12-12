-- SQL script to create the filing_schedule table for CATSU iVote

-- Create filing_schedule table
CREATE TABLE IF NOT EXISTS filing_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'closed',
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Constraints
    CHECK (end_date > start_date)
);

-- Insert default closed filing schedule if table is empty
INSERT IGNORE INTO filing_schedule (id, status, start_date, end_date, description) 
VALUES (1, 'closed', NOW(), DATE_ADD(NOW(), INTERVAL 1 HOUR), 'Default filing schedule - update as needed');

-- Create indexes for performance
CREATE INDEX idx_filing_schedule_status ON filing_schedule(status);
CREATE INDEX idx_filing_schedule_dates ON filing_schedule(start_date, end_date);
CREATE INDEX idx_filing_schedule_updated ON filing_schedule(updated_at);

-- Show the table structure
DESCRIBE filing_schedule;

-- Show current data
SELECT * FROM filing_schedule;