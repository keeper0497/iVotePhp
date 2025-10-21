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

// =============== ENHANCED VOTING SYSTEM ===============
// Multiple Selection Voting Manager
class VotingManager {
    constructor() {
        // Get position limits from global variable set by PHP
        this.positionLimits = window.positionLimits || {
            'Senators': 8,
            'Legislator': 2,
            'Representative': 8
        };
        this.init();
    }
    
    init() {
        this.setupCheckboxValidation();
        this.setupFormValidation();
        this.setupVisualFeedback();
        this.setupHelpTooltips();
        this.addCustomStyles();
    }
    
    setupCheckboxValidation() {
        document.querySelectorAll('.candidate-input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.handleCheckboxChange(e.target);
            });
        });
    }
    
    handleCheckboxChange(checkbox) {
        const position = checkbox.dataset.position;
        const limit = this.positionLimits[position] || 1;
        const positionContainer = document.querySelector(`[data-position="${position}"]`);
        
        if (!positionContainer) return;
        
        const checkboxes = positionContainer.querySelectorAll('.candidate-input[type="checkbox"]');
        const counter = positionContainer.querySelector('.selection-counter .count');
        const validationMessage = positionContainer.querySelector('.validation-message');
        const selectionCounter = positionContainer.querySelector('.selection-counter');
        
        let checkedCount = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) checkedCount++;
        });
        
        // Update counter with color coding
        if (counter) {
            counter.textContent = checkedCount;
            if (selectionCounter) {
                selectionCounter.style.borderLeft = checkedCount > 0 ? '4px solid #059669' : '4px solid #e5e7eb';
                
                if (checkedCount === limit) {
                    selectionCounter.style.background = '#fef3c7';
                    selectionCounter.style.color = '#92400e';
                } else if (checkedCount > 0) {
                    selectionCounter.style.background = '#e6fffa';
                    selectionCounter.style.color = '#065f46';
                } else {
                    selectionCounter.style.background = '#f1f5f9';
                    selectionCounter.style.color = '#475569';
                }
            }
        }
        
        // Handle limit exceeded
        if (checkedCount > limit) {
            checkbox.checked = false;
            checkedCount--;
            if (counter) counter.textContent = checkedCount;
            
            this.showValidationMessage(validationMessage, position, limit);
            this.showToast(`Maximum ${limit} selections allowed for ${position}`, 'warning');
            return;
        }
        
        // Update visual feedback for all checkboxes in this position
        this.updateVisualFeedback(checkboxes, checkedCount, limit);
        
        // Hide validation message if visible
        if (validationMessage && validationMessage.style.display !== 'none') {
            validationMessage.style.display = 'none';
        }
        
        // Update submit button state
        this.updateSubmitButtonState();
    }
    
    updateVisualFeedback(checkboxes, checkedCount, limit) {
        checkboxes.forEach(cb => {
            const candidateOption = cb.closest('.candidate-option');
            
            if (cb.checked) {
                candidateOption.style.background = '#e0f2fe';
                candidateOption.style.borderColor = '#0ea5e9';
                candidateOption.style.transform = 'translateX(3px)';
            } else {
                candidateOption.style.background = '';
                candidateOption.style.borderColor = '#e5e7eb';
                candidateOption.style.transform = '';
                
                // If limit reached, dim non-selected options
                if (checkedCount >= limit) {
                    candidateOption.style.opacity = '0.6';
                    cb.disabled = true;
                } else {
                    candidateOption.style.opacity = '';
                    cb.disabled = false;
                }
            }
        });
    }
    
    showValidationMessage(validationElement, position, limit) {
        if (validationElement) {
            validationElement.style.display = 'block';
            validationElement.style.animation = 'shake 0.5s ease-in-out';
            
            setTimeout(() => {
                validationElement.style.animation = '';
                validationElement.style.display = 'none';
            }, 3000);
        }
    }
    
    setupFormValidation() {
        const form = document.getElementById('votingForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm()) {
                    e.preventDefault();
                }
            });
        }
    }
    
    validateForm() {
        const errors = [];
        let hasAnyVote = false;
        
        // Check each multi-select position
        Object.keys(this.positionLimits).forEach(position => {
            const positionContainer = document.querySelector(`[data-position="${position}"]`);
            if (positionContainer) {
                const checkboxes = positionContainer.querySelectorAll('.candidate-input[type="checkbox"]:checked');
                const limit = this.positionLimits[position];
                
                if (checkboxes.length > limit) {
                    errors.push(`Too many candidates selected for ${position}. Maximum allowed: ${limit}`);
                }
                
                if (checkboxes.length > 0) {
                    hasAnyVote = true;
                }
            }
        });
        
        // Check radio buttons for any votes
        const radioButtons = document.querySelectorAll('input[type="radio"]:checked');
        if (radioButtons.length > 0) {
            hasAnyVote = true;
        }
        
        if (!hasAnyVote) {
            errors.push('Please select at least one candidate before submitting your vote.');
        }
        
        if (errors.length > 0) {
            this.showToast(errors.join('\n'), 'error');
            return false;
        }
        
        // Show confirmation dialog
        return this.showConfirmationDialog();
    }
    
    showConfirmationDialog() {
        const selectedCandidates = this.getSelectedCandidates();
        let message = "Please confirm your votes:\n\n";
        
        selectedCandidates.forEach(selection => {
            message += `${selection.position}: ${selection.candidates.join(', ')}\n`;
        });
        
        message += "\nOnce submitted, your vote cannot be changed.\n\nDo you want to proceed?";
        
        return confirm(message);
    }
    
    getSelectedCandidates() {
        const selections = [];
        
        // Get checkbox selections
        Object.keys(this.positionLimits).forEach(position => {
            const positionContainer = document.querySelector(`[data-position="${position}"]`);
            if (positionContainer) {
                const checkboxes = positionContainer.querySelectorAll('.candidate-input[type="checkbox"]:checked');
                if (checkboxes.length > 0) {
                    const candidates = Array.from(checkboxes).map(cb => {
                        return cb.closest('.candidate-option').querySelector('label strong').textContent;
                    });
                    selections.push({
                        position: position,
                        candidates: candidates
                    });
                }
            }
        });
        
        // Get radio button selections
        const radioButtons = document.querySelectorAll('input[type="radio"]:checked');
        radioButtons.forEach(radio => {
            const candidateName = radio.closest('.candidate-option').querySelector('label strong').textContent;
            const positionText = radio.closest('.candidate-group').querySelector('h4').textContent.split('\n')[0]; // Get only position name
            selections.push({
                position: positionText,
                candidates: [candidateName]
            });
        });
        
        return selections;
    }
    
    updateSubmitButtonState() {
        const submitBtn = document.getElementById('submitVoteBtn');
        if (!submitBtn) return;
        
        const hasAnySelection = 
            document.querySelectorAll('input[type="checkbox"]:checked, input[type="radio"]:checked').length > 0;
        
        if (hasAnySelection) {
            submitBtn.style.background = 'linear-gradient(135deg, #059669, #0ea5e9)';
            submitBtn.style.transform = 'scale(1.02)';
            submitBtn.innerHTML = '🗳️ Submit My Vote';
        } else {
            submitBtn.style.background = 'linear-gradient(135deg, #0ea5e9, #3b82f6)';
            submitBtn.style.transform = '';
            submitBtn.innerHTML = '🗳️ Submit My Vote';
        }
    }
    
    setupVisualFeedback() {
        // Setup hover effects for candidate options
        document.querySelectorAll('.candidate-option').forEach(option => {
            option.addEventListener('mouseenter', () => {
                const input = option.querySelector('input');
                if (!input.disabled) {
                    option.style.background = '#f8fafc';
                    option.style.borderColor = '#0ea5e9';
                }
            });
            
            option.addEventListener('mouseleave', () => {
                const input = option.querySelector('input');
                if (!input.checked) {
                    option.style.background = '';
                    option.style.borderColor = '#e5e7eb';
                }
            });
        });
        
        // Setup radio button change handlers
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', () => {
                this.updateSubmitButtonState();
            });
        });
    }
    
    setupHelpTooltips() {
        // Add help icons for multi-select positions
        Object.keys(this.positionLimits).forEach(position => {
            const positionContainer = document.querySelector(`[data-position="${position}"]`);
            if (positionContainer && this.positionLimits[position] > 1) {
                const header = positionContainer.querySelector('h4');
                if (header) {
                    const helpIcon = document.createElement('span');
                    helpIcon.innerHTML = ' ℹ️';
                    helpIcon.style.cursor = 'help';
                    helpIcon.title = `You can select up to ${this.positionLimits[position]} candidates for this position.`;
                    header.appendChild(helpIcon);
                }
            }
        });
    }
    
    showToast(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            max-width: 350px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease-out;
        `;
        
        switch(type) {
            case 'error':
                toast.style.background = '#dc2626';
                break;
            case 'warning':
                toast.style.background = '#d97706';
                break;
            case 'success':
                toast.style.background = '#059669';
                break;
            default:
                toast.style.background = '#0ea5e9';
        }
        
        toast.textContent = message;
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
    
    addCustomStyles() {
        // Add CSS animations if not already present
        if (!document.getElementById('voting-styles')) {
            const style = document.createElement('style');
            style.id = 'voting-styles';
            style.textContent = `
                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    25% { transform: translateX(-5px); }
                    75% { transform: translateX(5px); }
                }
                
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
                
                .candidate-option {
                    transition: all 0.2s ease;
                }
                
                .selection-counter {
                    transition: all 0.3s ease;
                }
            `;
            document.head.appendChild(style);
        }
    }
}

// =============== ORIGINAL VOTE SUBMISSION (FALLBACK) ===============
const votingForm = document.getElementById('votingForm');
if (votingForm) {
    // Remove any existing listeners and add enhanced one
    const existingHandler = votingForm.onsubmit;
    votingForm.onsubmit = null;
    
    // The VotingManager will handle this, but keep a fallback for basic functionality
    if (!window.positionLimits) {
        votingForm.addEventListener('submit', function(e) {
            const inputs = votingForm.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked');
            
            if (inputs.length === 0) {
                e.preventDefault();
                alert('Please select at least one candidate before submitting your vote.');
                return false;
            }
            
            if (!confirm('Are you sure you want to submit your vote?\n\nThis action is FINAL and cannot be undone.')) {
                e.preventDefault();
                return false;
            }
            
            const btn = votingForm.querySelector('button[name="submitVote"]');
            if (btn) {
                btn.innerHTML = 'Submitting...';
            }
            
            return true;
        });
    }
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
    
    // Initialize enhanced voting system if position limits are available
    if (window.positionLimits && Object.keys(window.positionLimits).length > 0) {
        new VotingManager();
        console.log('Enhanced voting system initialized with position limits:', window.positionLimits);
    } else {
        console.log('Using basic voting system - no position limits defined');
    }
};