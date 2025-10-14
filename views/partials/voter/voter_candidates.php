<div id="candidatesSection" style="display:none; padding:20px;">
    <div class="candidates-profile-section">
        <h2>Candidates Profile</h2>
        <p style="text-align: center; margin-bottom: 30px; color: #6b7280;">
            View the accepted candidates running for various positions
        </p>
        
        <?php
        // Main Organizations
        $mainOrgs = ['USC' => 'University Student Council (USC)', 'CSC' => 'College Student Council (CSC)'];
        
        foreach ($mainOrgs as $orgCode => $orgName):
            $orgCandidates = array_filter($mainCandidates, function($c) use ($orgCode) {
                return ($c['organization'] ?? '') === $orgCode;
            });
        ?>
            <div class="organization-section" style="margin-bottom: 40px;">
                <h3 style="color: #1e40af; border-bottom: 2px solid #3b82f6; padding-bottom: 10px;">
                    🏛️ <?= htmlspecialchars($orgName) ?> Candidates
                </h3>
                
                <?php if (count($orgCandidates) > 0): ?>
                    <div class="candidates-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                        <?php foreach ($orgCandidates as $candidate): ?>
                            <div class="candidate-card" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; text-align: center;">
                                <div class="candidate-image" style="margin-bottom: 10px;">
                                    <?php
                                    $profilePic = !empty($candidate['profile_pic']) ? htmlspecialchars($candidate['profile_pic']) : '';
                                    if ($profilePic):
                                    ?>
                                        <img src="<?= $profilePic ?>" alt="Profile" 
                                             style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;"
                                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2YzZjRmNiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjEyIiBmaWxsPSIjOWNhM2FmIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+Tm8gSW1hZ2U8L3RleHQ+PC9zdmc+';">
                                    <?php else: ?>
                                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2YzZjRmNiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjEyIiBmaWxsPSIjOWNhM2FmIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+Tm8gSW1hZ2U8L3RleHQ+PC9zdmc+" 
                                             alt="No Image" style="width: 100px; height: 100px; border-radius: 50%;">
                                    <?php endif; ?>
                                </div>
                                <h4 style="margin: 10px 0; color: #1f2937;">
                                    <?= htmlspecialchars(trim(($candidate['first_name'] ?? '') . ' ' . ($candidate['middle_name'] ?? '') . ' ' . ($candidate['last_name'] ?? ''))) ?>
                                </h4>
                                <div style="color: #6b7280; font-size: 0.9rem;">
                                    <?= htmlspecialchars($candidate['position'] ?? 'Position not specified') ?>
                                </div>
                                <div style="margin-top: 10px; padding: 5px; background: #e6f3ff; border-radius: 4px; font-size: 0.85rem; color: #0ea5e9;">
                                    <?= htmlspecialchars($orgName) ?>
                                </div>
                                <?php if (!empty($candidate['college'])): ?>
                                    <div style="margin-top: 5px; font-size: 0.85rem; color: #6b7280;">
                                        College: <?= htmlspecialchars($candidate['college']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #9ca3af; padding: 20px;">
                        No <?= htmlspecialchars($orgName) ?> candidates have been accepted yet.
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <!-- Sub Organizations -->
        <div class="organization-section">
            <h3 style="color: #1e40af; border-bottom: 2px solid #3b82f6; padding-bottom: 10px;">
                🎯 Sub Organization Candidates
            </h3>
            
            <?php if (count($subCandidates) > 0): ?>
                <div class="candidates-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                    <?php foreach ($subCandidates as $candidate): ?>
                        <div class="candidate-card" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; text-align: center;">
                            <div class="candidate-image" style="margin-bottom: 10px;">
                                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2YzZjRmNiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjEyIiBmaWxsPSIjOWNhM2FmIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+Tm8gSW1hZ2U8L3RleHQ+PC9zdmc+" 
                                     alt="Profile" style="width: 100px; height: 100px; border-radius: 50%;">
                            </div>
                            <h4 style="margin: 10px 0; color: #1f2937;">
                                <?= htmlspecialchars(trim(($candidate['first_name'] ?? '') . ' ' . ($candidate['middle_name'] ?? '') . ' ' . ($candidate['last_name'] ?? ''))) ?>
                            </h4>
                            <div style="color: #6b7280; font-size: 0.9rem;">
                                <?= htmlspecialchars($candidate['position'] ?? 'Representative') ?>
                            </div>
                            <div style="margin-top: 10px; padding: 5px; background: #f0f9ff; border-radius: 4px; font-size: 0.85rem; color: #1f2937;">
                                <?= htmlspecialchars($candidate['organization'] ?? 'Unknown Sub Org') ?>
                            </div>
                            <?php if (!empty($candidate['year'])): ?>
                                <div style="margin-top: 5px; font-size: 0.85rem; color: #6b7280;">
                                    Year <?= htmlspecialchars($candidate['year']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #9ca3af; padding: 20px;">
                    No sub organization candidates have been accepted yet.
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>