<div class="page-head">
    <div>
        <p class="eyebrow">BayanAlert administration</p>
        <h1>Good day, <?= $e($admin_name); ?></h1>
        <p class="subtitle">A live operational view of BayanAlert data. Choose a register to review, add, or update records.</p>
    </div>
    <a class="btn btn-light" href="<?= site_url('admin/tables/hazard_reports'); ?>">View reports <span aria-hidden="true">→</span></a>
</div>

<section class="grid stats">
    <article class="stat-card"><div class="stat-label">Total records</div><div class="stat-value"><?= number_format($total_records); ?></div><div class="stat-note">Across <?= count($tables); ?> BayanAlert tables</div></article>
    <article class="stat-card"><div class="stat-label">Hazard reports</div><div class="stat-value"><?= number_format($report_count); ?></div><div class="stat-note">All report records</div></article>
    <article class="stat-card"><div class="stat-label">Pending reports</div><div class="stat-value"><?= number_format($pending_reports); ?></div><div class="stat-note">Awaiting initial review</div></article>
    <article class="stat-card"><div class="stat-label">Active accounts</div><div class="stat-value"><?= number_format($active_users); ?></div><div class="stat-note">Users with active account status</div></article>
</section>

<section class="grid dashboard-grid">
    <article class="panel">
        <div class="panel-head"><div><h2 class="panel-title">Database registers</h2><div class="panel-subtitle">Choose a table to view and manage its records.</div></div><span class="tag"><?= count($tables); ?> tables</span></div>
        <div class="panel-body table-groups">
            <?php foreach ($groups as $groupName => $groupTables): ?>
                <div>
                    <h3 class="group-title"><span><?= $e($groupName); ?></span><span><?= count($groupTables); ?> registers</span></h3>
                    <div class="tile-grid">
                        <?php foreach ($groupTables as $tableName => $definition): ?>
                            <a class="table-tile" href="<?= site_url('admin/tables/' . $tableName); ?>">
                                <span><span class="tile-name"><?= $e($definition['label']); ?></span><span class="quick-description" style="display:block"><?= $e($definition['description']); ?></span></span>
                                <span class="tile-count"><?= number_format($counts[$tableName] ?? 0); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>

    <div class="grid" style="gap:16px">
        <article class="panel">
            <div class="panel-head"><div><h2 class="panel-title">Recent reports</h2><div class="panel-subtitle">Latest hazard reports submitted to BayanAlert.</div></div><a class="text-link" href="<?= site_url('admin/tables/hazard_reports'); ?>">Open register</a></div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead><tr><th>Reference</th><th>Report</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php if (!empty($recent_reports)): foreach ($recent_reports as $report): ?>
                        <tr>
                            <td class="id-cell"><?= $e($report['reference_number']); ?></td>
                            <td title="<?= $e($report['title']); ?>"><?= $e(mb_strimwidth($report['title'], 0, 30, '…', 'UTF-8')); ?></td>
                            <td><span class="tag"><?= $e(ucwords(str_replace('_', ' ', $report['status']))); ?></span></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="3" class="empty">No reports have been submitted yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
        <article class="panel">
            <div class="panel-head"><div><h2 class="panel-title">Quick access</h2><div class="panel-subtitle">Common BayanAlert administration tasks.</div></div></div>
            <div class="panel-body quick-list">
                <a class="quick-link" href="<?= site_url('admin/tables/hazard_categories'); ?>"><span class="quick-icon">+</span><span><span class="quick-name">Manage hazard categories</span><span class="quick-description" style="display:block">Add, update, activate, or deactivate categories.</span></span></a>
                <a class="quick-link" href="<?= site_url('admin/tables/users'); ?>"><span class="quick-icon">U</span><span><span class="quick-name">Manage user accounts</span><span class="quick-description" style="display:block">Review account details, roles, and status.</span></span></a>
                <a class="quick-link" href="<?= site_url('admin/tables/stations'); ?>"><span class="quick-icon">S</span><span><span class="quick-name">Manage response stations</span><span class="quick-description" style="display:block">Maintain stations available for report assignments.</span></span></a>
            </div>
        </article>
    </div>
</section>
