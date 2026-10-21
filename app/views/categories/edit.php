<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">Edit Hazard Category</h4>
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <form action="<?= site_url('category/update/' . $category['category_id']); ?>" method="POST">
                    <?php csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="category_name" class="form-control" value="<?= htmlspecialchars($category['category_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($category['description']); ?></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="activeCheck" <?= $category['is_active'] ? 'checked' : ''; ?> value="1">
                        <label class="form-check-label" for="activeCheck">Active</label>
                    </div>
                    <button type="submit" class="btn btn-success">Update Category</button>
                    <a href="<?= site_url('category'); ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>