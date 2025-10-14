<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATSU iVote - Voter</title>
    <?php
    // Get the base URL dynamically
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = str_replace('voter.php', '', $_SERVER['SCRIPT_NAME']);
    $baseUrl = $protocol . "://" . $host . $scriptName;
    ?>
    <link rel="stylesheet" href="<?= $baseUrl ?>views/assets/css/style.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>views/assets/css/voters.css">
    <style>
        .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .message.success { background: #d4edda; color: #155724; }
        .message.error { background: #f8d7da; color: #721c24; }
        .voting-alert { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .voting-alert.active { background: #e6f3ff; border-left: 4px solid #0ea5e9; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navigation -->
        <?php include __DIR__ . '/partials/voter/voter_navigation.php'; ?>

        <div class="main">
            <!-- Top Bar -->
            <?php include __DIR__ . '/partials/voter/voter_topbar.php'; ?>

            <!-- Voting Alert -->
            <div class="voting-alert <?= $votingStatus['can_vote'] ? 'active' : '' ?>" id="votingAlert">
                <h3><?= $votingStatus['can_vote'] ? '🗳️ Voting is Active' : '⚠️ Voting is Closed' ?></h3>
                <p><?= htmlspecialchars($votingStatus['message']) ?></p>
            </div>

            <!-- Messages -->
            <?php if (!empty($message)): ?>
                <div class="message <?= htmlspecialchars($messageType) ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Dashboard Section -->
            <?php include __DIR__ . '/partials/voter/voter_dashboard.php'; ?>

            <!-- Candidates Section -->
            <?php include __DIR__ . '/partials/voter/voter_candidates.php'; ?>

            <!-- Filing Section -->
            <?php include __DIR__ . '/partials/voter/voter_filing.php'; ?>

            <!-- Votes Section -->
            <?php include __DIR__ . '/partials/voter/voter_votes.php'; ?>

            <!-- Rules Modal -->
            <?php include __DIR__ . '/partials/voter/voter_rules_modal.php'; ?>

            <!-- Notification Dropdown -->
            <div id="notificationDropdown" style="display: none; position: absolute; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.2); padding: 10px; width: 250px; z-index: 1000; right: 20px; top: 60px; border-radius: 8px;">
                <h4 style="margin: 0 0 10px 0; color: #1e293b;">Notifications</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php if ($notificationCount > 0): ?>
                        <?php foreach ($notifications as $note): ?>
                            <li style="padding: 8px 0; border-bottom: 1px solid #e5e7eb; color: #475569;">
                                <?= htmlspecialchars($note) ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li style="padding: 8px 0; color: #94a3b8; font-style: italic;">No new notifications.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= $baseUrl ?>views/assets/js/voter.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>