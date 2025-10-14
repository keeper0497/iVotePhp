<div class="details" id="filingSection" style="display:none;">
    <div class="recentOrders">
        <div class="cardHeader">
            <h2>Filing Management</h2>
        </div>

        <?php if ($message && isset($_POST['action']) && isset($_POST['filing_id'])): ?>
            <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Main Organization Filings -->
        <h3>Main Organization Filings</h3>
        <?php if (count($mainFilings) === 0): ?>
            <p>No filings found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Organization</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Preview</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mainFilings as $filing): ?>
                        <tr>
                            <td><?= htmlspecialchars($filing['id'] ?? '') ?></td>
                            <td><?= htmlspecialchars(($filing['last_name'] ?? '') . ', ' . ($filing['first_name'] ?? '') . ' ' . ($filing['middle_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($filing['organization'] ?? '') ?></td>
                            <td><?= htmlspecialchars($filing['position'] ?? '') ?></td>
                            <td><span class="status <?= htmlspecialchars($filing['status'] ?? '') ?>"><?= htmlspecialchars($filing['status'] ?? '') ?></span></td>
                            <td>
                                <button class="btn" 
                                    onclick="openPreviewModal(
                                        '<?= $filing['id'] ?? '' ?>',
                                        '<?= htmlspecialchars(($filing['last_name'] ?? '') . ', ' . ($filing['first_name'] ?? '') . ' ' . ($filing['middle_name'] ?? '')) ?>',
                                        '<?= htmlspecialchars($filing['organization'] ?? '') ?>',
                                        '<?= htmlspecialchars($filing['position'] ?? '') ?>',
                                        '<?= htmlspecialchars($filing['status'] ?? '') ?>',
                                        '<?= addslashes($filing['profile_pic'] ?? '') ?>',
                                        '<?= addslashes($filing['comelec_form_1'] ?? '') ?>',
                                        '<?= addslashes($filing['recommendation_letter'] ?? '') ?>',
                                        '<?= addslashes($filing['prospectus'] ?? '') ?>',
                                        '<?= addslashes($filing['clearance'] ?? '') ?>',
                                        '<?= addslashes($filing['coe'] ?? '') ?>',
                                        'main',
                                        '',
                                        '<?= addslashes($filing['comment'] ?? '') ?>'
                                    )">Preview</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Sub Organization Filings -->
        <h3 style="margin-top: 40px;">Sub Organization Filings</h3>
        <?php if (count($subFilings) === 0): ?>
            <p>No filings found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Organization</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Preview</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subFilings as $filing): ?>
                        <tr>
                            <td><?= htmlspecialchars($filing['id'] ?? '') ?></td>
                            <td><?= htmlspecialchars(($filing['last_name'] ?? '') . ', ' . ($filing['first_name'] ?? '') . ' ' . ($filing['middle_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($filing['organization'] ?? '') ?></td>
                            <td><?= htmlspecialchars($filing['year'] ?? '') ?></td>
                            <td><span class="status <?= htmlspecialchars($filing['status'] ?? '') ?>"><?= htmlspecialchars($filing['status'] ?? '') ?></span></td>
                            <td>
                                <button class="btn" 
                                    onclick="openPreviewModal(
                                        '<?= $filing['id'] ?? '' ?>',
                                        '<?= htmlspecialchars(($filing['last_name'] ?? '') . ', ' . ($filing['first_name'] ?? '') . ' ' . ($filing['middle_name'] ?? '')) ?>',
                                        '<?= htmlspecialchars($filing['organization'] ?? '') ?>',
                                        '<?= htmlspecialchars($filing['year'] ?? '') ?>',
                                        '<?= htmlspecialchars($filing['status'] ?? '') ?>',
                                        '', '', '', '', '', '',
                                        'sub',
                                        '<?= htmlspecialchars($filing['block_address'] ?? '') ?>',
                                        '<?= addslashes($filing['comment'] ?? '') ?>'
                                    )">Preview</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>