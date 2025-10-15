<div id="dashboardSection">
    <div class="cardBox">
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['total_voters']) ?></div>
                <div class="cardName">Total registered voters</div>
            </div>
            <div class="iconBx">
                <ion-icon name="people-outline"></ion-icon>
            </div>
        </div>
        
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['total_candidates']) ?></div>
                <div class="cardName">Total of candidates</div>
            </div>
            <div class="iconBx">
                <ion-icon name="people-outline"></ion-icon>
            </div>
        </div>
        
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['students_voted']) ?></div>
                <div class="cardName">Students who voted</div>
            </div>
            <div class="iconBx">
                <ion-icon name="people-outline"></ion-icon>
            </div>
        </div>
        
        <div class="card">
            <div>
                <div class="numbers"><?= $dashboardStats['voting_percentage'] ?>%</div>
                <div class="cardName">Voting Percentage</div>
            </div>
            <div class="iconBx">
                <ion-icon name="analytics-outline"></ion-icon>
            </div>
        </div>
    </div>

    <!-- Vote Tally Section -->
    <div style="max-width: 800px; margin: 40px auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #4f46e5; margin-bottom: 30px;">
            <ion-icon name="bar-chart" style="vertical-align: middle;"></ion-icon>
            Real-Time Vote Tally (Admin View)
        </h2>
        
        <!-- Voting Status Alert -->
        <?php 
        $isVotingClosed = ($votingSchedule && $votingSchedule['status'] === 'closed');
        ?>
        
        <?php if (!$isVotingClosed): ?>
            <div style="background: #dbeafe; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <ion-icon name="information-circle" style="font-size: 1.5rem; color: #1e40af;"></ion-icon>
                    <div>
                        <p style="color: #1e3a8a; margin: 0; font-weight: 600;">
                            🔴 Voting is currently <strong>OPEN</strong> - Results are updating in real-time
                        </p>
                        <p style="color: #1e40af; margin: 5px 0 0 0; font-size: 0.9rem;">
                            Note: These results are only visible to administrators.
                        </p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="background: #d1fae5; border-left: 4px solid #059669; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <ion-icon name="checkmark-circle" style="font-size: 1.5rem; color: #059669;"></ion-icon>
                    <p style="color: #065f46; margin: 0; font-weight: bold;">
                        ✓ Voting is <strong>CLOSED</strong> - Final results are displayed below
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Organization Selector -->
        <div style="max-width: 400px; margin: 0 auto 30px;">
            <label for="adminOrgSelect" style="font-weight: bold; font-size: 1.1rem; display: block; margin-bottom: 10px; color: #1f2937;">
                Select Organization:
            </label>
            <select id="adminOrgSelect" style="width: 100%; padding: 12px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: border-color 0.3s;">
                <option value="">-- Select Organization --</option>
                <optgroup label="Main Organizations">
                    <option value="USC">USC (University Student Council)</option>
                    <option value="CSC">CSC (College Student Council)</option>
                </optgroup>
                <optgroup label="Sub Organizations">
                    <option value="ACCESS">ACCESS</option>
                    <option value="ASITS">ASITS</option>
                    <option value="BSEMC PromtPT">BSEMC PromtPT</option>
                    <option value="ISSO">ISSO</option>
                    <option value="LISAUX">LISAUX</option>
                    <option value="CICT-womens club">CICT-womens club</option>
                </optgroup>
            </select>
        </div>

        <!-- Auto-refresh toggle for real-time updates -->
        <?php if (!$isVotingClosed): ?>
        <div style="text-align: center; margin-bottom: 20px;">
            <label style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; background: #f3f4f6; padding: 10px 20px; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                <input type="checkbox" id="autoRefreshToggle" style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-weight: 600; color: #374151;">
                    <ion-icon name="refresh" style="vertical-align: middle;"></ion-icon>
                    Auto-refresh every 10 seconds
                </span>
            </label>
        </div>
        <?php endif; ?>

        <!-- Candidates Tally Container -->
        <div id="adminTallyContainer">
            <p style="text-align: center; color: #6b7280; font-style: italic; padding: 40px;">
                <ion-icon name="stats-chart" style="font-size: 3rem; color: #9ca3af; display: block; margin: 0 auto 15px;"></ion-icon>
                Select an organization to view vote tally
            </p>
        </div>
    </div>
</div>

<script>
// Vote Tally JavaScript with Status Check
document.getElementById('adminOrgSelect')?.addEventListener('change', function() {
    const org = this.value;
    const container = document.getElementById('adminTallyContainer');
    
    if (!org) {
        container.innerHTML = '<p style="text-align: center; color: #6b7280; font-style: italic;">Select an organization to view vote tally</p>';
        return;
    }

    container.innerHTML = '<p style="text-align: center;">Loading vote tally...</p>';

    fetch('api/fetch_org_candidates.php?organization=' + encodeURIComponent(org))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                // Check if error is due to voting being open
                if (data.voting_status === 'open') {
                    container.innerHTML = `
                        <div style="text-align: center; padding: 40px; background: #fef3c7; border-radius: 8px;">
                            <ion-icon name="time" style="font-size: 3rem; color: #f59e0b;"></ion-icon>
                            <h3 style="color: #92400e; margin: 20px 0 10px 0;">Results Not Yet Available</h3>
                            <p style="color: #78350f;">${data.message}</p>
                        </div>
                    `;
                } else {
                    container.innerHTML = '<p style="color:red; text-align: center;">Error: ' + data.error + '</p>';
                }
                return;
            }
            
            if (!data.candidates || data.candidates.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #6b7280;">No accepted candidates found for ' + org + '.</p>';
                return;
            }

            const positionOrder = [
                'President', 'Vice President', 'Executive Secretary', 'Finance Secretary',
                'Budget Secretary', 'Auditor', 'Public Information Secretary',
                'Property Custodian', 'Senators', 'Legislators', 'Year Representative',
                'Representative', 'Other'
            ];

            const sortedCandidates = data.candidates.sort((a, b) => {
                const posA = positionOrder.indexOf(a.position) !== -1 ? positionOrder.indexOf(a.position) : positionOrder.length;
                const posB = positionOrder.indexOf(b.position) !== -1 ? positionOrder.indexOf(b.position) : positionOrder.length;
                return posA - posB;
            });

            let html = `<h3 style="text-align: center; margin-bottom: 20px; color: #1f2937;">Vote Tally for ${org}</h3>`;
            html += `<table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f3f4f6;">
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Candidate Name</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Position</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e5e7eb;">Total Votes</th>
                            </tr>
                        </thead>
                        <tbody>`;

            sortedCandidates.forEach(c => {
                const fullName = [c.first_name, c.middle_name, c.last_name].filter(Boolean).join(' ');
                const position = c.position || 'Representative';
                html += `
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 12px;"><strong>${fullName}</strong></td>
                        <td style="padding: 12px;">${position}</td>
                        <td style="padding: 12px; text-align: center; font-weight: bold; color: #4f46e5;">${c.total_votes || 0}</td>
                    </tr>
                `;
            });

            html += `</tbody></table>`;
            
            const totalVotes = sortedCandidates.reduce((sum, c) => sum + (parseInt(c.total_votes) || 0), 0);
            html += `<div style="margin-top: 20px; padding: 15px; background: #f0f9ff; border-left: 4px solid #0ea5e9; border-radius: 6px;">`;
            html += `<p style="margin: 0; color: #0c4a6e;"><strong>Total Votes Cast for ${org}:</strong> ${totalVotes}</p></div>`;

            container.innerHTML = html;
        })
        .catch(err => {
            container.innerHTML = '<p style="color: red; text-align: center;">Error loading vote tally.</p>';
            console.error(err);
        });
});
</script>