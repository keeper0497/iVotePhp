// Commissioner Panel JavaScript

// Section Management
const sections = {
    dashboard: document.getElementById('dashboardSection'),
    candidates: document.getElementById('candidatesSection'),
    results: document.getElementById('resultsSection')
};

function showSection(sectionName) {
    console.log('Showing section:', sectionName);
    
    Object.values(sections).forEach(section => {
        if (section) section.style.display = 'none';
    });
    
    if (sections[sectionName]) {
        sections[sectionName].style.display = 'block';
    }
    
    sessionStorage.setItem('activeSection', sectionName);
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

document.getElementById('resultsBtn')?.addEventListener('click', (e) => {
    e.preventDefault();
    showSection('results');
});

// Restore active section on page load
window.onload = function() {
    const activeSection = sessionStorage.getItem('activeSection') || 'dashboard';
    showSection(activeSection);
};

// Vote Tally - Main Organization
document.getElementById('mainOrgSelect')?.addEventListener('change', function() {
    const org = this.value;
    const tbody = document.getElementById('mainVoteTbody');
    
    if (!org) {
        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #6b7280;">Select a main organization to view tally</td></tr>';
        return;
    }

    tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px;">Loading...</td></tr>';

    fetch('api/fetch_org_candidates.php?organization=' + encodeURIComponent(org))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #dc2626;">Error: ' + data.error + '</td></tr>';
                return;
            }

            if (!data.candidates || data.candidates.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #6b7280;">No candidates found</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            data.candidates.forEach(c => {
                const fullName = [c.first_name, c.middle_name, c.last_name].filter(Boolean).join(' ');
                const row = document.createElement('tr');
                row.style.borderBottom = '1px solid #e5e7eb';
                row.innerHTML = `
                    <td style="padding: 12px;">${fullName}</td>
                    <td style="padding: 12px;">${c.position}</td>
                    <td style="padding: 12px; text-align: center; font-weight: bold; color: #4f46e5;">${c.total_votes || 0}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(err => {
            tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #dc2626;">Error loading data</td></tr>';
            console.error(err);
        });
});

// Vote Tally - Sub Organization
document.getElementById('subOrgSelect')?.addEventListener('change', function() {
    const org = this.value;
    const tbody = document.getElementById('subVoteTbody');
    
    if (!org) {
        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #6b7280;">Select a sub organization to view tally</td></tr>';
        return;
    }

    tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px;">Loading...</td></tr>';

    fetch('api/fetch_org_candidates.php?organization=' + encodeURIComponent(org))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #dc2626;">Error: ' + data.error + '</td></tr>';
                return;
            }

            if (!data.candidates || data.candidates.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #6b7280;">No candidates found</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            data.candidates.forEach(c => {
                const fullName = [c.first_name, c.middle_name, c.last_name].filter(Boolean).join(' ');
                const row = document.createElement('tr');
                row.style.borderBottom = '1px solid #e5e7eb';
                row.innerHTML = `
                    <td style="padding: 12px;">${fullName}</td>
                    <td style="padding: 12px;">${c.position || 'Representative'}</td>
                    <td style="padding: 12px; text-align: center; font-weight: bold; color: #4f46e5;">${c.total_votes || 0}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(err => {
            tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #dc2626;">Error loading data</td></tr>';
            console.error(err);
        });
});

// Sidebar toggle
const toggle = document.querySelector('.toggle');
const navigation = document.querySelector('.navigation');
const main = document.querySelector('.main');

if (toggle) {
    toggle.addEventListener('click', () => {
        navigation?.classList.toggle('active');
        main?.classList.toggle('active');
    });
}
