<?php
require 'vendor/autoload.php'; // Include the Stripe PHP library
require 'config.php'; // Your config file

\Stripe\Stripe::setApiKey('sk_test_51PM9Pb09zaznWE0mm2vJfcdtgDjfeRMtgwwDe7dKFQETqbiPMpwhqib2o6hXpU4mFNIrwDRiO7KbOeBF8QuiH5S900fp9n2YGV'); // Your Secret Key

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['stripeToken']) && !empty($_POST['amount']) && !empty($_POST['user_id']) && !empty($_POST['product_id'])) {
        $token = $_POST['stripeToken'];
        $amount = $_POST['amount']; // Amount in cents
        $user_id = $_POST['user_id'];
        $product_id = $_POST['product_id'];

        try {
            // Create a charge
            $charge = \Stripe\Charge::create([
                'amount' => $amount, // Use dynamic amount from the form
                'currency' => 'usd',
                'source' => $token,
                'description' => 'Payment for Product ID: ' . $product_id . ' by User ID: ' . $user_id,
            ]);

            // Store the payment information in your database
            $sql = "INSERT INTO payments (user_id, amount, payment_status) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id, $amount, 'success']);

            // Remove the product from the cart after payment
            $deleteCartSql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
            $deleteCartStmt = $conn->prepare($deleteCartSql);
            $deleteCartStmt->execute([$user_id, $product_id]);

            // Redirect to home page with success message
            header("Location: list_products.php?message=Payment successful and product removed from cart!");
            exit();
        } catch (Exception $e) {
            // Handle the error
            echo 'Payment failed: ' . $e->getMessage();
        }
    } else {
        echo 'Payment failed: Missing required data (token, amount, user_id, or product_id)';
    }
}
