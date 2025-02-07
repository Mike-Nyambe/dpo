<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h1>Payment Details</h1>
            <p>Select your preferred payment method and fill in the required details.</p>

            <!-- Payment Method Toggle -->
            <div class="payment-method-toggle">
                <input type="radio" id="mobile-money" name="payment_method" value="mobile_money" checked>
                <label for="mobile-money" class="toggle-button">
                    <i class="fas fa-mobile-alt"></i>
                    <span>Mobile Money</span>
                </label>
                <input type="radio" id="card-payment" name="payment_method" value="card_payment">
                <label for="card-payment" class="toggle-button">
                    <i class="fas fa-credit-card"></i>
                    <span>Card Payment</span>
                </label>
            </div>

            <!-- Mobile Money Section -->
            <div id="mobile-money-section" class="payment-section">
                <div class="form-group">
                    <label for="amount-mobile">Amount (Kwacha)</label>
                    <input type="text" id="amount-mobile" name="amount_mobile" value="10" readonly>
                </div>
                <div class="form-group">
                    <label for="mobile-network">Mobile Network Operator</label>
                    <select id="mobile-network" name="mobile_network" required>
                        <option value="" disabled selected>Select Network</option>
                        <option value="mtn">MTN</option>
                        <option value="airtel">Airtel</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
                </div>
            </div>

            <!-- Card Payment Section -->
            <div id="card-payment-section" class="payment-section" style="display: none;">
                <div class="form-group">
                    <label for="amount-card">Amount (USD)</label>
                    <input type="text" id="amount-card" name="amount_card" value="10" readonly>
                </div>
                <div class="form-group">
                    <label for="cardholder-name">Cardholder Name</label>
                    <input type="text" id="cardholder-name" name="cardholder_name"
                        placeholder="Enter cardholder name" required>
                </div>
                <div class="form-group">
                    <label for="card-number">Card Number</label>
                    <input type="text" id="card-number" name="card_number" placeholder="Enter card number" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="expiry-date">Expiry Date</label>
                        <input type="text" id="expiry-date" name="expiry_date" placeholder="MM/YY" required>
                    </div>
                    <div class="form-group">
                        <label for="cvc">CVC</label>
                        <input type="text" id="cvc" name="cvc" placeholder="CVC" required>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-button">Proceed to Payment</button>
        </div>
    </div>

    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <!-- JavaScript for Toggle -->
    <script src="{{ asset('assets/script.js') }}"></script>
</body>

</html>
