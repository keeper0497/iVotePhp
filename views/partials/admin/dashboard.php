<div id="dashboardSection">
    <div class="cardBox">
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['total_voters']) ?></div>
                <div class="cardName">Total registered voters</div>
            </div>
            <div class="iconBx">
                <ion-icon name="people-outline"></ion-icon>
            </div>
        </div>
        
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['total_candidates']) ?></div>
                <div class="cardName">Total of candidates</div>
            </div>
            <div class="iconBx">
                <ion-icon name="people-outline"></ion-icon>
            </div>
        </div>
        
        <div class="card">
            <div>
                <div class="numbers"><?= number_format($dashboardStats['students_voted']) ?></div>
                <div class="cardName">Students who voted</div>
            </div>
            <div class="iconBx">
                <ion-icon name="people-outline"></ion-icon>
            </div>
        </div>
        
        <div class="card">
            <div>
                <div class="numbers"><?= $dashboardStats['voting_percentage'] ?>%</div>
                <div class="cardName">Voting Percentage</div>
            </div>
            <div class="iconBx">
                <ion-icon name="analytics-outline"></ion-icon>
            </div>
        </div>
    </div>

    <!-- Vote Tally Section -->
    <div style="max-width: 800px; margin: 40px auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #4f46e5; margin-bottom: 30px;">Vote Tally by Organization</h2>
        
        <!-- Organization Selector -->
        <div style="max-width: 400px; margin: 0 auto 30px;">
            <label for="adminOrgSelect" style="font-weight: bold; font-size: 1.1rem; display: block; margin-bottom: 10px;">
                Select Organization:
            </label>
            <select id="adminOrgSelect" style="width: 100%; padding: 12px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 1rem;">
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
        <div id="adminTallyContainer">
            <p style="text-align: center; color: #6b7280; font-style: italic;">
                Select an organization to view vote tally
            </p>
        </div>
    </div>
</div>