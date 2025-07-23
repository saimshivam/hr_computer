<?php
require_once 'includes/header.php';
?>

<section class="py-5 bg-light">
  <div class="container">
    <h1 class="mb-4 text-center">Payment Method</h1>
    <div class="row">

      <!-- QR Section -->
      <div class="col-md-6 mb-4">
        <div class="text-center">
          <img src="assets/images/qr.jpeg" alt="QR Code" class="img-fluid" style="max-width: 250px;">
          <div class="mt-4 text-start">
            <h6 class="text-danger">HOW TO PAY</h6>
            <h3 class="fw-bold">Pay Now</h3>
            <ul class="list-unstyled mt-3">
              <li><strong>Bank Name:</strong> Bank of India</li>
              <li><strong>IFSC:</strong> BARB0MIRGAN</li>
              <li><strong>Account No.:</strong> 52840100013142</li>
              <li><strong>UPI Address:</strong> 6201528726-2@axl</li>
            </ul>
            <p class="bg-info bg-opacity-25 p-2 rounded mt-3">
              <em>Send Payment Screenshot to WhatsApp <span class="text-danger">+91 62015 28726</span></em>
            </p>
          </div>
        </div>
      </div>

      <!-- Form Section -->
      <div class="col-md-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            <form method="POST" action="">
              <div class="mb-3">
                <label for="admission_id" class="form-label">Student Name</label>
                <input type="text" class="form-control" id="admission_id" name="admission_id" required>
              </div>
              <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" required>
              </div>
              <div class="mb-3">
                <label for="method" class="form-label">Payment Method</label>
                <select class="form-select" id="method" name="method" required>
                  <option value="">Select Method</option>
                  <option value="UPI">UPI</option>
                  <option value="Net Banking">Net Banking</option>
                  <option value="Card">Card</option>
                  <option value="Cash">Cash</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="transaction_id" class="form-label">Transaction ID</label>
                <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                <small class="text-muted">(If applicable)</small>
              </div>
              <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary w-100">Submit Payment</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
