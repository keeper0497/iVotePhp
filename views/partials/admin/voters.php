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
        
        <?php 
        // Filter only voters from all users
        $votersList = array_filter($allUsers, function($user) {
            return $user['role'] === 'voter';
        });
        ?>
        
        <?php if (count($votersList) === 0): ?>
            <p style="text-align: center; color: #6b7280; font-style: italic; padding: 20px;">No voters found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Email</th>
                        <th>College</th>
                        <th>Vote Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($votersList as $voter): ?>
                        <?php 
                            $hasVoted = $voter['has_voted'] ?? false;
                            $votedAt = $voter['voted_at'] ?? 'N/A';
                            
                            if ($hasVoted) {
                                $voteStatus = 'Voted';
                                $statusClass = 'voted'; // Make sure you have CSS for .voted
                            } else {
                                $voteStatus = 'Not Voted';
                                $statusClass = 'not-voted'; // Make sure you have CSS for .not-voted
                            }
                        ?>
                        <tr data-college="<?= htmlspecialchars($voter['college'] ?? '') ?>">
                            <td><?= htmlspecialchars($voter['id']) ?></td>
                            <td><?= htmlspecialchars($voter['student_id']) ?></td>
                            <td><?= htmlspecialchars($voter['email']) ?></td>
                            <td><?= htmlspecialchars($voter['college'] ?? 'N/A') ?></td>
                            <td>
                                <span class="vote-status <?= $statusClass ?>">
                                    <?= $voteStatus ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($voteStatus === 'Voted'): ?>
                                    <button class="action-btn" style="background-color: #f59e0b; color: white; padding: 4px 8px; font-size: 0.8rem;" 
                                            onclick="alert('Reset vote functionality can be implemented here.')">
                                        Reset Vote
                                    </button>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div id="votersSummary" style="margin-top: 20px; padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #4f46e5;">
                <strong>Total Voters:</strong> <span id="totalVoters"><?= count($votersList) ?></span><br>
                <strong>Voted:</strong> <span id="votedCount">0</span><br>
                <strong>Not Voted:</strong> <span id="notVotedCount"><?= count($votersList) ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>