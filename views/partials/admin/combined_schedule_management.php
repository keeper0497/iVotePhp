<div class="details" id="scheduleManagementSection" style="display:none;">
    <div class="recentOrders">
        <div class="cardHeader">
            <h2>Schedule Management</h2>
            <!-- Toggle Buttons -->
            <div class="schedule-toggle-container">
                <div class="toggle-buttons">
                    <button id="votingToggleBtn" class="toggle-btn active" onclick="showScheduleType('voting')">
                        <ion-icon name="checkbox-outline"></ion-icon>
                        <span>Voting Schedule</span>
                    </button>
                    <button id="filingToggleBtn" class="toggle-btn" onclick="showScheduleType('filing')">
                        <ion-icon name="document-outline"></ion-icon>
                        <span>Filing Schedule</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Voting Schedule Section -->
        <div id="votingScheduleContainer" class="schedule-container">
            <?php if ($message && isset($_POST['updateVotingSchedule'])): ?>
                <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <!-- Current Voting Status -->
            <div class="voting-status-card">
                <h3>Current Voting Status</h3>
                <?php if ($votingSchedule): ?>
                    <div class="status-info">
                        <p><strong>Status:</strong> 
                            <span class="voting-status <?= $votingSchedule['status'] ?>">
                                <?= strtoupper($votingSchedule['status']) ?>
                            </span>
                        </p>
                        <?php if ($votingSchedule['status'] === 'open'): ?>
                            <p><strong>Active Period:</strong> 
                                <?php 
                                    $isActive = $votingController->isVotingActive($votingSchedule);
                                    echo $isActive ? '<span class="active-indicator">CURRENTLY ACTIVE</span>' : '<span class="inactive-indicator">NOT IN ACTIVE PERIOD</span>';
                                ?>
                            </p>
                        <?php endif; ?>
                        <p><strong>Start Date:</strong> <?= $votingSchedule['start_date'] ? date('M j, Y g:i A', strtotime($votingSchedule['start_date'])) : 'Not set' ?></p>
                        <p><strong>End Date:</strong> <?= $votingSchedule['end_date'] ? date('M j, Y g:i A', strtotime($votingSchedule['end_date'])) : 'Not set' ?></p>
                        <p><strong>Description:</strong> <?= htmlspecialchars($votingSchedule['description']) ?: 'No description' ?></p>
                        <p><strong>Last Updated:</strong> <?= date('M j, Y g:i A', strtotime($votingSchedule['updated_at'])) ?></p>
                    </div>
                <?php else: ?>
                    <p class="no-schedule">No voting schedule has been set yet.</p>
                <?php endif; ?>
            </div>

            <!-- Update Voting Schedule Form -->
            <div class="schedule-form-container">
                <h3>Update Voting Schedule</h3>
                <form method="POST" class="schedule-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="voting_status"><strong>Voting Status:</strong></label>
                            <select name="voting_status" id="voting_status" required>
                                <option value="closed" <?= (!$votingSchedule || $votingSchedule['status'] === 'closed') ? 'selected' : '' ?>>Closed</option>
                                <option value="open" <?= ($votingSchedule && $votingSchedule['status'] === 'open') ? 'selected' : '' ?>>Open</option>
                            </select>
                        </div>
                    </div>

                    <!-- Reset Votes Option -->
                    <div class="form-row" id="resetVotesOption" style="display: none; background: #fef3c7; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b; margin: 15px 0;">
                        <div class="form-group">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" name="reset_votes" id="reset_votes" value="1" style="margin-right: 10px; width: 20px; height: 20px; cursor: pointer;">
                                <span style="font-weight: bold; color: #92400e;">
                                    ⚠️ Reset all votes when closing (This will delete ALL votes from the database)
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date"><strong>Start Date & Time:</strong></label>
                            <input type="datetime-local" name="start_date" id="start_date" 
                                   value="<?= $votingSchedule ? date('Y-m-d\TH:i', strtotime($votingSchedule['start_date'])) : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date"><strong>End Date & Time:</strong></label>
                            <input type="datetime-local" name="end_date" id="end_date" 
                                   value="<?= $votingSchedule ? date('Y-m-d\TH:i', strtotime($votingSchedule['end_date'])) : '' ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="description"><strong>Description/Notes:</strong></label>
                            <textarea name="description" id="description" rows="3" placeholder="Optional description or notes about this voting period"><?= $votingSchedule ? htmlspecialchars($votingSchedule['description']) : '' ?></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="updateVotingSchedule" class="btn update-btn">Update Voting Schedule</button>
                    </div>
                </form>
            </div>

            <!-- Voting Quick Actions -->
            <div class="quick-actions voting-actions">
                <h3>Voting Quick Actions</h3>
                <div class="action-buttons">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="voting_status" value="open">
                        <input type="hidden" name="start_date" value="<?= date('Y-m-d\TH:i') ?>">
                        <input type="hidden" name="end_date" value="<?= date('Y-m-d\TH:i', strtotime('+8 hours')) ?>">
                        <input type="hidden" name="description" value="Quick open for 8 hours">
                        <button type="submit" name="updateVotingSchedule" class="btn btn-success">
                            ✓ Open Voting Now (8 hours)
                        </button>
                    </form>
                    
                    <form method="POST" style="display:inline;" onsubmit="return confirmQuickClose()">
                        <input type="hidden" name="voting_status" value="closed">
                        <input type="hidden" name="start_date" value="<?= date('Y-m-d\TH:i') ?>">
                        <input type="hidden" name="end_date" value="<?= date('Y-m-d\TH:i', strtotime('+1 minute')) ?>">
                        <input type="hidden" name="description" value="Voting closed by admin">
                        <input type="hidden" name="reset_votes" value="1">
                        <button type="submit" name="updateVotingSchedule" class="btn btn-danger">
                            ✗ Close & Reset Votes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Filing Schedule Section -->
        <div id="filingScheduleContainer" class="schedule-container" style="display:none;">
            <?php if ($message && isset($_POST['updateFilingSchedule'])): ?>
                <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <!-- Safety check for filingSchedule variable -->
            <?php 
            if (!isset($filingSchedule)) {
                $filingSchedule = null;
            }
            ?>

            <!-- Current Filing Status -->
            <div class="filing-status-card">
                <h3>Current Filing Status</h3>
                <?php if ($filingSchedule): ?>
                    <div class="status-info">
                        <p><strong>Status:</strong> 
                            <span class="filing-status <?= $filingSchedule['status'] ?>">
                                <?= strtoupper($filingSchedule['status']) ?>
                            </span>
                        </p>
                        <?php if ($filingSchedule['status'] === 'open'): ?>
                            <p><strong>Active Period:</strong> 
                                <?php 
                                    if (isset($filingController)) {
                                        $isActive = $filingController->isFilingActive($filingSchedule);
                                        echo $isActive ? '<span class="active-indicator">CURRENTLY ACTIVE</span>' : '<span class="inactive-indicator">NOT IN ACTIVE PERIOD</span>';
                                    }
                                ?>
                            </p>
                        <?php endif; ?>
                        <p><strong>Start Date:</strong> <?= $filingSchedule['start_date'] ? date('M j, Y g:i A', strtotime($filingSchedule['start_date'])) : 'Not set' ?></p>
                        <p><strong>End Date:</strong> <?= $filingSchedule['end_date'] ? date('M j, Y g:i A', strtotime($filingSchedule['end_date'])) : 'Not set' ?></p>
                        <p><strong>Description:</strong> <?= htmlspecialchars($filingSchedule['description'] ?? '') ?: 'No description' ?></p>
                        <p><strong>Last Updated:</strong> <?= date('M j, Y g:i A', strtotime($filingSchedule['updated_at'])) ?></p>
                    </div>
                <?php else: ?>
                    <p class="no-schedule">No filing schedule has been set yet.</p>
                <?php endif; ?>
            </div>

            <!-- Update Filing Schedule Form -->
            <div class="schedule-form-container">
                <h3>Update Filing Schedule</h3>
                <form method="POST" class="schedule-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="filing_status"><strong>Filing Status:</strong></label>
                            <select name="filing_status" id="filing_status" required>
                                <option value="closed" <?= (!$filingSchedule || $filingSchedule['status'] === 'closed') ? 'selected' : '' ?>>Closed</option>
                                <option value="open" <?= ($filingSchedule && $filingSchedule['status'] === 'open') ? 'selected' : '' ?>>Open</option>
                            </select>
                        </div>
                    </div>

                    <!-- Reset Filings Option -->
                    <div class="form-row" id="resetFilingsOption" style="display: none; background: #fef3c7; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b; margin: 15px 0;">
                        <div class="form-group">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" name="reset_filings" id="reset_filings" value="1" style="margin-right: 10px; width: 20px; height: 20px; cursor: pointer;">
                                <span style="font-weight: bold; color: #92400e;">
                                    ⚠️ Reset all filings when closing (This will delete ALL candidate filings from the database)
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="filing_start_date"><strong>Start Date & Time:</strong></label>
                            <input type="datetime-local" name="filing_start_date" id="filing_start_date" 
                                   value="<?= ($filingSchedule && $filingSchedule['start_date']) ? date('Y-m-d\TH:i', strtotime($filingSchedule['start_date'])) : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="filing_end_date"><strong>End Date & Time:</strong></label>
                            <input type="datetime-local" name="filing_end_date" id="filing_end_date" 
                                   value="<?= ($filingSchedule && $filingSchedule['end_date']) ? date('Y-m-d\TH:i', strtotime($filingSchedule['end_date'])) : '' ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="filing_description"><strong>Description/Notes:</strong></label>
                            <textarea name="filing_description" id="filing_description" rows="3" placeholder="Optional description or notes about this filing period"><?= $filingSchedule ? htmlspecialchars($filingSchedule['description'] ?? '') : '' ?></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="updateFilingSchedule" class="btn update-btn">Update Filing Schedule</button>
                    </div>
                </form>
            </div>

            <!-- Filing Statistics -->
            <div class="filing-stats">
                <h3>📊 Filing Statistics</h3>
                <?php 
                if (isset($filingController) && method_exists($filingController, 'getTotalFilingsCount')) {
                    $filingStats = $filingController->getTotalFilingsCount();
                } else {
                    $filingStats = ['total' => 0, 'main_count' => 0, 'sub_count' => 0];
                }
                ?>
                <div class="stats-grid">
                    <div class="stat-item">
                        <p><strong>Total Filings:</strong> <?= $filingStats['total'] ?></p>
                    </div>
                    <div class="stat-item">
                        <p><strong>Main Org Candidates:</strong> <?= $filingStats['main_count'] ?></p>
                    </div>
                    <div class="stat-item">
                        <p><strong>Sub Org Candidates:</strong> <?= $filingStats['sub_count'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Filing Quick Actions -->
            <div class="quick-actions filing-actions">
                <h3>Filing Quick Actions</h3>
                <div class="action-buttons">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="filing_status" value="open">
                        <input type="hidden" name="filing_start_date" value="<?= date('Y-m-d\TH:i') ?>">
                        <input type="hidden" name="filing_end_date" value="<?= date('Y-m-d\TH:i', strtotime('+7 days')) ?>">
                        <input type="hidden" name="filing_description" value="Quick open for 7 days">
                        <button type="submit" name="updateFilingSchedule" class="btn btn-success">
                            ✓ Open Filing Now (7 days)
                        </button>
                    </form>
                    
                    <form method="POST" style="display:inline;" onsubmit="return confirmQuickCloseFiling()">
                        <input type="hidden" name="filing_status" value="closed">
                        <input type="hidden" name="filing_start_date" value="<?= date('Y-m-d\TH:i') ?>">
                        <input type="hidden" name="filing_end_date" value="<?= date('Y-m-d\TH:i', strtotime('+1 minute')) ?>">
                        <input type="hidden" name="filing_description" value="Filing closed by admin">
                        <input type="hidden" name="reset_filings" value="1">
                        <button type="submit" name="updateFilingSchedule" class="btn btn-danger">
                            ✗ Close & Reset Filings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Toggle Button Styles */
.schedule-toggle-container {
    margin-bottom: 20px;
}

.toggle-buttons {
    display: flex;
    gap: 10px;
    background: #f1f5f9;
    padding: 8px;
    border-radius: 12px;
    width: fit-content;
}

.toggle-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
}

.toggle-btn:hover {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.toggle-btn.active {
    background: #3b82f6;
    color: white;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
}

.toggle-btn ion-icon {
    font-size: 18px;
}

/* Schedule Container */
.schedule-container {
    transition: all 0.3s ease;
}

/* Status Cards */
.filing-status-card,
.voting-status-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.filing-status.open,
.voting-status.open {
    color: #059669;
    background-color: #d1fae5;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: bold;
}

.filing-status.closed,
.voting-status.closed {
    color: #dc2626;
    background-color: #fee2e2;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: bold;
}

.active-indicator {
    color: #059669;
    background-color: #d1fae5;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
}

.inactive-indicator {
    color: #dc2626;
    background-color: #fee2e2;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
}

/* Form Styles */
.schedule-form-container {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.form-group {
    flex: 1;
    min-width: 200px;
}

.form-group.full-width {
    flex-basis: 100%;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #374151;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 1rem;
}

/* Button Styles */
.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.btn-success {
    background-color: #059669;
    color: white;
}

.btn-success:hover {
    background-color: #047857;
}

.btn-danger {
    background-color: #dc2626;
    color: white;
}

.btn-danger:hover {
    background-color: #b91c1c;
}

.update-btn {
    background-color: #1d4ed8;
    color: white;
}

.update-btn:hover {
    background-color: #1e40af;
}

/* Quick Actions */
.quick-actions {
    background: #fef3c7;
    border: 1px solid #fbbf24;
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
}

.quick-actions h3 {
    color: #92400e;
    margin-bottom: 15px;
    font-size: 1.1rem;
}

.action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

/* Filing Stats */
.filing-stats {
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
}

.filing-stats h3 {
    color: #374151;
    margin-bottom: 15px;
    font-size: 1.1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.stat-item p {
    margin: 5px 0;
    color: #6b7280;
}

/* Message Styles */
.message {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: bold;
}

.message.success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.message.error {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}
</style>

<script>
// Toggle between voting and filing schedules
function showScheduleType(type) {
    // Update toggle buttons
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    if (type === 'voting') {
        document.getElementById('votingToggleBtn').classList.add('active');
        document.getElementById('votingScheduleContainer').style.display = 'block';
        document.getElementById('filingScheduleContainer').style.display = 'none';
    } else if (type === 'filing') {
        document.getElementById('filingToggleBtn').classList.add('active');
        document.getElementById('filingScheduleContainer').style.display = 'block';
        document.getElementById('votingScheduleContainer').style.display = 'none';
    }
}

// Show/hide reset options based on status
document.addEventListener('DOMContentLoaded', function() {
    // Voting schedule reset option
    document.getElementById('voting_status')?.addEventListener('change', function() {
        const resetOption = document.getElementById('resetVotesOption');
        if (this.value === 'closed') {
            resetOption.style.display = 'block';
        } else {
            resetOption.style.display = 'none';
            document.getElementById('reset_votes').checked = false;
        }
    });
    
    // Filing schedule reset option
    document.getElementById('filing_status')?.addEventListener('change', function() {
        const resetOption = document.getElementById('resetFilingsOption');
        if (this.value === 'closed') {
            resetOption.style.display = 'block';
        } else {
            resetOption.style.display = 'none';
            document.getElementById('reset_filings').checked = false;
        }
    });
});

// Confirmation functions
function confirmQuickClose() {
    return confirm('⚠️ This will close voting AND delete all votes!\n\nAre you sure you want to proceed?');
}

function confirmQuickCloseFiling() {
    return confirm('⚠️ This will close filing AND delete all filings!\n\nAre you sure you want to proceed?');
}
</script>