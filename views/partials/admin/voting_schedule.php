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

        <!-- Quick Actions -->
        <div class="quick-actions" style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 12px; padding: 20px; margin-top: 20px;">
            <h3 style="color: #92400e; margin-bottom: 15px; font-size: 1.1rem;">Quick Actions</h3>
            <div class="action-buttons" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="voting_status" value="open">
                    <input type="hidden" name="start_date" value="<?= date('Y-m-d\TH:i') ?>">
                    <input type="hidden" name="end_date" value="<?= date('Y-m-d\TH:i', strtotime('+8 hours')) ?>">
                    <input type="hidden" name="description" value="Quick open for 8 hours">
                    <button type="submit" name="updateVotingSchedule" class="btn" style="background-color: #059669;">Open Voting Now (8 hours)</button>
                </form>
                
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="voting_status" value="closed">
                    <input type="hidden" name="start_date" value="<?= date('Y-m-d\TH:i') ?>">
                    <input type="hidden" name="end_date" value="<?= date('Y-m-d\TH:i') ?>">
                    <input type="hidden" name="description" value="Voting closed by admin">
                    <button type="submit" name="updateVotingSchedule" class="btn" style="background-color: #dc2626;">Close Voting Now</button>
                </form>
            </div>
        </div>
    </div>
</div>