<div class="page-head">
    <div>
        <p class="eyebrow"><a class="text-link" href="<?= site_url('admin'); ?>">Overview</a> / <?= $e($table_info['group']); ?></p>
        <h1><?= $e($table_info['label']); ?></h1>
        <p class="subtitle"><?= $e($table_info['description']); ?> <span class="tag"><?= number_format($total); ?> records</span></p>
    </div>
    <?php if (empty($table_info['read_only'])): ?>
        <a class="btn btn-primary" href="<?= site_url('admin/tables/' . $table . '/create'); ?>"><span aria-hidden="true">＋</span> Add record</a>
    <?php else: ?>
        <span class="tag">Read-only audit records</span>
    <?php endif; ?>
</div>

<section class="table-card">
    <div class="toolbar">
        <div><strong style="font-size:12px"><?= $e($table_info['label']); ?></strong><div class="panel-subtitle">Showing records <?= $total ? number_format(($page - 1) * $page_size + 1) : 0; ?>–<?= number_format(min($page * $page_size, $total)); ?> of <?= number_format($total); ?></div></div>
        <?php if ($search_column !== null): ?>
            <form class="search" method="GET" action="<?= site_url('admin/tables/' . $table); ?>">
                <input class="control" type="search" name="q" value="<?= $e($search); ?>" placeholder="Search <?= $e(strtolower(str_replace('_', ' ', $search_column))); ?>…" aria-label="Search records">
                <button class="btn btn-light" type="submit">Search</button>
                <?php if ($search !== ''): ?><a class="btn btn-light" href="<?= site_url('admin/tables/' . $table); ?>">Clear</a><?php endif; ?>
            </form>
        <?php endif; ?>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr>
                <?php foreach ($columns as $column): ?><th><?= $e(ucwords(str_replace('_', ' ', $column))); ?></th><?php endforeach; ?>
                <?php if (empty($table_info['read_only'])): ?><th style="text-align:right">Actions</th><?php endif; ?>
            </tr></thead>
            <tbody>
            <?php if (!empty($rows)): foreach ($rows as $row): ?>
                <tr>
                    <?php foreach ($columns as $column): ?>
                        <td class="<?= $column === $table_info['primary_key'] ? 'id-cell' : ''; ?>" title="<?= $e($row[$column] ?? ''); ?>">
                            <?php
                            $value = $row[$column] ?? '';
                            if (is_string($value) && strlen($value) > 70) {
                                $value = mb_strimwidth($value, 0, 67, '…', 'UTF-8');
                            }
                            echo $e($value);
                            ?>
                        </td>
                    <?php endforeach; ?>
                    <?php if (empty($table_info['read_only'])): ?>
                        <td><div class="actions">
                            <?php if (!($table === 'users' && (string) $row[$table_info['primary_key']] === (string) $current_user_id)): ?>
                                <a class="btn btn-light" style="min-height:31px;padding:6px 9px;font-size:11px" href="<?= site_url('admin/tables/' . $table . '/edit/' . $row[$table_info['primary_key']]); ?>">Edit</a>
                                <form method="POST" action="<?= site_url('admin/tables/' . $table . '/delete/' . $row[$table_info['primary_key']]); ?>" onsubmit="return confirm('Delete this record? Database cascade rules may also remove related records.');">
                                    <?php csrf_field(); ?>
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            <?php else: ?><span class="tag">Current account</span><?php endif; ?>
                        </div></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; else: ?>
                <tr><td class="empty" colspan="<?= count($columns) + (empty($table_info['read_only']) ? 1 : 0); ?>"><?= $search !== '' ? 'No records match your search.' : 'There are no records in this table yet.'; ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php $pageCount = max(1, (int) ceil($total / $page_size)); ?>
    <div class="table-footer">
        <span>Records are stored in the configured BayanAlert database.</span>
        <?php if ($pageCount > 1): ?>
            <nav class="pager" aria-label="Pagination">
                <?php if ($page > 1): ?><a href="<?= site_url('admin/tables/' . $table . '?page=' . ($page - 1) . '&q=' . rawurlencode($search)); ?>">‹</a><?php endif; ?>
                <span class="current"><?= $page; ?> / <?= $pageCount; ?></span>
                <?php if ($page < $pageCount): ?><a href="<?= site_url('admin/tables/' . $table . '?page=' . ($page + 1) . '&q=' . rawurlencode($search)); ?>">›</a><?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
</section>
