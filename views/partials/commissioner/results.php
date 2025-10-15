<div id="resultsSection" style="display:none; padding:20px;">
    <div class="results-section">
        <h2>Results Summary</h2>
        <p style="text-align: center; margin-bottom: 30px; color: #6b7280;">
            Election results and vote tallies
        </p>
        
        <?php if ($dashboardStats['totalVotes'] > 0 && $canViewTally): ?>
            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
                <h3 style="color: #4f46e5; margin-bottom: 20px;">Overall Statistics</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div style="text-align: center; padding: 20px; background: #f0f9ff; border-radius: 8px;">
                        <div style="font-size: 2.5rem; font-weight: bold; color: #0284c7;"><?= $dashboardStats['totalVotes'] ?></div>
                        <div style="color: #0c4a6e; font-weight: 600;">Total Voters</div>
                    </div>
                    <div style="text-align: center; padding: 20px; background: #f0fdf4; border-radius: 8px;">
                        <div style="font-size: 2.5rem; font-weight: bold; color: #059669;"><?= $dashboardStats['acceptedCandidates'] ?></div>
                        <div style="color: #065f46; font-weight: 600;">Total Candidates</div>
                    </div>
                </div>
            </div>

            <!-- Detailed Results Tables Here -->
            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="color: #4f46e5; margin-bottom: 20px;">Detailed Results</h3>
                <p style="color: #6b7280;">Select an organization from the Dashboard tab to view detailed vote tallies.</p>
            </div>
        <?php elseif (!$canViewTally): ?>
            <div style="text-align: center; padding: 50px; background: #fef3c7; border-radius: 12px;">
                <div style="font-size: 4rem; color: #f59e0b; margin-bottom: 20px;">🔒</div>
                <h3 style="color: #92400e;">Results Not Available</h3>
                <p style="color: #78350f; margin-top: 10px;">
                    Results will be visible after voting closes.
                </p>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; background: #f9fafb; border-radius: 12px;">
                <h3 style="color: #6b7280;">No Votes Cast Yet</h3>
                <p style="color: #9ca3af;">Voting results will appear here once votes are submitted.</p>
            </div>
        <?php endif; ?>
    </div>
</div>