<?php
require_once 'includes/header.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    // Basic validation
    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($subject)) $errors[] = "Subject is required";
    if (empty($message)) $errors[] = "Message is required";
    
    if (empty($errors)) {
        // Store in database
        require_once 'includes/db.php';
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $success = true;
    }
}
?>

<!-- Contact Information and Form -->
<section class="contact-section bg-light" style="padding-top:0;">
    <div class="container">
        <h1 class="mb-4 text-center" style="margin-top:0;">Contact Us</h1>
        <div class="row g-4">
            <!-- Contact Information -->
            <div class="col-lg-4">
                <div class="contact-info">
                    <h3 class="mb-4">Contact Information</h3>
                    
                    <div class="d-flex mb-4">
                        <div class="icon-box me-3">
                            <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Address</h5>
                            <p class="mb-0">Mirganj - Linebazar Rd, Mirganj, Goapalganj, Bihar 841438</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="icon-box me-3">
                            <i class="fas fa-phone fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Phone</h5>
                            <p class="mb-0">+91 6201528726</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="icon-box me-3">
                            <i class="fas fa-envelope fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Email</h5>
                            <p class="mb-0">hrmirganj@gmail.com</p>
                        </div>
                    </div>
                    
                    <div class="d-flex">
                        <div class="icon-box me-3">
                            <i class="fas fa-clock fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Classes Hours</h5>
                            <p class="mb-0">Monday - Friday: 7:00 AM - 4:00 PM<br>Saturday: 7:00 AM - 4:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="mb-4">Send us a Message</h3>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success">
                                Thank you for your message. We will get back to you soon!
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" class="contact-form">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Your Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Your Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="subject" class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="subject" name="subject" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Maps -->
<section class="map-section py-5 bg-light">
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
            <!-- Google Maps Embed with your actual location -->
<iframe 
  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3584.131445451573!2d84.33137437548628!3d26.367707276936588!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399301001b5bf953%3A0x8c898b0c821a566c!2sHR%20COMPUTER%20EDUCATION%20%F0%9F%96%A5%EF%B8%8F%F0%9F%96%A5%EF%B8%8F!5e0!3m2!1sen!2sin!4v1718614063771!5m2!1sen!2sin" 
  width="100%" 
  height="400" 
  style="border:0;" 
  allowfullscreen="" 
  loading="lazy" 
  referrerpolicy="no-referrer-when-downgrade">
</iframe>

            </div>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?> 