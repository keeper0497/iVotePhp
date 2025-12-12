<div class="details" id="filingScheduleSection" style="display:none;">
    <div class="recentOrders">
        <div class="cardHeader">
            <h2>Filing Schedule Management</h2>
        </div>

        <?php if ($message && isset($_POST['updateFilingSchedule'])): ?>
            <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- SAFETY CHECK: Ensure filingSchedule variable exists -->
        <?php 
        // Safety check - create empty filingSchedule if it doesn't exist
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
                                // Safety check for filingController
                                if (isset($filingController)) {
                                    $isActive = $filingController->isFilingActive($filingSchedule);
                                    echo $isActive ? '<span class="active-indicator">CURRENTLY ACTIVE</span>' : '<span class="inactive-indicator">NOT IN ACTIVE PERIOD</span>';
                                } else {
                                    echo '<span class="inactive-indicator">CHECKING...</span>';
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
                <div class="setup-help" style="background: #fef3c7; padding: 15px; border-radius: 8px; margin-top: 15px;">
                    <p><strong>⚠️ Setup Required:</strong></p>
                    <ul style="margin: 10px 0;">
                        <li>Make sure you've run the database setup script</li>
                        <li>Verify that the filing_schedule table exists</li>
                        <li>Check that FilingController has the new methods</li>
                    </ul>
                </div>
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

                <!-- Reset Filings Option (only show when closing) -->
                <div class="form-row" id="resetFilingsOption" style="display: none; background: #fef3c7; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b; margin: 15px 0;">
                    <div class="form-group">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="reset_filings" id="reset_filings" value="1" style="margin-right: 10px; width: 20px; height: 20px; cursor: pointer;">
                            <span style="font-weight: bold; color: #92400e;">
                                ⚠️ Reset all filings when closing (This will delete ALL candidate filings from the database)
                            </span>
                        </label>
                        <p style="margin: 10px 0 0 30px; color: #78350f; font-size: 0.9rem;">
                            Check this box to clear all candidate filing data when closing filing period. This action cannot be undone.
                        </p>
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
                    <?php if ($filingSchedule && $filingSchedule['status'] === 'open'): ?>
                        <button type="button" class="btn emergency-close" onclick="emergencyCloseFiling()">Emergency Close Filing</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Filing Statistics -->
        <div class="filing-stats" style="background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 12px; padding: 20px; margin-top: 20px;">
            <h3 style="color: #374151; margin-bottom: 15px; font-size: 1.1rem;">📊 Filing Statistics</h3>
            <?php 
            // Safety check for filingController and method
            if (isset($filingController) && method_exists($filingController, 'getTotalFilingsCount')) {
                $filingStats = $filingController->getTotalFilingsCount();
            } else {
                $filingStats = ['total' => 0, 'main_count' => 0, 'sub_count' => 0];
            }
            ?>
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div class="stat-item">
                    <p style="margin: 5px 0; color: #6b7280;"><strong>Total Filings:</strong> <?= $filingStats['total'] ?></p>
                </div>
                <div class="stat-item">
                    <p style="margin: 5px 0; color: #6b7280;"><strong>Main Org Candidates:</strong> <?= $filingStats['main_count'] ?></p>
                </div>
                <div class="stat-item">
                    <p style="margin: 5px 0; color: #6b7280;"><strong>Sub Org Candidates:</strong> <?= $filingStats['sub_count'] ?></p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions" style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 12px; padding: 20px; margin-top: 20px;">
            <h3 style="color: #92400e; margin-bottom: 15px; font-size: 1.1rem;">Quick Actions</h3>
            <div class="action-buttons" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="filing_status" value="open">
                    <input type="hidden" name="filing_start_date" value="<?= date('Y-m-d\TH:i') ?>">
                    <input type="hidden" name="filing_end_date" value="<?= date('Y-m-d\TH:i', strtotime('+7 days')) ?>">
                    <input type="hidden" name="filing_description" value="Quick open for 7 days">
                    <button type="submit" name="updateFilingSchedule" class="btn" style="background-color: #059669; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer;">
                        ✓ Open Filing Now (7 days)
                    </button>
                </form>
                
                <form method="POST" style="display:inline;" onsubmit="return confirmQuickCloseFiling()">
                    <input type="hidden" name="filing_status" value="closed">
                    <input type="hidden" name="filing_start_date" value="<?= date('Y-m-d\TH:i') ?>">
                    <input type="hidden" name="filing_end_date" value="<?= date('Y-m-d\TH:i', strtotime('+1 minute')) ?>">
                    <input type="hidden" name="filing_description" value="Filing closed by admin">
                    <input type="hidden" name="reset_filings" value="1">
                    <button type="submit" name="updateFilingSchedule" class="btn" style="background-color: #dc2626; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer;">
                        ✗ Close & Reset Filings
                    </button>
                </form>
                
                <!-- Manual Reset All Filings -->
                <button type="button" class="btn" onclick="confirmResetAllFilings()" 
                        style="background: #dc2626; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer;">
                    🗑️ Reset All Filings
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide reset filings option based on filing status
document.getElementById('filing_status')?.addEventListener('change', function() {
    const resetOption = document.getElementById('resetFilingsOption');
    if (this.value === 'closed') {
        resetOption.style.display = 'block';
    } else {
        resetOption.style.display = 'none';
        document.getElementById('reset_filings').checked = false;
    }
});

// Confirm before resetting all filings
function confirmResetAllFilings() {
    <?php if (isset($filingController) && method_exists($filingController, 'getTotalFilingsCount')): ?>
    const filingStats = <?= json_encode($filingController->getTotalFilingsCount()) ?>;
    <?php else: ?>
    const filingStats = {total: 0, main_count: 0, sub_count: 0};
    <?php endif; ?>
    
    if (filingStats.total === 0) {
        alert('There are no filings to reset.');
        return;
    }
    
    const confirmation = confirm(
        `⚠️ WARNING: You are about to delete ${filingStats.total} filing(s) from the database!\n\n` +
        `Breakdown:\n` +
        `- Main Organization Candidates: ${filingStats.main_count}\n` +
        `- Sub Organization Candidates: ${filingStats.sub_count}\n\n` +
        `This action CANNOT be undone.\n\n` +
        `Are you absolutely sure you want to proceed?`
    );
    
    if (confirmation) {
        const doubleCheck = prompt('Type "DELETE ALL FILINGS" to confirm:');
        if (doubleCheck === 'DELETE ALL FILINGS') {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            const resetInput = document.createElement('input');
            resetInput.type = 'hidden';
            resetInput.name = 'resetAllFilings';
            resetInput.value = '1';
            
            const confirmInput = document.createElement('input');
            confirmInput.type = 'hidden';
            confirmInput.name = 'confirm_filing_reset';
            confirmInput.value = 'yes';
            
            form.appendChild(resetInput);
            form.appendChild(confirmInput);
            document.body.appendChild(form);
            form.submit();
        } else {
            alert('Reset cancelled. Confirmation text did not match.');
        }
    }
}

// Confirm quick close with reset for filing
function confirmQuickCloseFiling() {
    <?php if (isset($filingController) && method_exists($filingController, 'getTotalFilingsCount')): ?>
    const filingStats = <?= json_encode($filingController->getTotalFilingsCount()) ?>;
    <?php else: ?>
    const filingStats = {total: 0, main_count: 0, sub_count: 0};
    <?php endif; ?>
    
    if (filingStats.total === 0) {
        return confirm('Close filing now?');
    }
    
    return confirm(
        `⚠️ This will close filing AND delete ${filingStats.total} filing(s)!\n\n` +
        `Breakdown:\n` +
        `- Main Organization: ${filingStats.main_count} candidates\n` +
        `- Sub Organizations: ${filingStats.sub_count} candidates\n\n` +
        `Are you sure you want to proceed?`
    );
}

// Emergency close filing
function emergencyCloseFiling() {
    if (confirm('⚠️ Emergency close filing period now?\n\nThis will immediately close the filing period but keep existing filings.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'filing_status';
        statusInput.value = 'closed';
        
        const startInput = document.createElement('input');
        startInput.type = 'hidden';
        startInput.name = 'filing_start_date';
        startInput.value = '<?= date('Y-m-d\TH:i') ?>';
        
        const endInput = document.createElement('input');
        endInput.type = 'hidden';
        endInput.name = 'filing_end_date';
        endInput.value = '<?= date('Y-m-d\TH:i', strtotime('+1 minute')) ?>';
        
        const descInput = document.createElement('input');
        descInput.type = 'hidden';
        descInput.name = 'filing_description';
        descInput.value = 'Emergency closed by admin';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'updateFilingSchedule';
        actionInput.value = '1';
        
        form.appendChild(statusInput);
        form.appendChild(startInput);
        form.appendChild(endInput);
        form.appendChild(descInput);
        form.appendChild(actionInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Trigger check on page load
document.addEventListener('DOMContentLoaded', function() {
    const filingStatus = document.getElementById('filing_status');
    if (filingStatus && filingStatus.value === 'closed') {
        document.getElementById('resetFilingsOption').style.display = 'block';
    }
});
</script>

<style>
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

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    font-size: 1rem;
}

.update-btn {
    background-color: #1d4ed8;
    color: white;
}

.update-btn:hover {
    background-color: #1e40af;
}

.emergency-close {
    background-color: #ef4444;
    color: white;
}

.emergency-close:hover {
    background-color: #dc2626;
}

.no-schedule {
    color: #6b7280;
    font-style: italic;
    text-align: center;
    padding: 20px;
}

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

.setup-help {
    font-size: 0.9rem;
}

.setup-help ul {
    padding-left: 20px;
}
</style>