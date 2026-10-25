<?php
/**
 * JeevanRakt - Footer Include
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$baseUrl = get_base_url();
?>
    </div> <!-- /.container -->
</main>

<footer class="mt-auto">
    <div class="container py-4">
        <div class="row gy-3 align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <h5 class="fw-bold text-white mb-1">
                    <img src="<?= $baseUrl ?>images/blood-drop.svg" alt="Drop Logo" width="20" height="24" class="me-1">
                    JeevanRakt – Blood Donor Management System
                </h5>
                <p class="mb-0 text-light opacity-75 small">
                    A BCA College Project developed by <strong>Aryan Verma</strong> (Student ID: <strong>241306064</strong>).
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <ul class="list-inline mb-1">
                    <li class="list-inline-item me-3"><a href="<?= $baseUrl ?>index.php">Home</a></li>
                    <li class="list-inline-item me-3"><a href="<?= $baseUrl ?>search.php">Find Donors</a></li>
                    <li class="list-inline-item me-3"><a href="<?= $baseUrl ?>blood-stock.php">Blood Stock</a></li>
                    <li class="list-inline-item me-3"><a href="<?= $baseUrl ?>screening.php">Health Screening</a></li>
                    <li class="list-inline-item"><a href="<?= $baseUrl ?>about.php">About</a></li>
                </ul>
                <small class="text-light opacity-50">&copy; 2026 JeevanRakt. For Educational & Demonstration Purposes.</small>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?= $baseUrl ?>js/script.js"></script>
</body>
</html>
