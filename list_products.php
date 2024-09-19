<?php
session_start();
require 'config.php';

// Handle Add to Cart
if (isset($_GET['product_id'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // Redirect to login if the user is not logged in
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $product_id = intval($_GET['product_id']);

    // Check if product is already in the cart for this user
    $check_cart_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_cart_sql);
    $stmt->execute([$user_id, $product_id]);
    $product_in_cart = $stmt->fetch();

    if ($product_in_cart) {
        // If product already exists in cart, increment the quantity
        $update_cart_sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($update_cart_sql);
        $stmt->execute([$user_id, $product_id]);
    } else {
        // If product is not in cart, insert it
        $insert_cart_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($insert_cart_sql);
        $stmt->execute([$user_id, $product_id]);
    }

    // Redirect back to the product list with a success message
    $_SESSION['success'] = "Product added to cart successfully.";
    header("Location: list_products.php");
    exit();
}

// Product listing and pagination logic
$limit = 6;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$sql = "SELECT * FROM products LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// Load more logic
if (isset($_GET['load_more'])) {
    foreach ($products as $product) {
        echo "
            <div class='col-md-4'>
                <div class='card'>
                    <div class='card-body'>
                        <h5 class='card-title'>" . htmlspecialchars($product['product_name']) . "</h5>
                        <p class='card-text'>" . htmlspecialchars($product['description']) . "</p>
                        <p class='card-text'>Price: $" . htmlspecialchars($product['price']) . "</p>
                        <a href='list_products.php?product_id=" . $product['id'] . "' class='btn btn-primary'>Add to Cart</a>
                    </div>
                </div>
            </div>
        ";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Product List</title>
</head>

<body>

    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center m-3">Products</h2>

        <!-- Display success message -->
        <?php if (isset($_SESSION['success'])) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="row" id="product-list">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Title: <?php echo htmlspecialchars($product['product_name']); ?></h5>
                            <p class="card-text">Description :<?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="card-text">Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                            <a href="list_products.php?product_id=<?php echo $product['id']; ?>" class="btn btn-primary">Add to Cart</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
        <div class="text-center mt-3">
            <button id="load-more" class="btn btn-secondary">Load More</button>
        </div>
    </div>
    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentPage = 1;

        $('#load-more').click(function() {
            currentPage++;
            $.ajax({
                url: 'list_products.php',
                type: 'GET',
                data: {
                    page: currentPage,
                    load_more: 1
                },
                success: function(response) {
                    $('#product-list').append(response);
                },
                error: function() {
                    alert('Unable to load more products');
                }
            });
        });
    </script>

</body>

</html>