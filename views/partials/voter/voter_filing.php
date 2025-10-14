<div class="details" id="filingSection" style="padding:20px;">
    <div class="recentOrders">
        <div class="cardHeader">
            <h2>Filing of Candidacy</h2>
        </div>

        <div style="display: flex; gap: 40px; margin-top: 20px;">
            <div style="flex: 1;" id="mainOrgSelectWrapper">
                <label for="mainOrg"><strong>Main Organization</strong></label>
                <select id="mainOrg" name="main_org" onchange="handleMainOrgChange()">
                    <option value="">Select Main Org</option>
                    <option value="USC">USC (University Student Council)</option>
                    <option value="CSC">CSC (College Student Council)</option>
                </select>
            </div>
            <div style="flex: 1;" id="subOrgSelectWrapper">
                <label for="subOrg"><strong>Sub Organization</strong></label>
                <select id="subOrg" name="sub_org" onchange="handleSubOrgChange()">
                    <option value="">Select Sub Org</option>
                    <option value="ACCESS">ACCESS</option>
                    <option value="ASITS">ASITS</option>
                    <option value="BSEMC PromtPT">BSEMC PromtPT</option>
                    <option value="ISSO">ISSO</option>
                    <option value="LISAUX">LISAUX</option>
                    <option value="CICT-womens club">CICT-womens club</option>
                </select>
            </div>
        </div>

        <div id="mainOrgForm" style="display:none; margin-top: 30px;">
            <form method="POST" enctype="multipart/form-data" action="">
                <input type="hidden" name="organization" id="hiddenMainOrg" value="">
                <h3>Main Organization Candidate Details</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                    <div style="flex: 1 1 200px;">
                        <label>Upload 1x1 Picture:</label>
                        <input type="file" name="profile_pic" accept="image/*" required>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Last Name:</label>
                        <input type="text" name="last_name" required>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>First Name:</label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Middle Name:</label>
                        <input type="text" name="middle_name">
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Nickname:</label>
                        <input type="text" name="nickname">
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Age:</label>
                        <input type="text" name="age" id="age" readonly>
                    </div>
                    <div style="flex: 1 1 150px;">
                        <label>Gender:</label>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Date of Birth:</label>
                        <input type="date" name="dob" id="dob" required onchange="calculateAge()">
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>College:</label>
                        <select name="college" id="collegeSelect" required>
                            <option value="">Select College</option>
                            <option value="CICT">College of Information and Communications Technology (CICT)</option>
                            <option value="CBA">College of Business and Accountancy (CBA)</option>
                            <option value="CIT">College of Industrial Technology (CIT)</option>
                            <option value="CHS">College of Health Sciences (CHS)</option>
                            <option value="CEA">College of Engineering and Architecture (CEA)</option>
                            <option value="CHUMMS">College of Humanities and Social Sciences (CHUMMS)</option>
                            <option value="COS">College of Sciences (COS)</option>
                            <option value="CAF">College of Agriculture and Fisheries (CAF)</option>
                            <option value="COED">College of Education (COED)</option>
                        </select>
                    </div>
                    <div style="flex: 1 1 150px;">
                        <label>Year:</label>
                        <input type="number" name="year" min="1" max="5" required>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Program:</label>
                        <select name="program" id="programSelect" required>
                            <option value="">Select Program</option>
                        </select>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Phone:</label>
                        <input type="tel" name="phone" required>
                    </div>
                    <div style="flex: 1 1 300px;">
                        <label>Email Address:</label>
                        <input type="email" name="email" required>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Position Candidating For:</label>
                        <select name="position" required>
                            <option value="">Select position</option>
                            <option value="President">President</option>
                            <option value="Vice President">Vice President</option>
                            <option value="Executive Secretary">Executive Secretary</option>
                            <option value="Finance Secretary">Finance Secretary</option>
                            <option value="Budget Secretary">Budget Secretary</option>
                            <option value="Auditor">Auditor</option>
                            <option value="Public Information Secretary">Public Information Secretary</option>
                            <option value="Property Custodian">Property Custodian</option>
                            <option value="Senators">Senators</option>
                            <option value="Legislators">Legislators</option>
                            <option value="Year Representative">Year Representative</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Partylist:</label>
                        <input type="text" name="partylist">
                    </div>
                    <div style="flex: 1 1 300px;">
                        <label>Permanent Address:</label>
                        <textarea name="permanent_address" rows="2" required></textarea>
                    </div>
                    <div style="flex: 1 1 300px;">
                        <label>Temporary Address:</label>
                        <textarea name="temporary_address" rows="2" required></textarea>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Period of Residency:</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" name="residency_years" min="0" max="10" placeholder="Years" required style="flex:1;">
                            <input type="number" name="residency_semesters" min="0" max="20" placeholder="Semesters" required style="flex:1;">
                        </div>
                        <small style="color: #6b7280;">Enter how many years and semesters you have studied in the university</small>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Semester and Year in Current College and University:</label>
                        <input type="text" name="semester_year" required>
                    </div>
                    <div style="flex: 1 1 300px;">
                        <label>Upload Certificate of Candidacy:</label><br>
                        <a href="files/COMELEC-FORM-NO.-1.docx" target="_blank" style="color: blue; text-decoration: underline;">
                            📄 Download Certificate of Candidacy (PDF)
                        </a><br><br>
                        <input type="file" name="CertificateofCandidacy" accept=".jpg, .jpeg" required>
                    </div>
                    <div style="flex: 1 1 300px;">
                        <label>Upload COMELEC Form 1:</label><br>
                        <a href="files/COMELEC-FORM-NO.-1.docx" target="_blank" style="color: blue; text-decoration: underline;">
                            📄 Download COMELEC Form 1 (PDF)
                        </a><br><br>
                        <input type="file" name="comelec_form_1" accept=".jpg, .jpeg" required>
                    </div>
                    <div style="flex: 1 1 300px;">
                        <label>Upload Certificate of Recommendation:</label><br>
                        <a href="files/Certificate-of-Recommendation.docx" target="_blank" style="color: blue; text-decoration: underline;">
                            📄 Download Certificate of Recommendation (PDF)
                        </a><br><br>
                        <input type="file" name="CertificateOfRecommendation" accept=".jpg, .jpeg" required>
                    </div>
                    <div style="flex: 1 1 300px;">
                        <label>Upload Photocopy of Prospectus:</label>
                        <input type="file" name="prospectus" accept="image/*,application/pdf" required>
                    </div>
                    <div style="flex: 1 1 300px;">
                        <label>Upload Clearance:</label>
                        <input type="file" name="clearance" accept="image/*,application/pdf" required>
                    </div>
                    <div style="flex: 1 1 300px;">
                        <label>Upload Photocopy of COE:</label>
                        <input type="file" name="coe" accept="image/*,application/pdf" required>
                    </div>
                </div>
                <button type="submit" name="submitFiling" class="btn" style="margin-top: 20px;">Submit Filing</button>
            </form>
        </div>
        <div id="subOrgForm" style="display:none; margin-top: 30px;">
            <form method="POST" enctype="multipart/form-data" action="">
                <input type="hidden" name="organization" id="hiddenSubOrg" value="">
                <h3>Sub Organization Candidate Details</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                    <div style="flex: 1 1 200px;">
                        <label>Last Name:</label>
                        <input type="text" name="last_name_sub" required>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>First Name:</label>
                        <input type="text" name="first_name_sub" required>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Position Candidating For:</label>
                        <select name="position_sub" required>
                            <option value="">Select position</option>
                            <option value="Representative">Representative</option>
                            <option value="President">President</option>
                            <option value="Vice President">Vice President</option>
                            <option value="Secretary">Secretary</option>
                            <option value="Treasurer">Treasurer</option>
                            <option value="Auditor">Auditor</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div style="flex: 1 1 200px;">
                        <label>Middle Name:</label>
                        <input type="text" name="middle_name_sub">
                    </div>
                    <div style="flex: 1 1 150px;">
                        <label>Year:</label>
                        <input type="number" name="year_sub" min="1" max="4" required>
                    </div>
                    <div style="flex: 1 1 300px;">
                        <label>Address:</label>
                        <textarea name="block_address_sub" rows="2" required></textarea>
                    </div>
                </div>
                <button type="submit" name="submitFilingSub" class="btn" style="margin-top: 20px;">Submit Filing</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Map of colleges and their programs for the Main Org form
    const programs = {
        'CICT': ['BS Information Technology (BSIT)', 'BS Computer Science (BSCS)', 'BS Information System (BSIS)'],
        'CBA': ['BS Business Administration (BSBA)', 'BS Accountancy (BSA)'],
        // Add all other colleges and their programs here
        'CIT': ['BSEd Industrial Technology', 'BTTE'],
        'CHS': ['BS Nursing', 'BS Pharmacy'],
        'CEA': ['BS Civil Engineering', 'BS Electrical Engineering'],
        'CHUMMS': ['AB English', 'BS Psychology'],
        'COS': ['BS Biology', 'BS Chemistry'],
        'CAF': ['BS Agriculture', 'BS Fisheries'],
        'COED': ['BSEd', 'BEEd']
    };

    function calculateAge() {
        const dobInput = document.getElementById('dob').value;
        if (dobInput) {
            const birthDate = new Date(dobInput);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            document.getElementById('age').value = age;
        }
    }

    function handleMainOrgChange() {
        const mainOrgSelect = document.getElementById('mainOrg');
        const subOrgSelect = document.getElementById('subOrg');
        const mainOrgForm = document.getElementById('mainOrgForm');
        const subOrgForm = document.getElementById('subOrgForm');
        const hiddenMainOrg = document.getElementById('hiddenMainOrg');
        const selectedValue = mainOrgSelect.value;

        // Reset and hide Sub Org form
        subOrgSelect.value = '';
        subOrgForm.style.display = 'none';

        if (selectedValue) {
            mainOrgForm.style.display = 'block';
            hiddenMainOrg.value = selectedValue;
        } else {
            mainOrgForm.style.display = 'none';
            hiddenMainOrg.value = '';
        }
    }

    function handleSubOrgChange() {
        const mainOrgSelect = document.getElementById('mainOrg');
        const subOrgSelect = document.getElementById('subOrg');
        const mainOrgForm = document.getElementById('mainOrgForm');
        const subOrgForm = document.getElementById('subOrgForm');
        const hiddenSubOrg = document.getElementById('hiddenSubOrg');
        const selectedValue = subOrgSelect.value;

        // Reset and hide Main Org form
        mainOrgSelect.value = '';
        mainOrgForm.style.display = 'none';

        if (selectedValue) {
            subOrgForm.style.display = 'block';
            hiddenSubOrg.value = selectedValue;
        } else {
            subOrgForm.style.display = 'none';
            hiddenSubOrg.value = '';
        }
    }
    
    // Logic to update Program options based on College selection
    document.getElementById('collegeSelect').addEventListener('change', function() {
        const college = this.value;
        const programSelect = document.getElementById('programSelect');
        
        // Clear previous options
        programSelect.innerHTML = '<option value="">Select Program</option>';

        if (programs[college]) {
            programs[college].forEach(program => {
                const option = document.createElement('option');
                option.value = program;
                option.textContent = program;
                programSelect.appendChild(option);
            });
        }
    });

</script>
