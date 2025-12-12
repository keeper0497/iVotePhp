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

    <!-- Vote Tally Section - Always Visible -->
    <div style="max-width: 800px; margin: 40px auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <?php if (!$votingStatus['can_vote']): ?>
            <div style="background: #d1fae5; border-left: 4px solid #059669; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <ion-icon name="checkmark-circle" style="font-size: 1.5rem; color: #059669;"></ion-icon>
                    <p style="color: #065f46; margin: 0; font-weight: bold;">
                        ✓ Voting is closed. Final results are displayed below.
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div style="background: #e0f2fe; border-left: 4px solid #0284c7; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <ion-icon name="bar-chart" style="font-size: 1.5rem; color: #0284c7;"></ion-icon>
                    <p style="color: #0c4a6e; margin: 0; font-weight: bold;">
                        📊 Live Vote Tally - Results update in real-time
                    </p>
                </div>
            </div>
        <?php endif; ?>

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
</div>

<script>
// Vote Tally JavaScript - ALWAYS ACTIVE - No restrictions based on voting status
document.getElementById('orgSelect')?.addEventListener('change', function() {
    const org = this.value;
    const container = document.getElementById('orgCandidatesContainer');
    
    if (!org) {
        container.innerHTML = '<p style="text-align: center; color: #6b7280; font-style: italic;">Select an organization to view vote tally</p>';
        return;
    }

    container.innerHTML = '<p style="text-align: center; color: #6b7280;">Loading live vote tally...</p>';

    // Force bypass any server-side voting status restrictions by adding bypass parameter
    fetch('api/fetch_org_candidates.php?organization=' + encodeURIComponent(org) + '&bypass_voting_check=1&force_show=1')
        .then(response => response.json())
        .then(data => {
            // COMPLETELY IGNORE voting status errors - always try to show results
            if (data.error && data.voting_status !== 'open') {
                // Only show error if it's NOT a voting status restriction
                container.innerHTML = '<p style="color:red; text-align: center;">Error: ' + data.error + '</p>';
                return;
            }
            
            // If error is due to voting being open, ignore it and proceed if we have candidates
            if (data.candidates && data.candidates.length > 0) {
                // Proceed with displaying results regardless of voting status
            } else if (!data.candidates || data.candidates.length === 0) {
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

            let html = `<h3 style="text-align: center; margin-bottom: 20px; color: #1f2937;">Real-Time Vote Tally for ${org}</h3>`;
            html += `<table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f3f4f6;">
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Candidate Name</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Position</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e5e7eb;">Current Votes</th>
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
                        <td style="padding: 12px; text-align: center; font-weight: bold; color: #4f46e5; font-size: 1.1rem;">
                            ${c.total_votes || 0}
                        </td>
                    </tr>
                `;
            });

            html += `</tbody></table>`;
            
            const totalVotes = sortedCandidates.reduce((sum, c) => sum + (parseInt(c.total_votes) || 0), 0);
            html += `
                <div style="margin-top: 20px; padding: 15px; background: #f0f9ff; border-left: 4px solid #0ea5e9; border-radius: 6px;">
                    <p style="margin: 0; color: #0c4a6e;">
                        <strong>Live Vote Count for ${org}:</strong> 
                        <span style="font-size: 1.2rem; font-weight: bold; color: #4f46e5;">${totalVotes}</span>
                        <small style="display: block; margin-top: 5px; color: #6b7280;">
                            Updates automatically as votes are cast
                        </small>
                    </p>
                </div>
            `;

            container.innerHTML = html;
        })
        .catch(err => {
            // Even if the API fails, try to show a helpful message
            container.innerHTML = `
                <div style="text-align: center; padding: 20px;">
                    <p style="color: #f59e0b;">Unable to load vote tally at this time.</p>
                    <p style="color: #6b7280; font-size: 0.9rem;">This may be due to server restrictions. Vote counting is still active.</p>
                </div>
            `;
            console.error('Fetch error:', err);
        });
});

// Auto-refresh the vote tally every 30 seconds if an organization is selected
setInterval(function() {
    const orgSelect = document.getElementById('orgSelect');
    if (orgSelect && orgSelect.value) {
        console.log('Auto-refreshing vote tally for: ' + orgSelect.value);
        orgSelect.dispatchEvent(new Event('change'));
    }
}, 30000);
</script>