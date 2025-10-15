<?php
// Check if voting is closed (for vote tally visibility)
$isVotingClosed = !$votingStatus['can_vote'];
?>

<div id="dashboardSection">
    <div class="cardBox">
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['totalVoters']) ?></div>
                <div class="cardName">Total Registered Voters</div>
            </div>
            <div class="iconBx">
                <ion-icon name="people-outline"></ion-icon>
            </div>
        </div>
        
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['voted']) ?></div>
                <div class="cardName">Students Who Voted</div>
            </div>
            <div class="iconBx">
                <ion-icon name="people-outline"></ion-icon>
            </div>
        </div>
        
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['notVoted']) ?></div>
                <div class="cardName">Students Who Have Not Voted</div>
            </div>
            <div class="iconBx">
                <ion-icon name="people-outline"></ion-icon>
            </div>
        </div>
        
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['totalCandidates']) ?></div>
                <div class="cardName">Total Candidates</div>
            </div>
            <div class="iconBx">
                <ion-icon name="people-outline"></ion-icon>
            </div>
        </div>
    </div>

    <!-- Vote Tally Section - ONLY VISIBLE WHEN VOTING IS CLOSED -->
    <?php if ($isVotingClosed): ?>
        <!-- Voting Closed - Show Results -->
        <div style="max-width: 800px; margin: 40px auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <div style="background: #d1fae5; border-left: 4px solid #059669; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <ion-icon name="checkmark-circle" style="font-size: 1.5rem; color: #059669;"></ion-icon>
                    <p style="color: #065f46; margin: 0; font-weight: bold;">
                        ✓ Voting is closed. Results are now available.
                    </p>
                </div>
            </div>

            <h2 style="text-align: center; color: #4f46e5; margin-bottom: 30px;">Vote Tally by Organization</h2>
            
            <div style="max-width: 400px; margin: 0 auto 30px;">
                <label for="orgSelect" style="font-weight: bold; font-size: 1.1rem; display: block; margin-bottom: 10px;">
                    Select Organization:
                </label>
                <select id="orgSelect" style="width: 100%; padding: 12px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 1rem;">
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

            <div id="orgCandidatesContainer">
                <p style="text-align: center; color: #6b7280; font-style: italic;">
                    Select an organization to view vote tally
                </p>
            </div>
        </div>
    <?php else: ?>
        <!-- Voting Active - Show Locked Message -->
        <div style="max-width: 800px; margin: 40px auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 25px; border-radius: 8px;">
                <div style="text-align: center;">
                    <div style="font-size: 4rem; color: #f59e0b; margin-bottom: 15px;">🔒</div>
                    <h3 style="color: #92400e; margin: 0 0 10px 0; font-size: 1.3rem;">Vote Tally Not Available</h3>
                    <p style="color: #78350f; margin: 0; line-height: 1.6; font-size: 1rem;">
                        Results will be visible after voting closes. This prevents real-time vote counts from influencing voters and ensures a fair election process.
                    </p>
                    <div style="margin-top: 20px; padding: 15px; background: rgba(245, 158, 11, 0.1); border-radius: 6px;">
                        <p style="color: #92400e; margin: 0; font-weight: bold;">
                            Current Status: <span style="color: #dc2626;">Voting is Active</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Vote Tally JavaScript - Only active when voting is closed
<?php if ($isVotingClosed): ?>
document.getElementById('orgSelect')?.addEventListener('change', function() {
    const org = this.value;
    const container = document.getElementById('orgCandidatesContainer');
    
    if (!org) {
        container.innerHTML = '<p style="text-align: center; color: #6b7280; font-style: italic;">Select an organization to view vote tally</p>';
        return;
    }

    container.innerHTML = '<p style="text-align: center; color: #6b7280;">Loading vote tally...</p>';

    fetch('api/fetch_org_candidates.php?organization=' + encodeURIComponent(org))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                if (data.voting_status === 'open') {
                    container.innerHTML = `
                        <div style="text-align: center; padding: 30px; background: #fef3c7; border-radius: 8px;">
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
                        <td style="padding: 12px; text-align: center; font-weight: bold; color: #4f46e5; font-size: 1.1rem;">${c.total_votes || 0}</td>
                    </tr>
                `;
            });

            html += `</tbody></table>`;
            
            const totalVotes = sortedCandidates.reduce((sum, c) => sum + (parseInt(c.total_votes) || 0), 0);
            html += `
                <div style="margin-top: 20px; padding: 15px; background: #f0f9ff; border-left: 4px solid #0ea5e9; border-radius: 6px;">
                    <p style="margin: 0; color: #0c4a6e;">
                        <strong>Total Votes Cast for ${org}:</strong> 
                        <span style="font-size: 1.2rem; font-weight: bold; color: #4f46e5;">${totalVotes}</span>
                    </p>
                </div>
            `;

            container.innerHTML = html;
        })
        .catch(err => {
            container.innerHTML = '<p style="color: red; text-align: center;">Error loading vote tally. Please try again.</p>';
            console.error('Fetch error:', err);
        });
});
<?php else: ?>
// Voting is active - tally viewing disabled
console.log('Vote tally is locked while voting is active');
<?php endif; ?>
</script>