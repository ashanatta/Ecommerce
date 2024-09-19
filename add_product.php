<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Add Product</title>
</head>

<body>

    <?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
    require 'config.php';

    if (isset($_POST['add_product'])) {
        $product_name = $_POST['product_name'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        $sql = "INSERT INTO products (product_name, price, description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$product_name, $price, $description]);

        echo "Product added successfully.";
    }
    ?>

    <div class="container">
        <h2>Add Product</h2>
        <form action="add_product.php" method="POST">
            <div class="mb-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="product_name" name="product_name" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="add_product">Add Product</button>
        </form>
    </div>
</body>

</html>