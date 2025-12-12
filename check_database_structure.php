<?php
// Database Structure Checker - helps identify table and column names
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Database Structure Checker</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $conn = getConnection();
    
    // Check what tables exist
    echo "<h2>Available Tables:</h2>";
    $tables = $conn->query("SHOW TABLES");
    $tableList = [];
    
    if ($tables) {
        while ($row = $tables->fetch_array()) {
            $tableName = $row[0];
            $tableList[] = $tableName;
            echo "• " . $tableName . "<br>";
        }
    }
    
    // Check structure of candidate tables
    if (in_array('main_org_candidates', $tableList)) {
        echo "<h2>main_org_candidates table structure:</h2>";
        $result = $conn->query("DESCRIBE main_org_candidates");
        if ($result) {
            echo "<table border='1'><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
            }
            echo "</table>";
            
            // Sample data
            echo "<h3>Sample main_org_candidates data:</h3>";
            $sampleResult = $conn->query("SELECT * FROM main_org_candidates LIMIT 5");
            if ($sampleResult && $sampleResult->num_rows > 0) {
                echo "<table border='1'>";
                $firstRow = true;
                while ($row = $sampleResult->fetch_assoc()) {
                    if ($firstRow) {
                        echo "<tr>";
                        foreach (array_keys($row) as $header) {
                            echo "<th>$header</th>";
                        }
                        echo "</tr>";
                        $firstRow = false;
                    }
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No data found in main_org_candidates table";
            }
        }
    }
    
    if (in_array('sub_org_candidates', $tableList)) {
        echo "<h2>sub_org_candidates table structure:</h2>";
        $result = $conn->query("DESCRIBE sub_org_candidates");
        if ($result) {
            echo "<table border='1'><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
            }
            echo "</table>";
            
            // Sample data
            echo "<h3>Sample sub_org_candidates data:</h3>";
            $sampleResult = $conn->query("SELECT * FROM sub_org_candidates LIMIT 5");
            if ($sampleResult && $sampleResult->num_rows > 0) {
                echo "<table border='1'>";
                $firstRow = true;
                while ($row = $sampleResult->fetch_assoc()) {
                    if ($firstRow) {
                        echo "<tr>";
                        foreach (array_keys($row) as $header) {
                            echo "<th>$header</th>";
                        }
                        echo "</tr>";
                        $firstRow = false;
                    }
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No data found in sub_org_candidates table";
            }
        }
    }
    
    if (in_array('votes', $tableList)) {
        echo "<h2>votes table structure:</h2>";
        $result = $conn->query("DESCRIBE votes");
        if ($result) {
            echo "<table border='1'><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
            }
            echo "</table>";
            
            // Sample votes data
            echo "<h3>Sample votes data:</h3>";
            $sampleResult = $conn->query("SELECT * FROM votes LIMIT 10");
            if ($sampleResult && $sampleResult->num_rows > 0) {
                echo "<table border='1'>";
                $firstRow = true;
                while ($row = $sampleResult->fetch_assoc()) {
                    if ($firstRow) {
                        echo "<tr>";
                        foreach (array_keys($row) as $header) {
                            echo "<th>$header</th>";
                        }
                        echo "</tr>";
                        $firstRow = false;
                    }
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No data found in votes table";
            }
        }
    }
    
    if (in_array('voting_schedule', $tableList)) {
        echo "<h2>voting_schedule table:</h2>";
        $result = $conn->query("SELECT * FROM voting_schedule ORDER BY id DESC LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $schedule = $result->fetch_assoc();
            echo "<table border='1'>";
            foreach ($schedule as $key => $value) {
                echo "<tr><td><strong>$key</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No voting schedule found";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>