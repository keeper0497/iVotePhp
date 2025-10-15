// Main JavaScript for Admin Panel

// =============== NAVIGATION & SECTION MANAGEMENT ===============
const sections = {
    dashboard: document.getElementById("dashboardSection"),
    userManagement: document.getElementById("userManagementSection"),
    filing: document.getElementById("filingSection"),
    voters: document.getElementById("votersSection"),
    votingSchedule: document.getElementById("votingScheduleSection"),
    reporting: document.getElementById("reportingSection")
};

function showSection(sectionName) {
    // Hide all sections
    Object.values(sections).forEach(section => {
        if (section) section.style.display = "none";
    });
    
    // Show selected section
    if (sections[sectionName]) {
        sections[sectionName].style.display = "block";
    }
}

// Navigation button event listeners
document.getElementById("dashboardBtn")?.addEventListener("click", (e) => {
    e.preventDefault();
    showSection("dashboard");
});

document.getElementById("userMgmtBtn")?.addEventListener("click", (e) => {
    e.preventDefault();
    showSection("userManagement");
});

document.getElementById("filingBtn")?.addEventListener("click", (e) => {
    e.preventDefault();
    showSection("filing");
});

document.getElementById("votersBtn")?.addEventListener("click", (e) => {
    e.preventDefault();
    showSection("voters");
});

document.getElementById("votingScheduleBtn")?.addEventListener("click", (e) => {
    e.preventDefault();
    showSection("votingSchedule");
});

document.getElementById("reportingBtn")?.addEventListener("click", (e) => {
    e.preventDefault();
    showSection("reporting");
});

// Initialize with dashboard view
showSection('dashboard');

// =============== MODAL MANAGEMENT ===============
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = "block";
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = "none";
}

// Close modals when clicking outside
window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal');
    for (let modal of modals) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
}

// =============== USER MANAGEMENT ===============
function editUser(id, email, student_id, role, college) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_student_id').value = student_id;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_college').value = college;
    openModal('editModal');
}

// =============== FILING MANAGEMENT ===============
function openPreviewModal(id, name, org, position, status, profile = '', form1 = '', rec = '', prospectus = '', clearance = '', coe = '', type = 'main', block = '', comment = '') {
    let content = `
        <div style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe); padding: 20px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid #4f46e5;">
            <p><strong>Name:</strong> ${name}</p>
            <p><strong>Organization:</strong> ${org}</p>
            <p><strong>Position/Year:</strong> ${position}</p>
            ${block ? `<p><strong>Block Address:</strong> ${block}</p>` : ""}
            <p><strong>Current Status:</strong> <span class="status ${status}">${status}</span></p>
        </div>
    `;

    if (type === "main") {
        content += `<p style="font-weight: 600; color: #1e293b; font-size: 1.1rem; margin: 20px 0 15px 0;">📁 Submitted Documents:</p><ul>`;
        
        if (profile) content += `<li><a href="#" onclick="showImage('${profile}', 'Profile Picture'); return false;">View Profile Picture</a></li>`;
        if (form1) content += `<li><a href="#" onclick="showImage('${form1}', 'COMELEC Form 1'); return false;">View COMELEC Form 1</a></li>`;
        if (rec) content += `<li><a href="#" onclick="showImage('${rec}', 'Recommendation Letter'); return false;">View Recommendation Letter</a></li>`;
        if (prospectus) content += `<li><a href="#" onclick="showImage('${prospectus}', 'Prospectus'); return false;">View Prospectus</a></li>`;
        if (clearance) content += `<li><a href="#" onclick="showImage('${clearance}', 'Clearance'); return false;">View Clearance</a></li>`;
        if (coe) content += `<li><a href="#" onclick="showImage('${coe}', 'Certificate of Enrollment'); return false;">View Certificate of Enrollment</a></li>`;
        
        content += `</ul>`;
    }

    if(comment) {
        content += `
            <div style="background: #fef3c7; border: 2px solid #f59e0b; border-radius: 12px; padding: 15px; margin-top: 20px;">
                <p style="font-weight: 600; color: #92400e; margin-bottom: 10px;">
                    <ion-icon name="warning" style="vertical-align: middle; font-size: 1.2rem;"></ion-icon>
                    Previous Admin Comment:
                </p>
                <p style="white-space: pre-wrap; background: white; padding: 15px; border-radius: 8px; color: #1e293b; margin: 0;">${comment}</p>
            </div>
        `;
    }

    document.getElementById("previewContent").innerHTML = content;
    document.getElementById("preview_filing_id").value = id;
    document.getElementById("preview_type").value = type;
    document.getElementById("comment").value = comment || '';
    openModal("previewModal");
}

function showImage(imagePath, fileName) {
    imagePath = imagePath.trim();
    document.getElementById('previewImage').src = imagePath;
    document.getElementById('imageFileName').textContent = fileName;
    openModal('imagePreviewModal');
}

function closeImagePreview(event) {
    if (event.target.id === 'imagePreviewModal') {
        closeModal('imagePreviewModal');
    }
}

function setAction(act) {
    document.getElementById("preview_action").value = act;
}

// =============== VOTING SCHEDULE ===============
function emergencyClose() {
    if (confirm("Are you sure you want to immediately close voting? This action will stop all ongoing voting.")) {
        const now = new Date();
        const startDate = now.toISOString().slice(0, 16);
        // Set end date to 1 minute after start date to satisfy validation
        const endDate = new Date(now.getTime() + 60000).toISOString().slice(0, 16);
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="voting_status" value="closed">
            <input type="hidden" name="start_date" value="${startDate}">
            <input type="hidden" name="end_date" value="${endDate}">
            <input type="hidden" name="description" value="Emergency closure by admin">
            <input type="hidden" name="updateVotingSchedule" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Date validation
const startDateInput = document.getElementById('start_date');
const endDateInput = document.getElementById('end_date');

if (startDateInput) {
    startDateInput.addEventListener('change', function() {
        const startDate = new Date(this.value);
        const endDate = new Date(endDateInput.value);
        
        if (endDate <= startDate) {
            const newEndDate = new Date(startDate.getTime() + (60 * 60 * 1000));
            endDateInput.value = newEndDate.toISOString().slice(0, 16);
        }
    });
}

if (endDateInput) {
    endDateInput.addEventListener('change', function() {
        const endDate = new Date(this.value);
        const startDate = new Date(startDateInput.value);
        
        if (endDate <= startDate) {
            alert('End date must be after start date');
            const newEndDate = new Date(startDate.getTime() + (60 * 60 * 1000));
            this.value = newEndDate.toISOString().slice(0, 16);
        }
    });
}

// =============== VOTE TALLY ===============
document.getElementById('adminOrgSelect')?.addEventListener('change', function() {
    const org = this.value;
    const container = document.getElementById('adminTallyContainer');
    
    if (!org) {
        container.innerHTML = '<p style="text-align: center; color: #6b7280; font-style: italic;">Select an organization to view vote tally</p>';
        return;
    }

    container.innerHTML = '<p style="text-align: center;">Loading vote tally...</p>';

    const positionOrder = [
        "President", "Vice President", "Executive Secretary", "Finance Secretary",
        "Budget Secretary", "Auditor", "Public Information Secretary", "Property Custodian",
        "Senators", "Legislators", "Year Representative", "Representative", "Other"
    ];

    fetch('api/fetch_org_candidates.php?organization=' + encodeURIComponent(org))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                container.innerHTML = '<p style="color: red; text-align: center;">Error: ' + data.error + '</p>';
                return;
            }

            if (!data.candidates || data.candidates.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #6b7280;">No accepted candidates found for ' + org + '.</p>';
                return;
            }

            const sortedCandidates = data.candidates.sort((a, b) => {
                const posA = positionOrder.indexOf(a.position) !== -1 ? positionOrder.indexOf(a.position) : positionOrder.length;
                const posB = positionOrder.indexOf(b.position) !== -1 ? positionOrder.indexOf(b.position) : positionOrder.length;
                return posA - posB;
            });

            let html = `<h3 style="text-align: center; margin-bottom: 20px; color: #1f2937;">Vote Tally for ${org}</h3>`;
            html += `<table><thead><tr>
                        <th>Candidate Name</th>
                        <th>Position</th>
                        <th style="text-align: center;">Total Votes</th>
                    </tr></thead><tbody>`;

            sortedCandidates.forEach(c => {
                const fullName = [c.first_name, c.middle_name, c.last_name].filter(Boolean).join(' ');
                const position = c.position || 'Representative';
                html += `<tr>
                            <td><strong>${fullName}</strong></td>
                            <td><span class="tally-position">${position}</span></td>
                            <td style="text-align: center;">${c.total_votes || 0}</td>
                        </tr>`;
            });

            html += `</tbody></table>`;
            
            const totalVotes = sortedCandidates.reduce((sum, c) => sum + (parseInt(c.total_votes) || 0), 0);
            html += `<div style="margin-top: 20px; padding: 15px; background: #f0f9ff; border-left: 4px solid #0ea5e9; border-radius: 6px;">`;
            html += `<p style="margin: 0; color: #0c4a6e;"><strong>Total Votes Cast for ${org}:</strong> ${totalVotes}</p>`;
            html += `</div>`;

            container.innerHTML = html;
        })
        .catch(err => {
            container.innerHTML = '<p style="color: red; text-align: center;">Error loading vote tally.</p>';
            console.error(err);
        });
});

// =============== COLLEGE FILTER FOR VOTERS ===============
document.getElementById('collegeFilter')?.addEventListener('change', function() {
    const selectedCollege = this.value;
    const rows = document.querySelectorAll('#votersSection tbody tr');
    let totalVoters = 0;
    let votedCount = 0;

    rows.forEach(row => {
        const college = row.getAttribute('data-college');
        if (!selectedCollege || college === selectedCollege) {
            row.style.display = '';
            totalVoters++;
            if (row.querySelector('.vote-status.voted')) {
                votedCount++;
            }
        } else {
            row.style.display = 'none';
        }
    });

    // Update summary if elements exist
    const totalElement = document.getElementById('totalVoters');
    const votedElement = document.getElementById('votedCount');
    const notVotedElement = document.getElementById('notVotedCount');
    
    if (totalElement) totalElement.textContent = totalVoters;
    if (votedElement) votedElement.textContent = votedCount;
    if (notVotedElement) notVotedElement.textContent = totalVoters - votedCount;
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