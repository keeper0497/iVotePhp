<div class="details" id="votersSection" style="display:none;">
    <div class="recentOrders">
        <div class="cardHeader">
            <h2>Voters Management</h2>
            <button class="btn" onclick="location.reload()">Refresh</button>
        </div>
        
        <div style="max-width: 400px; margin: 0 auto 20px;">
            <label for="collegeFilter" style="font-weight: bold; font-size: 1.1rem; display: block; margin-bottom: 10px;">Filter by College:</label>
            <select id="collegeFilter" style="width: 100%; padding: 12px; border: 2px solid #1472e4ff; border-radius: 8px; font-size: 1rem;">
                <option value="">All Colleges</option>
                <option value="CICT">CICT (College of Information and Communications Technology)</option>
                <option value="CBA">CBA (College of Business and Accountancy)</option>
                <option value="CHS">CHS (College of Health and Sciences)</option>
                <option value="CEA">CEA (College of Engineering and Architecture)</option>
                <option value="COS">COS (College of Science)</option>
                <option value="CHUMSS">CHUMSS (College of Humanities and Social Sciences)</option>
                <option value="CAF">CAF (College of Agriculture and Fisheries)</option>
                <option value="CIT">CIT (College of Information Technology)</option>
                <option value="COED">COED (College of Education)</option>
            </select>
        </div>
        
        <?php if (empty($votersWithStatus)): ?>
            <p style="text-align: center; color: #6b7280; font-style: italic; padding: 20px;">No voters found.</p>
        <?php else: ?>
            <table id="votersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Email</th>
                        <th>College</th>
                        <th>Vote Status</th>
                        <th>Voted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalVoters = 0;
                    $votedCount = 0;
                    
                    foreach ($votersWithStatus as $voter): 
                        $totalVoters++;
                        $hasVoted = (bool)$voter['has_voted'];
                        if ($hasVoted) $votedCount++;
                        
                        $votedAt = $voter['voted_at'] ?? null;
                        $votedAtFormatted = $votedAt ? date('M d, Y H:i', strtotime($votedAt)) : 'N/A';
                        
                        $voteStatus = $hasVoted ? 'Voted' : 'Not Voted';
                        $statusClass = $hasVoted ? 'voted' : 'not-voted';
                    ?>
                        <tr data-college="<?= htmlspecialchars($voter['college'] ?? '') ?>">
                            <td><?= htmlspecialchars($voter['id']) ?></td>
                            <td><?= htmlspecialchars($voter['student_id']) ?></td>
                            <td><?= htmlspecialchars($voter['email']) ?></td>
                            <td><?= htmlspecialchars($voter['college'] ?? 'N/A') ?></td>
                            <td>
                                <span class="vote-status <?= $statusClass ?>" style="
                                    padding: 5px 10px; 
                                    border-radius: 5px; 
                                    font-weight: bold;
                                    <?= $hasVoted 
                                        ? 'background: #d1fae5; color: #065f46;' 
                                        : 'background: #fee2e2; color: #991b1b;' 
                                    ?>
                                ">
                                    <?= $hasVoted ? '✓ Voted' : '✗ Not Voted' ?>
                                </span>
                            </td>
                            <td><?= $votedAtFormatted ?></td>
                            <td>
                                <?php if ($hasVoted): ?>
                                    <button class="action-btn" 
                                            style="background-color: #f59e0b; color: white; padding: 6px 12px; font-size: 0.85rem; border: none; border-radius: 5px; cursor: pointer;" 
                                            onclick="resetVote(<?= $voter['id'] ?>, '<?= htmlspecialchars($voter['student_id']) ?>')">
                                        Reset Vote
                                    </button>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div id="votersSummary" style="margin-top: 20px; padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #4f46e5;">
                <strong>Total Voters:</strong> <span id="totalVoters"><?= $totalVoters ?></span><br>
                <strong>Voted:</strong> <span id="votedCount" style="color: #059669;"><?= $votedCount ?></span><br>
                <strong>Not Voted:</strong> <span id="notVotedCount" style="color: #dc2626;"><?= $totalVoters - $votedCount ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// College Filter Functionality
document.getElementById('collegeFilter')?.addEventListener('change', function() {
    const selectedCollege = this.value;
    const rows = document.querySelectorAll('#votersTable tbody tr');
    
    let visibleTotal = 0;
    let visibleVoted = 0;
    
    rows.forEach(row => {
        const college = row.getAttribute('data-college');
        
        if (selectedCollege === '' || college === selectedCollege) {
            row.style.display = '';
            visibleTotal++;
            
            // Check if this row shows a voted status
            const statusSpan = row.querySelector('.vote-status.voted');
            if (statusSpan) {
                visibleVoted++;
            }
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update summary
    document.getElementById('totalVoters').textContent = visibleTotal;
    document.getElementById('votedCount').textContent = visibleVoted;
    document.getElementById('notVotedCount').textContent = visibleTotal - visibleVoted;
});

// Reset Vote Function (optional - implement if needed)
function resetVote(userId, studentId) {
    if (confirm(`Are you sure you want to reset the vote for Student ID: ${studentId}?\n\nThis will delete all their votes and allow them to vote again.`)) {
        // Create a form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'resetVote';
        input.value = userId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>