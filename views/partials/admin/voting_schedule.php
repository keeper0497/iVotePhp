<div class="details" id="votingScheduleSection" style="display:none;">
    <div class="recentOrders">
        <div class="cardHeader">
            <h2>Voting Schedule Management</h2>
        </div>

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

                <!-- Reset Votes Option (only show when closing) -->
                <div class="form-row" id="resetVotesOption" style="display: none; background: #fef3c7; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b; margin: 15px 0;">
                    <div class="form-group">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="reset_votes" id="reset_votes" value="1" style="margin-right: 10px; width: 20px; height: 20px; cursor: pointer;">
                            <span style="font-weight: bold; color: #92400e;">
                                ⚠️ Reset all votes when closing (This will delete ALL votes from the database)
                            </span>
                        </label>
                        <p style="margin: 10px 0 0 30px; color: #78350f; font-size: 0.9rem;">
                            Check this box to clear all voter data when closing voting. This action cannot be undone.
                        </p>
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
                    <?php if ($votingSchedule && $votingSchedule['status'] === 'open'): ?>
                        <button type="button" class="btn emergency-close" onclick="emergencyClose()">Emergency Close</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Manual Reset All Votes -->
        <!-- <div class="danger-zone" style="background: #fee2e2; border: 2px solid #dc2626; border-radius: 12px; padding: 20px; margin-top: 20px;">
            <h3 style="color: #991b1b; margin-bottom: 15px;">⚠️ Danger Zone</h3>
            <p style="color: #991b1b; margin-bottom: 15px;">
                <strong>Reset All Votes:</strong> This will permanently delete ALL votes from the database. This action cannot be undone!
            </p>
            <button type="button" class="btn" onclick="confirmResetAllVotes()" 
                    style="background: #dc2626; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
                🗑️ Reset All Votes Now
            </button>
            <p style="color: #991b1b; margin-top: 10px; font-size: 0.9rem;">
                Current votes in database: <strong><?= $votingController->getTotalVotesCount() ?></strong>
            </p>
        </div> -->

        <!-- Quick Actions -->
        <div class="quick-actions" style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 12px; padding: 20px; margin-top: 20px;">
            <h3 style="color: #92400e; margin-bottom: 15px; font-size: 1.1rem;">Quick Actions</h3>
            <div class="action-buttons" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="voting_status" value="open">
                    <input type="hidden" name="start_date" value="<?= date('Y-m-d\TH:i') ?>">
                    <input type="hidden" name="end_date" value="<?= date('Y-m-d\TH:i', strtotime('+8 hours')) ?>">
                    <input type="hidden" name="description" value="Quick open for 8 hours">
                    <button type="submit" name="updateVotingSchedule" class="btn" style="background-color: #059669; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer;">
                        ✓ Open Voting Now (8 hours)
                    </button>
                </form>
                
                <form method="POST" style="display:inline;" onsubmit="return confirmQuickClose()">
                    <input type="hidden" name="voting_status" value="closed">
                    <input type="hidden" name="start_date" value="<?= date('Y-m-d\TH:i') ?>">
                    <input type="hidden" name="end_date" value="<?= date('Y-m-d\TH:i', strtotime('+1 minute')) ?>">
                    <input type="hidden" name="description" value="Voting closed by admin">
                    <input type="hidden" name="reset_votes" value="1">
                    <button type="submit" name="updateVotingSchedule" class="btn" style="background-color: #dc2626; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer;">
                        ✗ Close & Reset Votes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide reset votes option based on voting status
document.getElementById('voting_status')?.addEventListener('change', function() {
    const resetOption = document.getElementById('resetVotesOption');
    if (this.value === 'closed') {
        resetOption.style.display = 'block';
    } else {
        resetOption.style.display = 'none';
        document.getElementById('reset_votes').checked = false;
    }
});

// Confirm before resetting all votes
function confirmResetAllVotes() {
    const totalVotes = <?= $votingController->getTotalVotesCount() ?>;
    
    if (totalVotes === 0) {
        alert('There are no votes to reset.');
        return;
    }
    
    const confirmation = confirm(
        `⚠️ WARNING: You are about to delete ${totalVotes} vote(s) from the database!\n\n` +
        `This action CANNOT be undone.\n\n` +
        `Are you absolutely sure you want to proceed?`
    );
    
    if (confirmation) {
        const doubleCheck = prompt('Type "DELETE ALL VOTES" to confirm:');
        if (doubleCheck === 'DELETE ALL VOTES') {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            const resetInput = document.createElement('input');
            resetInput.type = 'hidden';
            resetInput.name = 'resetAllVotes';
            resetInput.value = '1';
            
            const confirmInput = document.createElement('input');
            confirmInput.type = 'hidden';
            confirmInput.name = 'confirm_reset';
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

// Confirm quick close with reset
function confirmQuickClose() {
    const totalVotes = <?= $votingController->getTotalVotesCount() ?>;
    
    if (totalVotes === 0) {
        return confirm('Close voting now?');
    }
    
    return confirm(
        `⚠️ This will close voting AND delete ${totalVotes} vote(s)!\n\n` +
        `Are you sure you want to proceed?`
    );
}

// Trigger check on page load
document.addEventListener('DOMContentLoaded', function() {
    const votingStatus = document.getElementById('voting_status');
    if (votingStatus && votingStatus.value === 'closed') {
        document.getElementById('resetVotesOption').style.display = 'block';
    }
});
</script>