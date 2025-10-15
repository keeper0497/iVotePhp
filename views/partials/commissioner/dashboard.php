<?php
// Check user role - commissioners and admins can always view
$isPrivilegedUser = ($_SESSION['role'] === 'commissioner' || $_SESSION['role'] === 'admin');
$canViewTally = !$votingStatus['can_vote'] || $isPrivilegedUser;
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

    <!-- Vote Tally Section - Always visible for commissioners -->
    <div class="vote-tally-section" style="margin-top: 40px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #4f46e5; margin-bottom: 25px;">
            <ion-icon name="bar-chart" style="vertical-align: middle;"></ion-icon>
            Real-Time Vote Tally
        </h2>

        <!-- Voting Status Alert -->
        <?php if ($votingStatus['can_vote']): ?>
            <div style="background: #dbeafe; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <ion-icon name="information-circle" style="font-size: 1.5rem; color: #1e40af;"></ion-icon>
                    <div>
                        <p style="color: #1e3a8a; margin: 0; font-weight: 600;">
                            🔴 Voting is currently <strong>OPEN</strong> - Results are updating in real-time
                        </p>
                        <p style="color: #1e40af; margin: 5px 0 0 0; font-size: 0.9rem;">
                            Note: As a commissioner, you can view live results that are hidden from voters.
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

        <!-- Main Organization Vote Tally -->
        <h3 style="color: #1f2937; margin-top: 30px;">Main Organization Vote Tally</h3>
        <select id="mainOrgSelect" style="width: 100%; max-width: 400px; padding: 12px; border: 2px solid #cbd5e1; border-radius: 8px; margin-bottom: 20px; cursor: pointer;">
            <option value="">Select Main Organization</option>
            <option value="USC">USC (University Student Council)</option>
            <option value="CSC">CSC (College Student Council)</option>
        </select>
        <div style="overflow-x: auto;">
            <table class="results-table" style="width: 100%; border-collapse: collapse; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <thead>
                    <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Candidate Name</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Position</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600;">Total Votes</th>
                    </tr>
                </thead>
                <tbody id="mainVoteTbody">
                    <tr><td colspan="3" style="text-align: center; padding: 20px; color: #6b7280;">Select a main organization to view tally</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Sub Organization Vote Tally -->
        <h3 style="color: #1f2937; margin-top: 40px;">Sub Organization Vote Tally</h3>
        <select id="subOrgSelect" style="width: 100%; max-width: 400px; padding: 12px; border: 2px solid #cbd5e1; border-radius: 8px; margin-bottom: 20px; cursor: pointer;">
            <option value="">Select Sub Organization</option>
            <option value="ACCESS">ACCESS</option>
            <option value="ASITS">ASITS</option>
            <option value="BSEMC PromtPT">BSEMC PromtPT</option>
            <option value="ISSO">ISSO</option>
            <option value="LISAUX">LISAUX</option>
            <option value="CICT-womens club">CICT-womens club</option>
        </select>
        <div style="overflow-x: auto;">
            <table class="results-table" style="width: 100%; border-collapse: collapse; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <thead>
                    <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Candidate Name</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Position</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600;">Total Votes</th>
                    </tr>
                </thead>
                <tbody id="subVoteTbody">
                    <tr><td colspan="3" style="text-align: center; padding: 20px; color: #6b7280;">Select a sub organization to view tally</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>