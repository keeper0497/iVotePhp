<div class="navigation">
    <ul>
        <li>
            <a href="#">
                <span class="icon">
                    <img src="catsu.png" alt="CATSU-iVote Logo" style="width:24px; height:24px;">
                </span>
                <span class="title">CATSU-iVote Voter</span>
            </a>
        </li>
        <li style="position: relative;">
            <a href="#" id="notificationBell" title="Notifications">
                <span class="icon">
                    <ion-icon name="notifications-outline"></ion-icon>
                </span>
                <span class="title">Notifications</span>
                <?php if ($notificationCount > 0): ?>
                    <span class="notification-badge"><?= $notificationCount ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="#" id="dashboardBtn">
                <span class="icon">
                    <ion-icon name="home-outline"></ion-icon>
                </span>
                <span class="title">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="#" id="rulesBtn">
                <span class="icon">
                    <ion-icon name="book-outline"></ion-icon>
                </span>
                <span class="title">Voting Rules</span>
            </a>
        </li>
        <li>
            <a href="#" id="candidatesBtn">
                <span class="icon">
                    <ion-icon name="person-outline"></ion-icon>
                </span>
                <span class="title">Candidates Profile</span>
            </a>
        </li>
        <li>
            <a href="#" id="filingBtn">
                <span class="icon">
                    <ion-icon name="document-text-outline"></ion-icon>
                </span>
                <span class="title">Filing</span>
            </a>
        </li>
        <li>
            <a href="#" id="votesBtn">
                <span class="icon">
                    <ion-icon name="checkbox-outline"></ion-icon>
                </span>
                <span class="title">Cast Vote</span>
            </a>
        </li>
        <li>
            <a href="home.php?logout=true">
                <span class="icon">
                    <ion-icon name="log-out-outline"></ion-icon>
                </span>
                <span class="title">Log Out</span>
            </a>
        </li>
    </ul>
</div>