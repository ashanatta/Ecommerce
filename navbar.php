<?php
session_start();
require 'config.php';

// Fetch the cart count for the logged-in user
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $cart_count = $stmt->fetch()['count'];
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Logo on the left -->
        <a class="navbar-brand" href="#">Shop</a>

        <!-- Center links in the middle -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="list_products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="payment_history.php">Payment History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_product.php">Add Product</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        Cart <i class="bi bi-cart"></i>
                        <span id="cart-count" class="badge bg-secondary"><?php echo $cart_count; ?></span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Register/Login or Logout button based on user login status -->
        <div class="d-flex">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Logout button if the user is logged in -->
                <a class="nav-link" href="logout.php">Logout</a>
            <?php else: ?>
                <!-- Register and Login buttons if the user is not logged in -->
                <a class="nav-link" href="register.php">Register</a>
                <a class="nav-link" href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>