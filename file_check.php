<?php
// check_files.php - File structure diagnostic

$basePath = __DIR__;
$requiredFiles = [
    'admin/config/session.php',
    'admin/config/database.php',
    'admin/controllers/UserController.php',
    'admin/controllers/FilingController.php',
    'admin/controllers/VotingController.php',
    'admin/controllers/ReportController.php',
    'admin/controllers/DashboardController.php',
    'admin/helpers/FileHelper.php',
    'admin/helpers/ExportHelper.php',
    'admin/views/admin_view.php'
];

echo "<h2>File Structure Check</h2>";
echo "<p><strong>Base Path:</strong> $basePath</p>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>File</th><th>Status</th><th>Full Path</th></tr>";

foreach ($requiredFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    $exists = file_exists($fullPath);
    $status = $exists ? '✅ EXISTS' : '❌ MISSING';
    $color = $exists ? 'green' : 'red';
    
    echo "<tr>";
    echo "<td>$file</td>";
    echo "<td style='color: $color; font-weight: bold;'>$status</td>";
    echo "<td style='font-size: 0.8em;'>$fullPath</td>";
    echo "</tr>";
}

echo "</table>";

// Check directories
echo "<h3>Directory Check</h3>";
$directories = ['config', 'controllers', 'helpers', 'views', 'api', 'uploads'];
echo "<ul>";
foreach ($directories as $dir) {
    $fullPath = $basePath . '/' . $dir;
    $exists = is_dir($fullPath);
    $status = $exists ? '✅' : '❌';
    echo "<li>$status <strong>$dir/</strong> - $fullPath</li>";
}
echo "</ul>";
?>