<?php
// Main Admin View Template
// This file should NOT contain any business logic - only presentation
?>
<?php include __DIR__ . '/partials/admin/header.php'; ?>

<div class="container">
    <?php include __DIR__ . '/partials/admin/navigation.php'; ?>

    <div class="main">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="toggle">
                <ion-icon name="menu-outline"></ion-icon>
            </div>
            <div class="search">
                <label>
                    <input type="text" placeholder="Search here">
                    <ion-icon name="search-outline"></ion-icon>
                </label>
            </div>
            <div class="user">
                <img src="catsu.png" alt="User Profile">
            </div>
        </div>

        <!-- Display Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType ?>" style="margin: 20px; padding: 15px; border-radius: 8px; background: <?= $messageType === 'success' ? '#d1fae5' : '#fee2e2' ?>; color: <?= $messageType === 'success' ? '#065f46' : '#991b1b' ?>;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Dashboard Section -->
        <?php include __DIR__ . '/partials/admin/dashboard.php'; ?>

        <!-- User Management Section -->
        <?php include __DIR__ . '/partials/admin/users.php'; ?>

        <!-- Filing Management Section -->
        <?php include __DIR__ . '/partials/admin/filings.php'; ?>

        <!-- Voters Section -->
        <?php include __DIR__ . '/partials/admin/voters.php'; ?>

        <!-- Voting Schedule Section -->
        <?php include __DIR__ . '/partials/admin/voting_schedule.php'; ?>

        <?php include __DIR__ . '/partials/admin/filing_schedule.php'; ?>

        <!-- Reporting Section -->
        <?php include __DIR__ . '/partials/admin/reporting.php'; ?>
    </div>

    <!-- Modals -->
    <?php include __DIR__ . '/partials/admin/modals.php'; ?>
</div>

<!-- Scripts with dynamic base URL -->
<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptName = str_replace('admin.php', '', $_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . "://" . $host . $scriptName;
?>
<script src="<?= $baseUrl ?>views/assets/js/main.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>
</html>