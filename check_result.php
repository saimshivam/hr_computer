<?php
require_once 'includes/header.php';
?>
<section class="py-5 bg-light">
    <div class="container">
        <h1 class="mb-4 text-center">Check Result</h1>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Registration Verification (Local)</h4>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="reg_admission_id" class="form-label">Admission ID</label>
                                <input type="text" class="form-control" id="reg_admission_id" name="reg_admission_id" required>
                            </div>
                            <div class="mb-3">
                                <label for="reg_dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="reg_dob" name="reg_dob" required>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">Verify Registration</button>
                            </div>
                        </form>
                        <div class="mt-4" id="reg-result-placeholder">
                            <!-- Registration verification result will be shown here -->
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Result Verification (Local)</h4>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="res_admission_id" class="form-label">Admission ID</label>
                                <input type="text" class="form-control" id="res_admission_id" name="res_admission_id" required>
                            </div>
                            <div class="mb-3">
                                <label for="res_dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="res_dob" name="res_dob" required>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success">Verify Result</button>
                            </div>
                        </form>
                        <div class="mt-4" id="res-result-placeholder">
                            <!-- Result verification result will be shown here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?> 