<div id="candidatesSection" style="display:none; padding:20px;">
    <div class="candidates-profile-section">
        <h2>Candidates Profile</h2>
        <p style="text-align: center; margin-bottom: 30px; color: #6b7280;">
            View the accepted candidates running for various positions
        </p>

        <?php
        // Group main candidates by organization
        $mainByOrg = [];
        foreach ($mainCandidates as $candidate) {
            $org = $candidate['organization'] ?? 'Unknown';
            if (!isset($mainByOrg[$org])) {
                $mainByOrg[$org] = [];
            }
            $mainByOrg[$org][] = $candidate;
        }

        $orgNames = [
            'USC' => 'University Student Council (USC)',
            'CSC' => 'College Student Council (CSC)'
        ];

        foreach ($orgNames as $orgCode => $orgName) {
            echo "<div class='organization-section' style='margin-bottom: 40px;'>";
            echo "<h3 class='org-title' style='color: #4f46e5; margin-bottom: 20px;'>🏛️ {$orgName} Candidates</h3>";

            if (isset($mainByOrg[$orgCode]) && count($mainByOrg[$orgCode]) > 0) {
                echo "<div class='candidates-grid' style='display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;'>";
                foreach ($mainByOrg[$orgCode] as $candidate) {
                    $fullName = trim(($candidate['first_name'] ?? '') . ' ' . ($candidate['middle_name'] ?? '') . ' ' . ($candidate['last_name'] ?? ''));
                    $position = $candidate['position'] ?? 'Position not specified';
                    $college = $candidate['college'] ?? 'N/A';

                    echo "<div class='candidate-card' style='background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>";
                    echo "<div class='candidate-info'>";
                    echo "<h4 style='color: #1f2937; margin-bottom: 10px;'>" . htmlspecialchars($fullName) . "</h4>";
                    echo "<div style='color: #4f46e5; font-weight: 600; margin-bottom: 8px;'>" . htmlspecialchars($position) . "</div>";
                    echo "<div style='color: #6b7280; font-size: 0.9rem;'>College: " . htmlspecialchars($college) . "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                echo "</div>";
            } else {
                echo "<div style='text-align: center; padding: 40px; background: #f9fafb; border-radius: 8px;'>";
                echo "<p style='color: #6b7280;'>No {$orgName} candidates have been accepted yet.</p>";
                echo "</div>";
            }
            echo "</div>";
        }

        // Sub Organization Candidates
        if (count($subCandidates) > 0) {
            // Group by organization
            $subByOrg = [];
            foreach ($subCandidates as $candidate) {
                $org = $candidate['organization'] ?? 'Unknown';
                if (!isset($subByOrg[$org])) {
                    $subByOrg[$org] = [];
                }
                $subByOrg[$org][] = $candidate;
            }

            echo "<div class='organization-section' style='margin-bottom: 40px;'>";
            echo "<h3 class='org-title' style='color: #4f46e5; margin-bottom: 20px;'>🎯 Sub Organization Candidates</h3>";
            
            foreach ($subByOrg as $org => $candidates) {
                echo "<h4 style='color: #6b7280; margin: 20px 0 15px 0;'>" . htmlspecialchars($org) . "</h4>";
                echo "<div class='candidates-grid' style='display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;'>";
                
                foreach ($candidates as $candidate) {
                    $fullName = trim(($candidate['first_name'] ?? '') . ' ' . ($candidate['middle_name'] ?? '') . ' ' . ($candidate['last_name'] ?? ''));
                    $year = $candidate['year'] ?? 'N/A';

                    echo "<div class='candidate-card' style='background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>";
                    echo "<div class='candidate-info'>";
                    echo "<h4 style='color: #1f2937; margin-bottom: 10px;'>" . htmlspecialchars($fullName) . "</h4>";
                    echo "<div style='color: #4f46e5; font-weight: 600; margin-bottom: 8px;'>Representative</div>";
                    echo "<div style='color: #6b7280; font-size: 0.9rem;'>Year: " . htmlspecialchars($year) . "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<div style='text-align: center; padding: 40px; background: #f9fafb; border-radius: 8px;'>";
            echo "<p style='color: #6b7280;'>No sub organization candidates have been accepted yet.</p>";
            echo "</div>";
        }
        ?>
    </div>
</div>
