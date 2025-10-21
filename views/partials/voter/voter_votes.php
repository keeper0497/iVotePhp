<?php
// Position limits configuration
$positionLimits = [
    'Senators' => 8,
    'Legislator' => 2,
    'Representative' => 8
];

function isMultiSelectPosition($position, $positionLimits) {
    return isset($positionLimits[$position]) && $positionLimits[$position] > 1;
}

function getPositionLimit($position, $positionLimits) {
    return $positionLimits[$position] ?? 1;
}
?>

<div id="votesSection" style="display:none; padding:20px;">
    <div class="voting-section">
        <h2>Cast Your Vote</h2>
        
        <?php if ($votingStatus['can_vote']): ?>
            <?php if ($hasVoted): ?>
                <div class="already-voted-message" style="text-align: center; padding: 40px; background: #d1fae5; border-radius: 12px; margin: 20px 0;">
                    <div style="font-size: 4rem; color: #059669; margin-bottom: 20px;">✓</div>
                    <h3 style="color: #065f46; margin-bottom: 10px;">You Have Already Voted!</h3>
                    <p style="color: #047857;">Thank you for participating in the election. Your vote has been securely recorded.</p>
                    <p style="color: #047857;">Multiple voting is not allowed to ensure fair elections.</p>
                </div>
            <?php else: ?>
                <form method="POST" action="" id="votingForm">
                    <?php
                    // Group main org candidates by position
                    $mainByPosition = [];
                    foreach ($mainCandidates as $candidate) {
                        $position = $candidate['position'] ?? 'Other';
                        if (!isset($mainByPosition[$position])) {
                            $mainByPosition[$position] = [];
                        }
                        $mainByPosition[$position][] = $candidate;
                    }
                    
                    if (count($mainByPosition) > 0):
                    ?>
                        <div class="candidate-group" style="margin-bottom: 30px; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <h3 style="color: #1e40af; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px;">
                                Main Organization Candidates
                            </h3>
                            
                            <?php foreach ($mainByPosition as $position => $candidates): ?>
                                <?php 
                                $isMultiSelect = isMultiSelectPosition($position, $positionLimits);
                                $limit = getPositionLimit($position, $positionLimits);
                                $inputType = $isMultiSelect ? 'checkbox' : 'radio';
                                $inputName = $isMultiSelect ? "main_{$position}[]" : "main_" . str_replace(' ', '_', $position);
                                ?>
                                <div style="margin-bottom: 25px;" data-position="<?= htmlspecialchars($position) ?>" data-limit="<?= $limit ?>">
                                    <h4 style="color: #475569; margin-bottom: 15px;">
                                        <?= htmlspecialchars($position) ?>
                                        <?php if ($isMultiSelect): ?>
                                            <span style="color: #059669; font-size: 0.9em; font-weight: normal;">
                                                (Select up to <?= $limit ?> candidates)
                                            </span>
                                        <?php endif; ?>
                                    </h4>
                                    
                                    <?php if ($isMultiSelect): ?>
                                        <div class="selection-counter" style="margin-bottom: 10px; padding: 8px; background: #f1f5f9; border-radius: 4px; font-size: 0.9em; color: #475569;">
                                            Selected: <span class="count">0</span> / <?= $limit ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php foreach ($candidates as $candidate): ?>
                                        <div class="candidate-option" style="padding: 12px; margin-bottom: 10px; border: 1px solid #e5e7eb; border-radius: 6px; display: flex; align-items: center; transition: all 0.2s;">
                                            <input type="<?= $inputType ?>" 
                                                   name="<?= htmlspecialchars($inputName) ?>" 
                                                   value="<?= $candidate['id'] ?>" 
                                                   id="main<?= $candidate['id'] ?>"
                                                   style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                                                   class="candidate-input"
                                                   data-position="<?= htmlspecialchars($position) ?>">
                                            <label for="main<?= $candidate['id'] ?>" style="cursor: pointer; flex: 1;">
                                                <strong><?= htmlspecialchars(($candidate['first_name'] ?? '') . ' ' . ($candidate['last_name'] ?? '')) ?></strong>
                                                <br>
                                                <small style="color: #6b7280;">
                                                    Organization: <?= htmlspecialchars($candidate['organization'] ?? 'Main Organization') ?>
                                                    <?php if (!empty($candidate['college'])): ?>
                                                        | College: <?= htmlspecialchars($candidate['college']) ?>
                                                    <?php endif; ?>
                                                </small>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <?php if ($isMultiSelect): ?>
                                        <div class="validation-message" style="color: #dc2626; font-size: 0.9em; margin-top: 5px; display: none;">
                                            You can only select up to <?= $limit ?> candidates for <?= htmlspecialchars($position) ?>.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="candidate-group" style="padding: 20px; text-align: center; color: #9ca3af;">
                            <p>No candidates filed for Main Organization.</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php
                    // Group sub org candidates by organization
                    $subByOrg = [];
                    foreach ($subCandidates as $candidate) {
                        $org = $candidate['organization'] ?? 'Other';
                        if (!isset($subByOrg[$org])) {
                            $subByOrg[$org] = [];
                        }
                        $subByOrg[$org][] = $candidate;
                    }
                    
                    if (count($subByOrg) > 0):
                    ?>
                        <div class="candidate-group" style="margin-bottom: 30px; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <h3 style="color: #1e40af; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px;">
                                Sub Organization Candidates
                            </h3>
                            
                            <?php foreach ($subByOrg as $org => $candidates): ?>
                                <div style="margin-bottom: 25px;">
                                    <h4 style="color: #475569; margin-bottom: 15px;">
                                        <?= htmlspecialchars($org) ?>
                                    </h4>
                                    
                                    <?php foreach ($candidates as $candidate): ?>
                                        <div class="candidate-option" style="padding: 12px; margin-bottom: 10px; border: 1px solid #e5e7eb; border-radius: 6px; display: flex; align-items: center;">
                                            <input type="radio" 
                                                   name="sub_<?= str_replace(' ', '_', $org) ?>" 
                                                   value="<?= $candidate['id'] ?>" 
                                                   id="sub<?= $candidate['id'] ?>"
                                                   style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;">
                                            <label for="sub<?= $candidate['id'] ?>" style="cursor: pointer; flex: 1;">
                                                <strong><?= htmlspecialchars(($candidate['first_name'] ?? '') . ' ' . ($candidate['last_name'] ?? '')) ?></strong> 
                                                - <?= htmlspecialchars($candidate['position'] ?? 'Representative') ?>
                                                <br>
                                                <small style="color: #6b7280;">
                                                    Organization: <?= htmlspecialchars($org) ?> 
                                                    <?php if (!empty($candidate['year'])): ?>
                                                        | Year <?= htmlspecialchars($candidate['year']) ?>
                                                    <?php endif; ?>
                                                </small>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="candidate-group" style="padding: 20px; text-align: center; color: #9ca3af;">
                            <p>No candidates filed for Sub Organization.</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (count($mainByPosition) > 0 || count($subByOrg) > 0): ?>
                        <div style="text-align: center; margin-top: 30px;">
                            <button type="submit" 
                                    name="submitVote" 
                                    class="btn" 
                                    id="submitVoteBtn"
                                    style="padding: 15px 40px; font-size: 1.1rem; background: linear-gradient(135deg, #0ea5e9, #3b82f6); color: white; border: none; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 12px rgba(14,165,233,0.3); transition: all 0.3s;">
                                🗳️ Submit My Vote
                            </button>
                            <p style="margin-top: 15px; color: #6b7280; font-size: 0.9rem;">
                                ⚠️ Once submitted, your vote cannot be changed
                            </p>
                        </div>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; background: #fee2e2; border-radius: 12px;">
                <div style="font-size: 4rem; color: #dc2626; margin-bottom: 20px;">🔒</div>
                <h3 style="color: #991b1b;">Voting is Currently Closed</h3>
                <p style="color: #b91c1c; margin-top: 10px;">
                    <?= htmlspecialchars($votingStatus['message']) ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const positionLimits = <?= json_encode($positionLimits) ?>;
    
    // Handle checkbox validation for multi-select positions
    document.querySelectorAll('.candidate-input[type="checkbox"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const position = this.dataset.position;
            const limit = positionLimits[position] || 1;
            const positionContainer = document.querySelector(`[data-position="${position}"]`);
            const checkboxes = positionContainer.querySelectorAll('.candidate-input[type="checkbox"]');
            const counter = positionContainer.querySelector('.selection-counter .count');
            const validationMessage = positionContainer.querySelector('.validation-message');
            
            let checkedCount = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) checkedCount++;
            });
            
            // Update counter
            if (counter) {
                counter.textContent = checkedCount;
            }
            
            // Check if limit exceeded
            if (checkedCount > limit) {
                // Uncheck the current checkbox
                this.checked = false;
                checkedCount--;
                if (counter) counter.textContent = checkedCount;
                
                // Show validation message
                if (validationMessage) {
                    validationMessage.style.display = 'block';
                    setTimeout(() => {
                        validationMessage.style.display = 'none';
                    }, 3000);
                }
            }
            
            // Update visual feedback
            checkboxes.forEach(cb => {
                const candidateOption = cb.closest('.candidate-option');
                if (cb.checked) {
                    candidateOption.style.background = '#e0f2fe';
                    candidateOption.style.borderColor = '#0ea5e9';
                } else {
                    candidateOption.style.background = '';
                    candidateOption.style.borderColor = '#e5e7eb';
                }
            });
        });
    });
    
    // Form validation before submission
    document.getElementById('votingForm').addEventListener('submit', function(e) {
        let isValid = true;
        const errors = [];
        
        // Check each multi-select position
        Object.keys(positionLimits).forEach(position => {
            const positionContainer = document.querySelector(`[data-position="${position}"]`);
            if (positionContainer) {
                const checkboxes = positionContainer.querySelectorAll('.candidate-input[type="checkbox"]:checked');
                const limit = positionLimits[position];
                
                if (checkboxes.length > limit) {
                    isValid = false;
                    errors.push(`Too many candidates selected for ${position}. Maximum allowed: ${limit}`);
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
        }
    });
});
</script>