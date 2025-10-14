<?php
// Asset Path Checker
// Place in root: C:\xampp\htdocs\iVoteUpdated\catsu-ivote\check_assets.php

$basePath = __DIR__;

// Get base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptName = str_replace('check_assets.php', '', $_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . "://" . $host . $scriptName;

echo "<!DOCTYPE html>
<html>
<head>
    <title>Asset Path Checker</title>
    <style>
        body { font-family: Arial; max-width: 1000px; margin: 20px auto; padding: 20px; }
        h2 { color: #1e40af; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .success { color: #059669; font-weight: bold; }
        .error { color: #dc2626; font-weight: bold; }
        .path { font-family: monospace; background: #f3f4f6; padding: 5px; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #3b82f6; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        .code { background: #1f2937; color: #10b981; padding: 15px; border-radius: 8px; margin: 10px 0; }
    </style>
</head>
<body>";

echo "<h1>🔍 CSS/JS Asset Path Checker</h1>";

echo "<h2>📍 Path Information</h2>";
echo "<table>";
echo "<tr><td><strong>Base Directory:</strong></td><td class='path'>$basePath</td></tr>";
echo "<tr><td><strong>Base URL:</strong></td><td class='path'>$baseUrl</td></tr>";
echo "<tr><td><strong>Script Name:</strong></td><td class='path'>{$_SERVER['SCRIPT_NAME']}</td></tr>";
echo "</table>";

// Check CSS files
echo "<h2>🎨 CSS Files Check</h2>";
$cssFiles = [
    'views/assets/css/style.css',
    'views/assets/css/admin.css',
    'assets/css/style.css', // Alternative location
    'assets/css/admin.css'   // Alternative location
];

echo "<table>";
echo "<tr><th>File Path</th><th>Exists?</th><th>URL</th><th>Test Link</th></tr>";

foreach ($cssFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    $exists = file_exists($fullPath);
    $status = $exists ? "<span class='success'>✅ YES</span>" : "<span class='error'>❌ NO</span>";
    $url = $baseUrl . $file;
    $testLink = $exists ? "<a href='$url' target='_blank'>Test</a>" : "-";
    
    echo "<tr>";
    echo "<td class='path'>$file</td>";
    echo "<td>$status</td>";
    echo "<td class='path' style='font-size: 0.85em;'>$url</td>";
    echo "<td>$testLink</td>";
    echo "</tr>";
}
echo "</table>";

// Check JS files
echo "<h2>📜 JavaScript Files Check</h2>";
$jsFiles = [
    'views/assets/js/main.js',
    'assets/js/main.js' // Alternative location
];

echo "<table>";
echo "<tr><th>File Path</th><th>Exists?</th><th>URL</th><th>Test Link</th></tr>";

foreach ($jsFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    $exists = file_exists($fullPath);
    $status = $exists ? "<span class='success'>✅ YES</span>" : "<span class='error'>❌ NO</span>";
    $url = $baseUrl . $file;
    $testLink = $exists ? "<a href='$url' target='_blank'>Test</a>" : "-";
    
    echo "<tr>";
    echo "<td class='path'>$file</td>";
    echo "<td>$status</td>";
    echo "<td class='path' style='font-size: 0.85em;'>$url</td>";
    echo "<td>$testLink</td>";
    echo "</tr>";
}
echo "</table>";

// Find actual CSS files
echo "<h2>🔎 Searching for CSS files...</h2>";
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
$cssFound = [];

foreach ($iterator as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'css') {
        $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
        $relativePath = str_replace('\\', '/', $relativePath);
        $cssFound[] = $relativePath;
    }
}

if (count($cssFound) > 0) {
    echo "<p class='success'>Found " . count($cssFound) . " CSS file(s):</p>";
    echo "<ul>";
    foreach ($cssFound as $css) {
        echo "<li class='path'>$css</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='error'>❌ No CSS files found in the project!</p>";
    echo "<p><strong>Action Required:</strong> You need to copy your CSS files to the project.</p>";
}

// Instructions
echo "<h2>📋 What To Do Next</h2>";

if (count($cssFound) > 0) {
    echo "<p>✅ CSS files found! Now you need to move them to the correct location:</p>";
    echo "<ol>";
    echo "<li>Create folder: <code>views/assets/css/</code></li>";
    echo "<li>Move your CSS files there</li>";
    echo "<li>The files should be:</li>";
    echo "<ul>";
    echo "<li><span class='path'>views/assets/css/style.css</span></li>";
    echo "<li><span class='path'>views/assets/css/admin.css</span></li>";
    echo "</ul>";
    echo "<li>Refresh this page to verify</li>";
    echo "</ol>";
} else {
    echo "<p class='error'>❌ No CSS files found. Options:</p>";
    echo "<ol>";
    echo "<li><strong>Option 1:</strong> Copy CSS from your original admin.php file</li>";
    echo "<li><strong>Option 2:</strong> If CSS was in separate files, copy them to <code>views/assets/css/</code></li>";
    echo "<li><strong>Option 3:</strong> Create new CSS files based on your original design</li>";
    echo "</ol>";
}

// Show correct HTML code
echo "<h2>💻 Correct Header Code</h2>";
echo "<p>Your <code>views/partials/header.php</code> should have:</p>";
echo "<pre class='code'>";
echo htmlspecialchars('<?php
$protocol = isset($_SERVER[\'HTTPS\']) && $_SERVER[\'HTTPS\'] === \'on\' ? "https" : "http";
$host = $_SERVER[\'HTTP_HOST\'];
$scriptName = str_replace(\'admin.php\', \'\', $_SERVER[\'SCRIPT_NAME\']);
$baseUrl = $protocol . "://" . $host . $scriptName;
?>
<link rel="stylesheet" href="<?= $baseUrl ?>views/assets/css/style.css">
<link rel="stylesheet" href="<?= $baseUrl ?>views/assets/css/admin.css">');
echo "</pre>";

echo "</body></html>";
?>