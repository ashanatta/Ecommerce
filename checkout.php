<?php
session_start();
require 'config.php'; // Your database connection
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_GET['product_id']; // Retrieve product ID from query string

// Query the product's price
$sql = "SELECT price FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found.";
    exit();
}

$amount = intval($product['price'] * 1); // Multiply by 100 to convert to cents and cast to integer
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Checkout</title>
</head>

<body>
    <div class="container">
        <h2>Checkout</h2>
        <form action="process_payment.php" method="POST" id="payment-form">
            <div class="mb-3">
                <label for="card-element">Credit or debit card</label>
                <div id="card-element">
                    <!-- Stripe Card Element will be inserted here -->
                </div>
            </div>
            <!-- Error messages from Stripe will appear here -->
            <div id="card-errors" role="alert" class="text-danger"></div>

            <!-- Add hidden input fields for user_id, product_id, and amount -->
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="amount" value="<?php echo $amount; ?>">

            <button class="btn btn-primary" id="submit-button">Submit Payment</button>
        </form>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Initialize Stripe
        const stripe = Stripe('pk_test_51PM9Pb09zaznWE0mECZ1ObECAO7tycklCuSRESHJVAJv6hDKSOL8nLXkTsvMNeWGK1hbAgdCq0FkiiXdRourf31I00s4mpkExQ');
        const elements = stripe.elements();

        // Create an instance of the card Element
        const card = elements.create('card');
        card.mount('#card-element');

        // Handle form submission
        const form = document.getElementById('payment-form');
        const cardErrors = document.getElementById('card-errors');

        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    // Display error in #card-errors
                    cardErrors.textContent = result.error.message;
                } else {
                    // Send the token to your server
                    stripeTokenHandler(result.token);
                }
            });
        });

        // Function to handle the token and submit the form
        function stripeTokenHandler(token) {
            // Insert the token into the form so it gets submitted to the server
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }
    </script>
</body>

</html>