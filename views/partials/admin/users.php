<div class="details" id="userManagementSection" style="display:none;">
    <div class="recentOrders">
        <div class="cardHeader">
            <h2>Users Management</h2>
            <button class="btn" onclick="openModal('addModal')">Add User</button>
        </div>
        
        <?php if ($message && (isset($_POST['addUser']) || isset($_POST['editUser']) || isset($_POST['deleteUser']))): ?>
            <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Email</td>
                    <td>Student ID</td>
                    <td>Role</td>
                    <td>College</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody>
                <?php if (count($allUsers) > 0): ?>
                    <?php foreach ($allUsers as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['student_id']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= htmlspecialchars($user['college']) ?></td>
                            <td>
                                <button class='action-btn edit' 
                                    onclick="editUser(
                                        <?= $user['id'] ?>, 
                                        '<?= htmlspecialchars($user['email']) ?>', 
                                        '<?= htmlspecialchars($user['student_id']) ?>', 
                                        '<?= htmlspecialchars($user['role']) ?>',
                                        '<?= htmlspecialchars($user['college']) ?>'
                                    )">Edit</button>
                                <form method='POST' style='display:inline;' 
                                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type='hidden' name='id' value='<?= $user['id'] ?>'/>
                                    <button class='action-btn delete' type='submit' name='deleteUser'>Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan='6'>No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>