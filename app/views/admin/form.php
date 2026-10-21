<div class="page-head">
    <div>
        <p class="eyebrow"><a class="text-link" href="<?= site_url('admin'); ?>">Overview</a> / <a class="text-link" href="<?= site_url('admin/tables/' . $table); ?>"><?= $e($table_info['label']); ?></a></p>
        <h1><?= $record ? 'Edit record' : 'Add record'; ?></h1>
        <p class="subtitle"><?= $record ? 'Update the selected ' . $e(strtolower($table_info['label'])) . ' record.' : 'Create a new ' . $e(strtolower($table_info['label'])) . ' record.'; ?> Fields marked required must be provided.</p>
    </div>
</div>

<section class="form-card">
    <form method="POST" action="<?= $record ? site_url('admin/tables/' . $table . '/update/' . $record[$table_info['primary_key']]) : site_url('admin/tables/' . $table . '/store'); ?>">
        <?php csrf_field(); ?>
        <div class="form-body">
            <?php foreach ($fields as $field): ?>
                <?php
                $type = $field['type'];
                $isBoolean = in_array($type, ['tinyint(1)', 'boolean', 'bool'], true);
                $isTextArea = strpos($type, 'text') !== false || preg_match('/^(varchar|char)\((\d+)\)/', $type, $lengthMatch) && (int) $lengthMatch[2] > 180;
                $required = $field['password']
                    ? !$record
                    : (!$field['nullable'] && $field['default'] === null);
                $value = $field['value'];
                if (strpos($type, 'datetime') === 0 && $value !== '') {
                    $value = str_replace(' ', 'T', substr((string) $value, 0, 16));
                }
                $fullWidth = $isTextArea || $field['password'];
                ?>
                <div class="field <?= $fullWidth ? 'full' : ''; ?>">
                    <?php if ($isBoolean): ?>
                        <label class="field-label"><?= $e($field['label']); ?></label>
                        <input type="hidden" name="<?= $e($field['form_name']); ?>" value="0">
                        <label class="check-control"><input type="checkbox" name="<?= $e($field['form_name']); ?>" value="1" <?= (string) $value === '1' ? 'checked' : ''; ?>> Enabled</label>
                    <?php else: ?>
                        <label class="field-label" for="field-<?= $e($field['form_name']); ?>"><?= $e($field['label']); ?><?= $required ? ' *' : ''; ?></label>
                        <?php if ($field['password']): ?>
                            <input class="control" id="field-<?= $e($field['form_name']); ?>" type="password" name="<?= $e($field['form_name']); ?>" <?= $required ? 'required' : ''; ?> minlength="8" autocomplete="new-password">
                            <div class="field-hint"><?= $record ? 'Leave blank to keep the current value. Stored hashes are never displayed.' : 'Use at least 8 characters. The value is stored as a one-way password hash.'; ?></div>
                        <?php elseif (!empty($field['enum'])): ?>
                            <select class="control" id="field-<?= $e($field['form_name']); ?>" name="<?= $e($field['form_name']); ?>" <?= $required ? 'required' : ''; ?>>
                                <?php if (!$required): ?><option value="">— Not set —</option><?php endif; ?>
                                <?php foreach ($field['enum'] as $option): ?><option value="<?= $e($option); ?>" <?= (string) $value === (string) $option ? 'selected' : ''; ?>><?= $e(ucwords(str_replace('_', ' ', $option))); ?></option><?php endforeach; ?>
                            </select>
                        <?php elseif (!empty($field['reference'])): ?>
                            <select class="control" id="field-<?= $e($field['form_name']); ?>" name="<?= $e($field['form_name']); ?>" <?= $required ? 'required' : ''; ?>>
                                <?php if (!$required): ?><option value="">— Not set —</option><?php endif; ?>
                                <?php foreach ($field['options'] as $option): ?>
                                    <?php $optionId = $option[$field['reference_key']]; ?>
                                    <option value="<?= $e($optionId); ?>" <?= (string) $value === (string) $optionId ? 'selected' : ''; ?>><?= $e($option[$field['reference'][1]]); ?> (<?= $e($optionId); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ($isTextArea): ?>
                            <textarea class="control" id="field-<?= $e($field['form_name']); ?>" name="<?= $e($field['form_name']); ?>" <?= $required ? 'required' : ''; ?>><?= $e($value); ?></textarea>
                        <?php else: ?>
                            <?php
                            $inputType = 'text';
                            if (preg_match('/^(tinyint|smallint|mediumint|int|bigint)/', $type)) {
                                $inputType = 'number';
                            } elseif (preg_match('/^(decimal|float|double)/', $type)) {
                                $inputType = 'number';
                            } elseif (strpos($type, 'datetime') === 0) {
                                $inputType = 'datetime-local';
                            } elseif (strpos($type, 'date') === 0) {
                                $inputType = 'date';
                            } elseif (strpos($type, 'time') === 0) {
                                $inputType = 'time';
                            } elseif ($field['name'] === 'email') {
                                $inputType = 'email';
                            }
                            ?>
                            <input class="control" id="field-<?= $e($field['form_name']); ?>" type="<?= $e($inputType); ?>" name="<?= $e($field['form_name']); ?>" value="<?= $e($value); ?>" <?= $required ? 'required' : ''; ?> <?= $inputType === 'number' && preg_match('/^(decimal|float|double)/', $type) ? 'step="any"' : ''; ?>>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="form-actions">
            <a class="btn btn-light" href="<?= site_url('admin/tables/' . $table); ?>">Cancel</a>
            <button class="btn btn-primary" type="submit"><?= $record ? 'Save changes' : 'Create record'; ?></button>
        </div>
    </form>
</section>
