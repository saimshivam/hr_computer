<?php include 'includes/header.php'; ?>
<?php
require_once 'includes/db.php';

// Fetch latest 3 courses
$featured_courses = $pdo->query('SELECT * FROM courses ORDER BY created_at DESC LIMIT 3')->fetchAll();
?>

<!-- Hero Carousel with Professional Overlay on Every Slide -->
<div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php
    $carousel_images = [
      'assets/images/logo3.png',
      'assets/images/logo2.png',
      'assets/images/logo1.png'
    ];

    foreach ($carousel_images as $index => $img) {
      $activeClass = $index === 0 ? 'active' : '';
      echo <<<HTML
      <div class="carousel-item $activeClass position-relative">
        <img src="$img" class="d-block w-100" alt="Slide" style="max-height: 500px; object-fit: cover;">
        
        <!-- Centered Text -->
        <!-- <div class="position-absolute top-50 start-50 translate-middle text-center">
          <h2 class="text-bold fw-bold display-5 mb-1">HR COMPUTER EDUCATION</h2>
          <p class="text-black fs-5">THE LEGACY OF EXCELLENCE IN COMPUTER EDUCATION</p>
        </div> -->

        <!-- Bottom Left (Address) -->
        <div class="position-absolute bottom-0 start-0 text-start text-black p-3">
          <p class="mb-0">📍 Line Bazar Road, Mirganj</p>
          <p class="mb-0">Gopalganj, Bihar</p>
        </div>

        <!-- Bottom Right (Contact) -->
        <div class="position-absolute bottom-0 end-0 text-end text-black p-3">
          <p class="mb-0">📞 +91 6201528726</p>
          <p class="mb-0">✉️ hrmirganj@gmail.com</p>
        </div>
      </div>
HTML;
    }
    ?>
  </div>

  <!-- Carousel Controls -->
  <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>


<!-- Featured Courses -->
<section class="featured-courses py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-4">Featured Courses</h2>
    <div class="row g-4">
      <?php if (count($featured_courses) > 0): ?>
        <?php foreach ($featured_courses as $course): ?>
          <div class="col-md-4">
            <div class="card h-100 shadow-sm">
              <div class="card-body text-center">
                <?php if ($course['icon']): ?>
                  <i class="<?= htmlspecialchars($course['icon']); ?> fa-3x mb-3 text-primary"></i>
                <?php else: ?>
                  <i class="fas fa-book-open fa-3x mb-3 text-primary"></i>
                <?php endif; ?>
                <h5 class="card-title"><?= htmlspecialchars($course['title']); ?></h5>
                <p class="card-text"><?= htmlspecialchars($course['description']); ?></p>
                <div class="d-flex justify-content-center align-items-baseline mb-2">
                  <?php if ($course['discount'] > 0): ?>
                    <span class="text-decoration-line-through text-muted me-2">₹<?= number_format($course['fees'], 2); ?></span>
                    <span class="fw-bold text-success">₹<?= number_format($course['fees'] - ($course['fees'] * $course['discount'] / 100), 2); ?></span>
                    <span class="badge bg-danger ms-2">-<?= number_format($course['discount'], 0); ?>%</span>
                  <?php else: ?>
                    <span class="fw-bold text-primary">₹<?= number_format($course['fees'], 2); ?></span>
                  <?php endif; ?>
                </div>
                <?php if ($course['duration']): ?>
                  <p class="card-text"><i class="far fa-clock me-1"></i> <?= htmlspecialchars($course['duration']); ?></p>
                <?php endif; ?>
                <a href="courses.php" class="btn btn-outline-primary mt-2">Learn More</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center text-muted">No featured courses available yet.</div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Why Choose Us -->
<section class="bg-light py-5">
  <div class="container">
    <h2 class="text-center mb-4">Why Choose HR Computer?</h2>
    <div class="row text-center">
      <div class="col-md-3">
        <i class="fas fa-graduation-cap fa-3x mb-3 text-primary"></i>
        <h4>Expert Faculty</h4>
        <p>Learn from industry experts with years of experience</p>
      </div>
      <div class="col-md-3">
        <i class="fas fa-laptop-code fa-3x mb-3 text-primary"></i>
        <h4>Modern Labs</h4>
        <p>State-of-the-art computer labs with latest technology</p>
      </div>
      <div class="col-md-3">
        <i class="fas fa-certificate fa-3x mb-3 text-primary"></i>
        <h4>Certification</h4>
        <p>Industry-recognized certificates upon completion</p>
      </div>
      <div class="col-md-3">
        <i class="fas fa-briefcase fa-3x mb-3 text-primary"></i>
        <h4>Placement Support</h4>
        <p>Career guidance and placement assistance</p>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="py-5">
  <div class="container text-center">
    <h2>Ready to Start Your Journey?</h2>
    <p class="lead">Join HR Computer today and take the first step towards a successful career</p>
    <a href="admission.php" class="btn btn-primary btn-lg mt-3">Apply Now</a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
