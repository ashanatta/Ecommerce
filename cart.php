<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';
$user_id = $_SESSION['user_id'];
$sql = "SELECT products.id, products.product_name, products.description, products.price FROM cart 
        JOIN products ON cart.product_id = products.id
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>Your Cart</h2>

        <?php if (count($cartItems) > 0): ?>
            <div class="row">
                <?php foreach ($cartItems as $item): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                <p class="card-text">Price: $<?php echo htmlspecialchars($item['price']); ?></p>
                                <!-- Add Checkout Button -->
                                <a href="checkout.php?user_id=<?php echo $user_id; ?>&product_id=<?php echo $item['id']; ?>" class="btn btn-success">Checkout</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="alert alert-warning">Your cart is empty.</p>
        <?php endif; ?>

        <a href="list_products.php" class="btn btn-primary mt-3">Continue Shopping</a>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>