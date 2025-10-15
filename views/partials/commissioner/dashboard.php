<?php
// Check if voting is closed for vote tally visibility
$canViewTally = !$votingStatus['can_vote'];
?>

<div id="dashboardSection">
    <div class="cardBox">
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['totalCandidates']) ?></div>
                <div class="cardName">Total Candidates</div>
            </div>
            <div class="iconBx">
                <ion-icon name="people-outline"></ion-icon>
            </div>
        </div>

        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['pendingCandidates']) ?></div>
                <div class="cardName">Pending Approvals</div>
            </div>
            <div class="iconBx">
                <ion-icon name="time-outline"></ion-icon>
            </div>
        </div>

        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['acceptedCandidates']) ?></div>
                <div class="cardName">Accepted Candidates</div>
            </div>
            <div class="iconBx">
                <ion-icon name="checkmark-circle-outline"></ion-icon>
            </div>
        </div>

        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['totalVotes']) ?></div>
                <div class="cardName">Total Votes Cast</div>
            </div>
            <div class="iconBx">
                <ion-icon name="bar-chart-outline"></ion-icon>
            </div>
        </div>
    </div>

    <!-- Vote Tally Section -->
    <?php if ($canViewTally): ?>
        <div class="vote-tally-section" style="margin-top: 40px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="background: #d1fae5; border-left: 4px solid #059669; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <p style="color: #065f46; margin: 0; font-weight: bold;">
                    ✓ Voting is closed. Vote tallies are now available.
                </p>
            </div>

            <h3>Main Organization Vote Tally</h3>
            <select id="mainOrgSelect" style="width: 100%; max-width: 400px; padding: 12px; border: 2px solid #cbd5e1; border-radius: 8px; margin-bottom: 20px;">
                <option value="">Select Main Organization</option>
                <option value="USC">USC (University Student Council)</option>
                <option value="CSC">CSC (College Student Council)</option>
            </select>
            <table class="results-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Candidate Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Position</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e5e7eb;">Total Votes</th>
                    </tr>
                </thead>
                <tbody id="mainVoteTbody">
                    <tr><td colspan="3" style="text-align: center; padding: 20px; color: #6b7280;">Select a main organization to view tally</td></tr>
                </tbody>
            </table>
        </div>

        <div class="vote-tally-section" style="margin-top: 40px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3>Sub Organization Vote Tally</h3>
            <select id="subOrgSelect" style="width: 100%; max-width: 400px; padding: 12px; border: 2px solid #cbd5e1; border-radius: 8px; margin-bottom: 20px;">
                <option value="">Select Sub Organization</option>
                <option value="ACCESS">ACCESS</option>
                <option value="ASITS">ASITS</option>
                <option value="BSEMC PromtPT">BSEMC PromtPT</option>
                <option value="ISSO">ISSO</option>
                <option value="LISAUX">LISAUX</option>
                <option value="CICT-womens club">CICT-womens club</option>
            </select>
            <table class="results-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Candidate Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Position</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e5e7eb;">Total Votes</th>
                    </tr>
                </thead>
                <tbody id="subVoteTbody">
                    <tr><td colspan="3" style="text-align: center; padding: 20px; color: #6b7280;">Select a sub organization to view tally</td></tr>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="max-width: 800px; margin: 40px auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 25px; border-radius: 8px;">
                <div style="text-align: center;">
                    <div style="font-size: 4rem; color: #f59e0b; margin-bottom: 15px;">🔒</div>
                    <h3 style="color: #92400e; margin: 0 0 10px 0;">Vote Tally Not Available</h3>
                    <p style="color: #78350f; margin: 0;">Results will be visible after voting closes.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>