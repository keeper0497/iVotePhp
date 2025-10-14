// Voter Panel JavaScript

// =============== SECTION MANAGEMENT ===============
const sections = {
    dashboard: document.getElementById('dashboardSection'),
    candidates: document.getElementById('candidatesSection'),
    filing: document.getElementById('filingSection'),
    votes: document.getElementById('votesSection')
};

function showSection(sectionName) {
    console.log('Showing section:', sectionName);
    
    // Hide all sections
    Object.values(sections).forEach(section => {
        if (section) section.style.display = 'none';
    });
    
    // Hide modals
    const rulesModal = document.getElementById('rulesModal');
    if (rulesModal) rulesModal.style.display = 'none';
    
    // Show selected section
    if (sections[sectionName]) {
        sections[sectionName].style.display = 'block';
    }
}

// Navigation event listeners
document.getElementById('dashboardBtn')?.addEventListener('click', (e) => {
    e.preventDefault();
    showSection('dashboard');
});

document.getElementById('candidatesBtn')?.addEventListener('click', (e) => {
    e.preventDefault();
    showSection('candidates');
});

document.getElementById('filingBtn')?.addEventListener('click', (e) => {
    e.preventDefault();
    showSection('filing');
});

document.getElementById('votesBtn')?.addEventListener('click', (e) => {
    e.preventDefault();
    showSection('votes');
});

document.getElementById('rulesBtn')?.addEventListener('click', (e) => {
    e.preventDefault();
    const rulesModal = document.getElementById('rulesModal');
    if (rulesModal) rulesModal.style.display = 'block';
});

// =============== NOTIFICATION DROPDOWN ===============
document.getElementById('notificationBell')?.addEventListener('click', function(e) {
    e.preventDefault();
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
});

// Close dropdown when clicking outside
window.addEventListener('click', function(e) {
    const dropdown = document.getElementById('notificationDropdown');
    const bell = document.getElementById('notificationBell');
    if (dropdown && !dropdown.contains(e.target) && !bell.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});

// =============== MODALS ===============
function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'none';
    
    // Save "don't show again" preference
    const dontShowCheckbox = document.getElementById('dontShowAgain');
    if (dontShowCheckbox && dontShowCheckbox.checked) {
        localStorage.setItem('hideRules', 'true');
    }
}

// =============== FILING FORM MANAGEMENT ===============
const mainOrgSelect = document.getElementById('mainOrg');
const subOrgSelect = document.getElementById('subOrg');
const mainOrgForm = document.getElementById('mainOrgForm');
const subOrgForm = document.getElementById('subOrgForm');

function handleMainOrgChange() {
    const mainOrg = document.getElementById('mainOrg');
    const hiddenMainOrg = document.getElementById('hiddenMainOrg');
    const mainOrgForm = document.getElementById('mainOrgForm');
    const subOrgForm = document.getElementById('subOrgForm');
    const subOrgWrapper = document.getElementById('subOrgSelectWrapper');
    const subOrg = document.getElementById('subOrg');
    const hiddenSubOrg = document.getElementById('hiddenSubOrg');
    
    if (mainOrg && mainOrg.value) {
        if (hiddenMainOrg) hiddenMainOrg.value = mainOrg.value;
        if (mainOrgForm) mainOrgForm.style.display = 'block';
        if (subOrgForm) subOrgForm.style.display = 'none';
        if (subOrgWrapper) subOrgWrapper.style.display = 'none';
        if (subOrg) subOrg.value = '';
        if (hiddenSubOrg) hiddenSubOrg.value = '';
    } else {
        if (mainOrgForm) mainOrgForm.style.display = 'none';
        if (subOrgWrapper) subOrgWrapper.style.display = 'block';
    }
}

function handleSubOrgChange() {
    const subOrg = document.getElementById('subOrg');
    const hiddenSubOrg = document.getElementById('hiddenSubOrg');
    const subOrgForm = document.getElementById('subOrgForm');
    const mainOrgForm = document.getElementById('mainOrgForm');
    const mainOrgWrapper = document.getElementById('mainOrgSelectWrapper');
    const mainOrg = document.getElementById('mainOrg');
    const hiddenMainOrg = document.getElementById('hiddenMainOrg');
    
    if (subOrg && subOrg.value) {
        if (hiddenSubOrg) hiddenSubOrg.value = subOrg.value;
        if (subOrgForm) subOrgForm.style.display = 'block';
        if (mainOrgForm) mainOrgForm.style.display = 'none';
        if (mainOrgWrapper) mainOrgWrapper.style.display = 'none';
        if (mainOrg) mainOrg.value = '';
        if (hiddenMainOrg) hiddenMainOrg.value = '';
    } else {
        if (subOrgForm) subOrgForm.style.display = 'none';
        if (mainOrgWrapper) mainOrgWrapper.style.display = 'block';
    }
}

// =============== AGE CALCULATOR ===============
function calculateAge() {
    const dobInput = document.getElementById('dob');
    const ageInput = document.getElementById('age');
    
    if (dobInput && ageInput && dobInput.value) {
        const today = new Date();
        const birthDate = new Date(dobInput.value);
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        ageInput.value = age;
    }
}

// =============== COLLEGE-PROGRAM MAPPING ===============
const collegePrograms = {
    'CICT': [
        'Bachelor of Science in Information Systems',
        'Bachelor of Science in Information Technology',
        'Bachelor of Science in Computer Science',
        'Bachelor of Science in Entertainment and Multimedia Computing - Game Development',
        'Bachelor of Science in Entertainment and Multimedia Computing - Digital Animation',
        'Bachelor of Library and Information Science'
    ],
    'CBA': [
        'Bachelor of Science in Accountancy',
        'Bachelor of Science in Accounting Information System',
        'Bachelor of Science in Business Administration - Financial Management',
        'Bachelor of Science in Business Administration - Human Resource Development Management',
        'Bachelor of Science in Business Administration - Management',
        'Bachelor of Science in Business Administration - Marketing Management',
        'Bachelor of Science in Entrepreneurship',
        'Bachelor of Science in Internal Auditing',
        'Bachelor of Science in Office Administration'
    ],
    // Add other colleges as needed...
};

const collegeSelect = document.getElementById('collegeSelect');
const programSelect = document.getElementById('programSelect');

if (collegeSelect && programSelect) {
    collegeSelect.addEventListener('change', function() {
        const selectedCollege = this.value;
        programSelect.options.length = 1; // Keep first option
        
        if (selectedCollege && collegePrograms[selectedCollege]) {
            collegePrograms[selectedCollege].forEach(program => {
                const option = document.createElement('option');
                option.value = program;
                option.textContent = program;
                programSelect.appendChild(option);
            });
        }
    });
}

// =============== VOTE SUBMISSION ===============
// ============================================
// OPTIONAL FIX 3: Update voter.js to ensure button value is sent
// ============================================

// Find the voting form submission handler and update it:

const votingForm = document.getElementById('votingForm');
if (votingForm) {
    votingForm.addEventListener('submit', function(e) {
        const radios = votingForm.querySelectorAll('input[type="radio"]:checked');
        
        if (radios.length === 0) {
            e.preventDefault();
            alert('Please select at least one candidate before submitting your vote.');
            return false;
        }
        
        if (!confirm('Are you sure you want to submit your vote?\n\nThis action is FINAL and cannot be undone.')) {
            e.preventDefault();
            return false;
        }
        
        // Don't prevent default or disable button - let form submit normally
        const btn = votingForm.querySelector('button[name="submitVote"]');
        if (btn) {
            btn.innerHTML = 'Submitting...';
        }
        
        // Allow form to submit normally
        return true;
    });
}

// =============== VOTE TALLY ===============
document.getElementById('orgSelect')?.addEventListener('change', function() {
    const org = this.value;
    const container = document.getElementById('orgCandidatesContainer');
    
    if (!org) {
        container.innerHTML = '<p style="text-align: center; color: #6b7280; font-style: italic;">Select an organization to view vote tally</p>';
        return;
    }

    container.innerHTML = '<p style="text-align: center;">Loading vote tally...</p>';

    fetch('api/fetch_org_candidates.php?organization=' + encodeURIComponent(org))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                container.innerHTML = '<p style="color:red; text-align: center;">Error: ' + data.error + '</p>';
                return;
            }
            
            if (!data.candidates || data.candidates.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #6b7280;">No accepted candidates found for ' + org + '.</p>';
                return;
            }

            const positionOrder = [
                'President', 'Vice President', 'Executive Secretary', 'Finance Secretary',
                'Budget Secretary', 'Auditor', 'Public Information Secretary',
                'Property Custodian', 'Senators', 'Legislators', 'Year Representative',
                'Representative', 'Other'
            ];

            const sortedCandidates = data.candidates.sort((a, b) => {
                const posA = positionOrder.indexOf(a.position) !== -1 ? positionOrder.indexOf(a.position) : positionOrder.length;
                const posB = positionOrder.indexOf(b.position) !== -1 ? positionOrder.indexOf(b.position) : positionOrder.length;
                return posA - posB;
            });

            let html = `<h3 style="text-align: center; margin-bottom: 20px; color: #1f2937;">Vote Tally for ${org}</h3>`;
            html += `<table><thead><tr><th>Candidate Name</th><th>Position</th><th style="text-align: center;">Total Votes</th></tr></thead><tbody>`;

            sortedCandidates.forEach(c => {
                const fullName = [c.first_name, c.middle_name, c.last_name].filter(Boolean).join(' ');
                const position = c.position || 'Representative';
                html += `<tr><td><strong>${fullName}</strong></td><td>${position}</td><td style="text-align: center;">${c.total_votes || 0}</td></tr>`;
            });

            html += `</tbody></table>`;
            
            const totalVotes = sortedCandidates.reduce((sum, c) => sum + (parseInt(c.total_votes) || 0), 0);
            html += `<div style="margin-top: 20px; padding: 15px; background: #f0f9ff; border-left: 4px solid #0ea5e9; border-radius: 6px;">`;
            html += `<p style="margin: 0; color: #0c4a6e;"><strong>Total Votes Cast for ${org}:</strong> ${totalVotes}</p></div>`;

            container.innerHTML = html;
        })
        .catch(err => {
            container.innerHTML = '<p style="color: red; text-align: center;">Error loading vote tally.</p>';
            console.error(err);
        });
});

// =============== SIDEBAR TOGGLE ===============
const toggle = document.querySelector('.toggle');
const navigation = document.querySelector('.navigation');
const main = document.querySelector('.main');

if (toggle) {
    toggle.addEventListener('click', () => {
        navigation?.classList.toggle('active');
        main?.classList.toggle('active');
    });
}

// =============== INITIALIZATION ===============
window.onload = function() {
    // Show rules modal on first visit
    if (!localStorage.getItem('hideRules')) {
        const rulesModal = document.getElementById('rulesModal');
        if (rulesModal) rulesModal.style.display = 'block';
    }
    
    // Show dashboard by default
    showSection('dashboard');
};