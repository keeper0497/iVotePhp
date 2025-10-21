<div class="details" id="userManagementSection" style="display:none;">
    <div class="recentOrders">
        <div class="cardHeader">
            <h2>Users Management</h2>
            <button class="btn" onclick="openModal('addModal')">Add User</button>
        </div>
        
        <?php if ($message && (isset($_POST['addUser']) || isset($_POST['editUser']) || isset($_POST['deactivateUser']) || isset($_POST['reactivateUser']))): ?>
            <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <!-- User Statistics -->
        <?php 
        $userStats = $userController->getUserStats();
        ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; font-size: 1.5rem;"><?= $userStats['active_users'] ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Active Users</p>
            </div>
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 15px; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; font-size: 1.5rem;"><?= $userStats['deactivated_users'] ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Deactivated Users</p>
            </div>
            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 15px; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; font-size: 1.5rem;"><?= $userStats['active_voters'] ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Active Voters</p>
            </div>
            <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 15px; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; font-size: 1.5rem;"><?= $userStats['active_admins'] ?></h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Active Admins</p>
            </div>
        </div>
        
        <!-- Filter Controls -->
        <div style="margin-bottom: 15px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <label for="statusFilter" style="font-weight: 600;">Filter by Status:</label>
            <select id="statusFilter" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="all">All Users</option>
                <option value="active" selected>Active Only</option>
                <option value="deactivated">Deactivated Only</option>
            </select>
        </div>
        
        <table>
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Email</td>
                    <td>Student ID</td>
                    <td>Role</td>
                    <td>College</td>
                    <td>Status</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <?php if (count($allUsers) > 0): ?>
                    <?php foreach ($allUsers as $user): ?>
                        <tr data-status="<?= htmlspecialchars($user['status']) ?>">
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['student_id']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= htmlspecialchars($user['college']) ?></td>
                            <td>
                                <span class="status-badge <?= $user['status'] ?>">
                                    <?= ucfirst(htmlspecialchars($user['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <button class='action-btn edit' 
                                    onclick="editUser(
                                        <?= $user['id'] ?>, 
                                        '<?= htmlspecialchars($user['email']) ?>', 
                                        '<?= htmlspecialchars($user['student_id']) ?>', 
                                        '<?= htmlspecialchars($user['role']) ?>',
                                        '<?= htmlspecialchars($user['college']) ?>'
                                    )">Edit</button>
                                
                                <?php if ($user['status'] === 'active'): ?>
                                    <form method='POST' style='display:inline;' 
                                        onsubmit="return confirm('Are you sure you want to deactivate this user? They will no longer be able to login.');">
                                        <input type='hidden' name='id' value='<?= $user['id'] ?>'/>
                                        <button class='action-btn deactivate' type='submit' name='deactivateUser'>Deactivate</button>
                                    </form>
                                <?php else: ?>
                                    <form method='POST' style='display:inline;' 
                                        onsubmit="return confirm('Are you sure you want to reactivate this user?');">
                                        <input type='hidden' name='id' value='<?= $user['id'] ?>'/>
                                        <button class='action-btn reactivate' type='submit' name='reactivateUser'>Reactivate</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan='7'>No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add custom CSS for status badges -->
<style>
.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.active {
    background: #dcfce7;
    color: #166534;
}

.status-badge.deactivated {
    background: #fee2e2;
    color: #991b1b;
}

.action-btn.deactivate {
    background: #ef4444;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
}

.action-btn.deactivate:hover {
    background: #dc2626;
}

.action-btn.reactivate {
    background: #22c55e;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
}

.action-btn.reactivate:hover {
    background: #16a34a;
}

.action-btn.edit {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
    margin-right: 5px;
}

.action-btn.edit:hover {
    background: #2563eb;
}
</style>

<!-- JavaScript for filtering -->
<script>
document.getElementById('statusFilter')?.addEventListener('change', function() {
    const selectedStatus = this.value;
    const rows = document.querySelectorAll('#usersTableBody tr');
    
    rows.forEach(row => {
        const userStatus = row.getAttribute('data-status');
        
        if (selectedStatus === 'all' || userStatus === selectedStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Set initial filter to show only active users
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.value = 'active';
        statusFilter.dispatchEvent(new Event('change'));
    }
});
</script>