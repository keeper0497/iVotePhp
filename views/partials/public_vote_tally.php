<?php
// This file contains the public vote tally interface - FIXED VERSION
// It's included in home.php and doesn't require authentication
?>
<style>
/* Additional styles for vote tally section */
.stats-section {
  max-width: 1200px; 
  margin: 40px auto; 
  padding: 0 20px;
}
.cardBox {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}
.card {
  background: linear-gradient(160deg, rgba(6,182,212,.15), rgba(34,197,94,.10));
  border: 1px solid rgba(148,163,184,.2);
  border-radius: var(--radius);
  padding: 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: var(--shadow);
}
.card .numbers {
  font-size: 2rem;
  font-weight: 800;
  color: var(--brand);
}
.card .cardName {
  font-size: 0.9rem;
  color: var(--muted);
  margin-top: 5px;
}
.card .iconBx {
  font-size: 3rem;
  color: rgba(34,211,238,.5);
}

/* Vote tally section */
.vote-tally-section {
  max-width: 1000px; 
  margin: 40px auto; 
  background: linear-gradient(160deg, rgba(6,182,212,.10), rgba(34,197,94,.08));
  border: 1px solid rgba(148,163,184,.2);
  border-radius: var(--radius); 
  padding: 30px; 
  box-shadow: var(--shadow);
}
.vote-tally-title {
  text-align: center; 
  color: var(--brand); 
  margin-bottom: 30px;
  font-size: 1.8rem;
}
.org-selector {
  max-width: 400px; 
  margin: 0 auto 30px;
}
.org-selector label {
  font-weight: bold; 
  font-size: 1.1rem; 
  display: block; 
  margin-bottom: 10px; 
  color: #cbd5e1;
}
.org-selector select {
  width: 100%; 
  padding: 12px; 
  border: 2px solid rgba(148,163,184,.28); 
  border-radius: 12px; 
  font-size: 1rem; 
  cursor: pointer; 
  transition: border-color 0.3s;
  background: rgba(2,6,23,.55);
  color: var(--text);
}
.tally-table {
  width: 100%; 
  border-collapse: collapse;
  margin-top: 20px;
}
.tally-table th,
.tally-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid rgba(148,163,184,.2);
}
.tally-table th {
  background: rgba(255,255,255,.05);
  font-weight: bold;
  color: var(--brand);
}
.tally-table tbody tr:hover {
  background: rgba(34,211,238,.05);
}
.vote-count {
  text-align: center; 
  font-weight: bold; 
  color: var(--accent);
}
.status-alert {
  padding: 15px; 
  border-radius: 8px; 
  margin-bottom: 20px;
}
.status-open {
  background: #fef3c7; 
  border-left: 4px solid #f59e0b;
  color: #92400e;
}
.status-closed {
  background: #d1fae5; 
  border-left: 4px solid #059669;
  color: #065f46;
}
.status-waiting {
  background: #fee2e2;
  border-left: 4px solid #dc2626;
  color: #991b1b;
}

@media (max-width: 768px) {
  .cardBox {
    grid-template-columns: 1fr;
  }
}
</style>

<!-- Election Statistics Section -->
<section class="stats-section" id="stats">
    <div class="cardBox">
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['total_voters']) ?></div>
                <div class="cardName">Total registered voters</div>
            </div>
            <div class="iconBx">
                👥
            </div>
        </div>
        
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['total_candidates']) ?></div>
                <div class="cardName">Total of candidates</div>
            </div>
            <div class="iconBx">
                🏛️
            </div>
        </div>
        
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['students_voted']) ?></div>
                <div class="cardName">Students who voted</div>
            </div>
            <div class="iconBx">
                ✅
            </div>
        </div>
        
        <div class="card">
            <div>
                <div class="numbers"><?= $dashboardStats['voting_percentage'] ?>%</div>
                <div class="cardName">Voting Percentage</div>
            </div>
            <div class="iconBx">
                📊
            </div>
        </div>
    </div>
</section>

<!-- Vote Tally Section -->
<section class="vote-tally-section" id="tally">
    <h2 class="vote-tally-title">📊 Real-Time Vote Tally</h2>
    
    <!-- Voting Status Alert -->
    <?php 
    $isVotingClosed = ($votingSchedule && $votingSchedule['status'] === 'closed');
    ?>
    
    <?php if (!$canShowResults): ?>
      <div class="status-alert status-waiting">
        <div style="display: flex; align-items: center; gap: 10px;">
          <span style="font-size: 1.5rem;">⏳</span>
          <div>
            <p style="margin: 0; font-weight: bold;">
              🔴 Voting is currently in progress
            </p>
            <p style="margin: 5px 0 0 0; font-size: 0.9rem;">
              Results will be displayed once voting has officially ended.
            </p>
          </div>
        </div>
      </div>
    <?php elseif ($isVotingClosed): ?>
      <div class="status-alert status-closed">
        <div style="display: flex; align-items: center; gap: 10px;">
          <span style="font-size: 1.5rem;">✅</span>
          <p style="margin: 0; font-weight: bold;">
            ✓ Voting is <strong>CLOSED</strong> - Final results are displayed below
          </p>
        </div>
      </div>
    <?php else: ?>
      <div class="status-alert status-open">
        <div style="display: flex; align-items: center; gap: 10px;">
          <span style="font-size: 1.5rem;">🔴</span>
          <div>
            <p style="margin: 0; font-weight: bold;">
              🔴 Voting is currently <strong>OPEN</strong> - Results are updating in real-time
            </p>
            <p style="margin: 5px 0 0 0; font-size: 0.9rem;">
              Note: Results may change as more votes are cast.
            </p>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($canShowResults): ?>
      <!-- Organization Selector -->
      <div class="org-selector">
        <label for="publicOrgSelect">Select Organization:</label>
        <select id="publicOrgSelect">
          <option value="">-- Select Organization --</option>
          <optgroup label="Main Organizations">
            <option value="USC">USC (University Student Council)</option>
            <option value="CSC">CSC (College Student Council)</option>
          </optgroup>
          <optgroup label="Sub Organizations">
            <option value="ACCESS">ACCESS</option>
            <option value="ASITS">ASITS</option>
            <option value="BSEMC PromtPT">BSEMC PromtPT</option>
            <option value="ISSO">ISSO</option>
            <option value="LISAUX">LISAUX</option>
            <option value="CICT-womens club">CICT-womens club</option>
          </optgroup>
        </select>
      </div>

      <!-- Candidates Tally Container -->
      <div id="publicTallyContainer">
        <p style="text-align: center; color: #6b7280; font-style: italic; padding: 40px;">
          <span style="font-size: 3rem; color: #9ca3af; display: block; margin: 0 auto 15px;">📊</span>
          Select an organization to view vote tally
        </p>
      </div>
    <?php endif; ?>
</section>

<script>
// FIXED Public Vote Tally JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Public vote tally script loaded');
    
    const orgSelect = document.getElementById('publicOrgSelect');
    const container = document.getElementById('publicTallyContainer');
    
    if (!orgSelect) {
        console.error('❌ publicOrgSelect element not found');
        return;
    }
    
    if (!container) {
        console.error('❌ publicTallyContainer element not found');
        return;
    }
    
    console.log('✅ Elements found, setting up event listener');
    
    orgSelect.addEventListener('change', function() {
        const org = this.value;
        console.log('📋 Organization selected:', org);
        
        if (!org) {
            container.innerHTML = '<p style="text-align: center; color: #6b7280; font-style: italic; padding: 40px;"><span style="font-size: 3rem; color: #9ca3af; display: block; margin: 0 auto 15px;">📊</span>Select an organization to view vote tally</p>';
            return;
        }

        // Show loading state
        container.innerHTML = '<p style="text-align: center; color: #6b7280; padding: 40px;">📥 Loading vote tally for ' + org + '...</p>';

        const apiUrl = 'api/public_fetch_org_candidates.php?organization=' + encodeURIComponent(org);
        console.log('🌐 Fetching from:', apiUrl);

        fetch(apiUrl)
            .then(response => {
                console.log('📡 Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📊 API Response:', data);
                
                if (data.error) {
                    console.warn('⚠️ API returned error:', data.error);
                    
                    if (data.voting_status === 'open') {
                        container.innerHTML = `
                            <div style="text-align: center; padding: 40px; background: rgba(251, 191, 36, 0.1); border-radius: 12px; border: 1px solid rgba(251, 191, 36, 0.3);">
                                <span style="font-size: 3rem; color: #f59e0b;">⏳</span>
                                <h3 style="color: #d97706; margin: 20px 0 10px 0;">Results Not Yet Available</h3>
                                <p style="color: #92400e;">${data.message || data.error}</p>
                            </div>
                        `;
                    } else {
                        container.innerHTML = `<p style="color: #ef4444; text-align: center;">❌ Error: ${data.error}</p>`;
                    }
                    return;
                }
                
                if (!data.candidates || data.candidates.length === 0) {
                    console.info('ℹ️ No candidates found for', org);
                    container.innerHTML = `<p style="text-align: center; color: #6b7280; padding: 40px;">No accepted candidates found for ${org}.</p>`;
                    return;
                }

                console.log(`✅ Found ${data.candidates.length} candidates for ${org}`);

                // Position order for sorting
                const positionOrder = [
                    'President', 'Vice President', 'Executive Secretary', 'Finance Secretary',
                    'Budget Secretary', 'Auditor', 'Public Information Secretary',
                    'Property Custodian', 'Senators', 'Legislators', 'Year Representative',
                    'Representative', 'Other'
                ];

                // Sort candidates by position, then by vote count
                const sortedCandidates = data.candidates.sort((a, b) => {
                    const posA = positionOrder.indexOf(a.position) !== -1 ? positionOrder.indexOf(a.position) : positionOrder.length;
                    const posB = positionOrder.indexOf(b.position) !== -1 ? positionOrder.indexOf(b.position) : positionOrder.length;
                    
                    if (posA !== posB) {
                        return posA - posB; // Sort by position first
                    }
                    
                    // If same position, sort by vote count (descending)
                    return parseInt(b.total_votes || 0) - parseInt(a.total_votes || 0);
                });

                // Build the HTML table
                let html = `
                    <h3 style="text-align: center; margin-bottom: 20px; color: var(--brand); font-size: 1.3rem;">
                        🗳️ Vote Tally for ${org}
                    </h3>
                    <table class="tally-table">
                        <thead>
                            <tr>
                                <th>Candidate Name</th>
                                <th>Position</th>
                                <th style="text-align: center;">Total Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                sortedCandidates.forEach(candidate => {
                    // Build full name, handling possible null/undefined values
                    const nameParts = [
                        candidate.first_name,
                        candidate.middle_name,
                        candidate.last_name
                    ].filter(part => part && part.trim() !== '');
                    
                    const fullName = nameParts.join(' ');
                    const position = candidate.position || 'Representative';
                    const votes = parseInt(candidate.total_votes) || 0;
                    
                    console.log(`👤 ${fullName} (${position}) - ${votes} votes`);
                    
                    html += `
                        <tr>
                            <td><strong>${fullName}</strong></td>
                            <td>${position}</td>
                            <td class="vote-count">${votes}</td>
                        </tr>
                    `;
                });

                html += `</tbody></table>`;
                
                // Calculate total votes for this organization
                const totalVotes = sortedCandidates.reduce((sum, candidate) => {
                    return sum + (parseInt(candidate.total_votes) || 0);
                }, 0);
                
                html += `
                    <div style="margin-top: 20px; padding: 15px; background: rgba(34, 211, 238, 0.1); border-left: 4px solid var(--brand); border-radius: 6px;">
                        <p style="margin: 0; color: var(--brand); font-weight: bold;">
                            📊 Total Votes Cast for ${org}: ${totalVotes}
                        </p>
                    </div>
                `;

                // Update the container
                container.innerHTML = html;
                console.log('✅ Vote tally displayed successfully');
            })
            .catch(err => {
                console.error('❌ Fetch error:', err);
                container.innerHTML = `
                    <div style="color: #ef4444; text-align: center; padding: 20px;">
                        <p><strong>❌ Error loading vote tally</strong></p>
                        <p style="font-size: 0.9rem;">Please check the browser console for details.</p>
                        <p style="font-size: 0.8rem; color: #999;">Error: ${err.message}</p>
                    </div>
                `;
            });
    });
});
</script>