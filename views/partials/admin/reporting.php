<?php
// Check if voting is closed to allow viewing reports
$canViewReports = ($votingSchedule && $votingSchedule['status'] === 'closed');
?>

<div class="details" id="reportingSection" style="display:none;">
    <div class="recentOrders">
        <div class="cardHeader">
            <h2>Reporting Module</h2>
            <div style="display: flex; gap: 10px;" id="reportActions">
                <?php if (!empty($reportData) && $canViewReports): ?>
                <button class="btn" onclick="window.print()">Print Report</button>
                <button class="btn" onclick="openModal('exportModal')">Export Report</button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Voting Status Alert -->
        <?php if (!$canViewReports): ?>
            <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <ion-icon name="lock-closed" style="font-size: 2.5rem; color: #f59e0b;"></ion-icon>
                    <div>
                        <h3 style="color: #92400e; margin: 0 0 8px 0; font-size: 1.2rem;">⚠️ Reports Locked During Active Voting</h3>
                        <p style="color: #78350f; margin: 0; line-height: 1.6;">
                            Reports and analytics will be available after voting closes to ensure result integrity 
                            and prevent real-time vote counts from influencing voters.
                        </p>
                        <p style="color: #78350f; margin: 10px 0 0 0; font-weight: bold;">
                            Current Status: <span style="color: #dc2626;">Voting is Open</span>
                        </p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="background: #d1fae5; border-left: 4px solid #059669; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <ion-icon name="checkmark-circle" style="font-size: 1.8rem; color: #059669;"></ion-icon>
                    <p style="color: #065f46; margin: 0; font-weight: bold;">
                        ✓ Voting is closed. All reports are now available.
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Report Error Message -->
        <?php if (isset($reportData['error'])): ?>
            <div style="background: #fee2e2; border-left: 4px solid #dc2626; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <ion-icon name="alert-circle" style="font-size: 2rem; color: #dc2626;"></ion-icon>
                    <div>
                        <h4 style="color: #991b1b; margin: 0 0 5px 0;">Cannot Generate Report</h4>
                        <p style="color: #991b1b; margin: 0;"><?= htmlspecialchars($reportData['error']) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="report-generator" style="<?= !$canViewReports ? 'opacity: 0.5; pointer-events: none; user-select: none;' : '' ?>">
            <h3>Generate Reports</h3>
            <form method="POST" class="report-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="report_type"><strong>Select Report Type:</strong></label>
                        <select name="report_type" id="report_type" required <?= !$canViewReports ? 'disabled' : '' ?>>
                            <option value="">-- Select Report Type --</option>
                            <option value="voters_summary" <?= $reportType == 'voters_summary' ? 'selected' : '' ?>>Voters Summary Report</option>
                            <option value="candidates_summary" <?= $reportType == 'candidates_summary' ? 'selected' : '' ?>>Candidates Summary Report</option>
                            <option value="voting_activity" <?= $reportType == 'voting_activity' ? 'selected' : '' ?>>Voting Activity Report</option>
                            <option value="filing_status" <?= $reportType == 'filing_status' ? 'selected' : '' ?>>Filing Status Report</option>
                            <option value="complete_election" <?= $reportType == 'complete_election' ? 'selected' : '' ?>>Complete Election Report</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="generateReport" class="btn generate-btn" <?= !$canViewReports ? 'disabled' : '' ?>>
                            <?= $canViewReports ? 'Generate Report' : '🔒 Locked' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Report Display -->
        <?php if (!empty($reportData) && !isset($reportData['error']) && $reportType !== 'complete_election' && $canViewReports): ?>
        <div class="report-content printable-content">
            <div class="report-header">
                <h2><?= htmlspecialchars($reportTitle) ?></h2>
                <p>Generated on: <?= date('F j, Y g:i A') ?></p>
                <p>CATSU iVote System</p>
            </div>
            
            <div class="report-data">
                <?php if ($reportType === 'voters_summary'): ?>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Email</th>
                                <th>College</th>
                                <th>Vote Status</th>
                                <th>Voted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['student_id']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['college'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="vote-status <?= $row['vote_status'] === 'Voted' ? 'voted' : 'not-voted' ?>" 
                                          style="padding: 5px 10px; border-radius: 5px; font-weight: bold; <?= $row['vote_status'] === 'Voted' ? 'background: #d1fae5; color: #065f46;' : 'background: #fee2e2; color: #991b1b;' ?>">
                                        <?= $row['vote_status'] ?>
                                    </span>
                                </td>
                                <td><?= $row['voted_at'] ? date('M j, Y g:i A', strtotime($row['voted_at'])) : 'N/A' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="report-summary" style="margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 8px;">
                        <?php
                        $totalVotersReport = count($reportData);
                        $votedCount = count(array_filter($reportData, function($row) { return $row['vote_status'] === 'Voted'; }));
                        $votingPercentage = $totalVotersReport > 0 ? round(($votedCount / $totalVotersReport) * 100, 2) : 0;
                        ?>
                        <h4>Summary Statistics:</h4>
                        <p>Total Registered Voters: <strong><?= $totalVotersReport ?></strong></p>
                        <p>Voters Who Voted: <strong style="color: #059669;"><?= $votedCount ?></strong></p>
                        <p>Voters Who Haven't Voted: <strong style="color: #dc2626;"><?= $totalVotersReport - $votedCount ?></strong></p>
                        <p>Voting Percentage: <strong style="color: #4f46e5;"><?= $votingPercentage ?>%</strong></p>
                    </div>
                
                <?php elseif ($reportType === 'candidates_summary'): ?>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Organization Type</th>
                                <th>Candidate Name</th>
                                <th>Organization</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Filing Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['org_type']) ?></td>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><?= htmlspecialchars($row['organization']) ?></td>
                                <td><?= htmlspecialchars($row['position']) ?></td>
                                <td><span class="status <?= htmlspecialchars($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                                <td><?= date('M j, Y', strtotime($row['filing_date'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                
                <?php elseif ($reportType === 'voting_activity'): ?>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Hour</th>
                                <th>Unique Voters</th>
                                <th>Total Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $row): ?>
                            <tr>
                                <td><?= date('M j, Y', strtotime($row['vote_date'])) ?></td>
                                <td><?= str_pad($row['vote_hour'], 2, '0', STR_PAD_LEFT) ?>:00</td>
                                <td><?= $row['unique_voters'] ?></td>
                                <td><?= $row['total_votes'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                
                <?php elseif ($reportType === 'filing_status'): ?>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Organization Type</th>
                                <th>Status</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['org_type']) ?></td>
                                <td><span class="status <?= htmlspecialchars($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                                <td><?= $row['count'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <?php elseif (!empty($reportData) && !isset($reportData['error']) && $reportType === 'complete_election' && $canViewReports): ?>
        <!-- Complete Election Report -->
        <div class="report-content printable-content">
            <div class="report-header">
                <h2>Complete Election Report</h2>
                <p>Generated on: <?= date('F j, Y g:i A') ?></p>
                <p>CATSU iVote System</p>
            </div>
            
            <div class="report-sections">
                <!-- Voters Statistics Section -->
                <div class="report-section">
                    <h3>1. Voters Statistics</h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number"><?= $reportData['voters_stats']['total_voters'] ?></div>
                            <div class="stat-label">Total Registered Voters</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $reportData['voters_stats']['voted_count'] ?></div>
                            <div class="stat-label">Voters Who Voted</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">
                                <?= $reportData['voters_stats']['total_voters'] > 0 ? round(($reportData['voters_stats']['voted_count'] / $reportData['voters_stats']['total_voters']) * 100, 1) : 0 ?>%
                            </div>
                            <div class="stat-label">Voting Turnout</div>
                        </div>
                    </div>
                </div>

                <!-- Candidates Statistics Section -->
                <div class="report-section">
                    <h3>2. Candidates Statistics</h3>
                    <div class="candidates-stats">
                        <h4>Main Organization Candidates:</h4>
                        <table class="mini-table">
                            <thead><tr><th>Status</th><th>Count</th></tr></thead>
                            <tbody>
                                <?php foreach ($reportData['main_candidates'] as $stat): ?>
                                <tr>
                                    <td><span class="status <?= $stat['status'] ?>"><?= $stat['status'] ?></span></td>
                                    <td><?= $stat['count'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <h4>Sub Organization Candidates:</h4>
                        <table class="mini-table">
                            <thead><tr><th>Status</th><th>Count</th></tr></thead>
                            <tbody>
                                <?php foreach ($reportData['sub_candidates'] as $stat): ?>
                                <tr>
                                    <td><span class="status <?= $stat['status'] ?>"><?= $stat['status'] ?></span></td>
                                    <td><?= $stat['count'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Voting Schedule Section -->
                <?php if ($reportData['schedule']): ?>
                <div class="report-section">
                    <h3>3. Voting Schedule</h3>
                    <table class="info-table">
                        <tr><td><strong>Status:</strong></td><td><span class="voting-status <?= $reportData['schedule']['status'] ?>"><?= strtoupper($reportData['schedule']['status']) ?></span></td></tr>
                        <tr><td><strong>Start Date:</strong></td><td><?= date('M j, Y g:i A', strtotime($reportData['schedule']['start_date'])) ?></td></tr>
                        <tr><td><strong>End Date:</strong></td><td><?= date('M j, Y g:i A', strtotime($reportData['schedule']['end_date'])) ?></td></tr>
                        <tr><td><strong>Description:</strong></td><td><?= htmlspecialchars($reportData['schedule']['description']) ?: 'No description' ?></td></tr>
                    </table>
                </div>
                <?php endif; ?>

                <!-- Daily Voting Activity Section -->
                <?php if (!empty($reportData['daily_activity'])): ?>
                <div class="report-section">
                    <h3>4. Daily Voting Activity</h3>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Unique Voters</th>
                                <th>Total Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData['daily_activity'] as $activity): ?>
                            <tr>
                                <td><?= date('M j, Y', strtotime($activity['vote_date'])) ?></td>
                                <td><?= $activity['unique_voters'] ?></td>
                                <td><?= $activity['total_votes'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (empty($reportData) && !isset($_POST['generateReport']) && $canViewReports): ?>
        <div class="no-report" style="text-align: center; padding: 40px; color: #6b7280;">
            <ion-icon name="document-text-outline" style="font-size: 4rem;"></ion-icon>
            <p style="font-size: 1.1rem; margin-top: 20px;">Select a report type and click "Generate Report" to view the report.</p>
        </div>
        <?php elseif (empty($reportData) && isset($_POST['generateReport']) && $canViewReports): ?>
        <div class="no-data" style="text-align: center; padding: 40px; color: #6b7280;">
            <ion-icon name="alert-circle-outline" style="font-size: 4rem; color: #f59e0b;"></ion-icon>
            <p style="font-size: 1.1rem; margin-top: 20px;">No data found for the selected report type.</p>
        </div>
        <?php endif; ?>
    </div>
</div>