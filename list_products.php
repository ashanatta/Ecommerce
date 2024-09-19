<?php

require 'config.php';


// Set the number of products to load at a time
$limit = 6;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Fetch products with pagination
$sql = "SELECT * FROM products LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// Check if it's an AJAX request for loading more
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
    exit(); // Only return the products, no need to return the entire page
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

        // Load more products on button click
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