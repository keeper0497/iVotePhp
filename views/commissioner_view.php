<?php include __DIR__ . '/partials/commissioner/header.php'; ?>

<div class="container">
    <?php include __DIR__ . '/partials/commissioner/navigation.php'; ?>

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
                <span>Commissioner Dashboard</span>
            </div>
        </div>

        <!-- Voting Status Alert -->
        <div class="voting-alert <?= $votingStatus['can_vote'] ? 'active' : '' ?>" style="padding: 15px; margin: 20px; border-radius: 8px; <?= $votingStatus['can_vote'] ? 'background: #d1fae5; color: #065f46;' : 'background: #fee2e2; color: #991b1b;' ?>">
            <h3><?= $votingStatus['can_vote'] ? '🗳️ Voting is Active' : '⚠️ Voting is Closed' ?></h3>
            <p><?= htmlspecialchars($votingStatus['message']) ?></p>
        </div>

        <!-- Display Messages -->
        <?php if (!empty($message)): ?>
            <div class="message <?= htmlspecialchars($messageType) ?>" style="padding: 15px; margin: 20px; border-radius: 8px; <?= $messageType === 'success' ? 'background: #d1fae5; color: #065f46;' : 'background: #fee2e2; color: #991b1b;' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Dashboard Section -->
        <?php include __DIR__ . '/partials/commissioner/dashboard.php'; ?>

        <!-- Candidates Section -->
        <?php include __DIR__ . '/partials/commissioner/candidates.php'; ?>

        <!-- Results Section -->
        <?php include __DIR__ . '/partials/commissioner/results.php'; ?>
    </div>
</div>

<!-- Scripts -->
<script src="<?= $baseUrl ?>views/assets/js/commissioner.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<!-- INLINE JAVASCRIPT FOR NAVIGATION -->
<script>
console.log('Commissioner JS Loaded');

// Get all sections
const sections = {
    dashboard: document.getElementById('dashboardSection'),
    candidates: document.getElementById('candidatesSection'),
    results: document.getElementById('resultsSection')
};

console.log('Sections found:', {
    dashboard: !!sections.dashboard,
    candidates: !!sections.candidates,
    results: !!sections.results
});

// Function to show a specific section
function showSection(sectionName) {
    console.log('Showing section:', sectionName);
    
    // Hide all sections
    Object.keys(sections).forEach(key => {
        if (sections[key]) {
            sections[key].style.display = 'none';
        }
    });
    
    // Show selected section
    if (sections[sectionName]) {
        sections[sectionName].style.display = 'block';
        console.log('Section displayed:', sectionName);
    } else {
        console.error('Section not found:', sectionName);
    }
}

// Add event listeners to navigation buttons
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    const dashboardBtn = document.getElementById('dashboardBtn');
    const candidatesBtn = document.getElementById('candidatesBtn');
    const resultsBtn = document.getElementById('resultsBtn');
    
    console.log('Buttons found:', {
        dashboard: !!dashboardBtn,
        candidates: !!candidatesBtn,
        results: !!resultsBtn
    });
    
    if (dashboardBtn) {
        dashboardBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Dashboard button clicked');
            showSection('dashboard');
        });
    }
    
    if (candidatesBtn) {
        candidatesBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Candidates button clicked');
            showSection('candidates');
        });
    }
    
    if (resultsBtn) {
        resultsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Results button clicked');
            showSection('results');
        });
    }
    
    // Vote Tally - Main Organization
    const mainOrgSelect = document.getElementById('mainOrgSelect');
    if (mainOrgSelect) {
        mainOrgSelect.addEventListener('change', function() {
            const org = this.value;
            const tbody = document.getElementById('mainVoteTbody');
            
            if (!org) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #6b7280;">Select a main organization to view tally</td></tr>';
                return;
            }

            tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px;">Loading...</td></tr>';

            fetch('api/fetch_org_candidates.php?organization=' + encodeURIComponent(org))
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #dc2626;">Error: ' + data.error + '</td></tr>';
                        return;
                    }

                    if (!data.candidates || data.candidates.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #6b7280;">No candidates found</td></tr>';
                        return;
                    }

                    tbody.innerHTML = '';
                    data.candidates.forEach(c => {
                        const fullName = [c.first_name, c.middle_name, c.last_name].filter(Boolean).join(' ');
                        const row = document.createElement('tr');
                        row.style.borderBottom = '1px solid #e5e7eb';
                        row.innerHTML = `
                            <td style="padding: 12px;">${fullName}</td>
                            <td style="padding: 12px;">${c.position}</td>
                            <td style="padding: 12px; text-align: center; font-weight: bold; color: #4f46e5;">${c.total_votes || 0}</td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(err => {
                    tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #dc2626;">Error loading data</td></tr>';
                    console.error(err);
                });
        });
    }

    // Vote Tally - Sub Organization
    const subOrgSelect = document.getElementById('subOrgSelect');
    if (subOrgSelect) {
        subOrgSelect.addEventListener('change', function() {
            const org = this.value;
            const tbody = document.getElementById('subVoteTbody');
            
            if (!org) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #6b7280;">Select a sub organization to view tally</td></tr>';
                return;
            }

            tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px;">Loading...</td></tr>';

            fetch('api/fetch_org_candidates.php?organization=' + encodeURIComponent(org))
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #dc2626;">Error: ' + data.error + '</td></tr>';
                        return;
                    }

                    if (!data.candidates || data.candidates.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #6b7280;">No candidates found</td></tr>';
                        return;
                    }

                    tbody.innerHTML = '';
                    data.candidates.forEach(c => {
                        const fullName = [c.first_name, c.middle_name, c.last_name].filter(Boolean).join(' ');
                        const row = document.createElement('tr');
                        row.style.borderBottom = '1px solid #e5e7eb';
                        row.innerHTML = `
                            <td style="padding: 12px;">${fullName}</td>
                            <td style="padding: 12px;">${c.position || 'Representative'}</td>
                            <td style="padding: 12px; text-align: center; font-weight: bold; color: #4f46e5;">${c.total_votes || 0}</td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(err => {
                    tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #dc2626;">Error loading data</td></tr>';
                    console.error(err);
                });
        });
    }
    
    // Sidebar toggle
    const toggle = document.querySelector('.toggle');
    const navigation = document.querySelector('.navigation');
    const main = document.querySelector('.main');
    
    if (toggle) {
        toggle.addEventListener('click', function() {
            navigation?.classList.toggle('active');
            main?.classList.toggle('active');
        });
    }
    
    // Show dashboard by default
    showSection('dashboard');
    console.log('Initialized - Dashboard shown by default');
});
</script>

</body>
</html>
