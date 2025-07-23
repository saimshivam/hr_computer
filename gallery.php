<?php
require_once 'includes/db.php';
$images = $pdo->query('SELECT * FROM gallery_images ORDER BY uploaded_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - HR Computer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <section class="gallery-section bg-light" style="padding-top:0;">
        <div class="container">
            <h1 class="mb-4 text-center" style="margin-top:0;">Gallery</h1>
            <div class="row g-3">
                <?php if (count($images) > 0): ?>
                    <?php foreach ($images as $img): ?>
                        <div class="col-md-3 col-sm-4 col-6">
                            <div class="card">
                                <img src="assets/images/gallery/<?php echo htmlspecialchars($img['filename']); ?>" class="card-img-top" alt="Gallery Image">
                                <?php if ($img['caption']): ?>
                                    <div class="card-body p-2">
                                        <p class="card-text small mb-0 text-center"><?php echo htmlspecialchars($img['caption']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted">No images in the gallery yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php require_once 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 