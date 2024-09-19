<?php
require 'vendor/autoload.php';
require 'config.php';

\Stripe\Stripe::setApiKey('sk_test_51PM9Pb09zaznWE0mm2vJfcdtgDjfeRMtgwwDe7dKFQETqbiPMpwhqib2o6hXpU4mFNIrwDRiO7KbOeBF8QuiH5S900fp9n2YGV'); // Your Secret Key

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['stripeToken']) && !empty($_POST['amount']) && !empty($_POST['user_id']) && !empty($_POST['product_id'])) {
        $token = $_POST['stripeToken'];
        $amount = $_POST['amount'];
        $user_id = $_POST['user_id'];
        $product_id = $_POST['product_id'];

        try {
            $charge = \Stripe\Charge::create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $token,
                'description' => 'Payment for Product ID: ' . $product_id . ' by User ID: ' . $user_id,
            ]);
            $sql = "INSERT INTO payments (user_id, amount, payment_status) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id, $amount, 'success']);
            $deleteCartSql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
            $deleteCartStmt = $conn->prepare($deleteCartSql);
            $deleteCartStmt->execute([$user_id, $product_id]);
            header("Location: list_products.php?message=Payment successful and product removed from cart!");
            exit();
        } catch (Exception $e) {
            echo 'Payment failed: ' . $e->getMessage();
        }
    } else {
        echo 'Payment failed: Missing required data (token, amount, user_id, or product_id)';
    }
}
