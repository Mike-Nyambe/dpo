<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Card Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h1>Credit Card Payment</h1>
    <form action="process_payment.php" method="POST">
        <div class="form-group">
            <label for="CreditCardNumber">Credit Card Number</label>
            <input type="text" id="CreditCardNumber" name="CreditCardNumber" required>
        </div>
        <div class="form-group">
            <label for="CreditCardExpiry">Expiration Date (MMYY)</label>
            <input type="text" id="CreditCardExpiry" name="CreditCardExpiry" required>
        </div>
        <div class="form-group">
            <label for="CreditCardCVV">CVV</label>
            <input type="text" id="CreditCardCVV" name="CreditCardCVV" required>
        </div>
        <div class="form-group">
            <label for="CardHolderName">Cardholder Name</label>
            <input type="text" id="CardHolderName" name="CardHolderName" required>
        </div>
        <div class="form-group">
            <label for="PaymentAmount">Amount</label>
            <input type="number" id="PaymentAmount" name="PaymentAmount" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="PaymentCurrency">Currency</label>
            <input type="text" id="PaymentCurrency" name="PaymentCurrency" value="USD" readonly>
        </div>
        <button type="submit">Submit Payment</button>
    </form>
</body>

</html>
