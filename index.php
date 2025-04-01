<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Standard Checkout - Capture</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AfgoNcC6jmi2fYWeZYAvXy2ngImzC8_dv94UEKlIvMLjStxvc7AKNTeR7R7UbYUyHtLIzCeNoideheU4&currency=EUR&components=messages,buttons&enable-funding=paylater&buyer-country=FR"></script>

</head>

<body style="display:flex; flex-direction:column; align-items:center;">
    <h1>Standard Checkout - Capture</h1>
    <div id="mycart">
        <!-- Container for the PayLater Messages (banner) -->
        <div data-pp-message data-pp-amount="240.00" data-pp-layout="text"></div>
        <!-- Container element for the button -->
        <div id="paypal-button-container"></div>
    </div>

    <!-- API Response after transaction -->
    <div style="margin-top:20px; text-align:center;" id="api-response">
        <a href="index.php">Back to index</a>
        <h4> Transaction Completed</h4>
        <h6>API response :</h6>
        <pre id="api-json"></pre>

    </div>
    <script src="app.js"></script>
</body>

</html>