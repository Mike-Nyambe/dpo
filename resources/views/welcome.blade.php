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
            <form id="mobile-money-form" action="{{ route('payment.submit') }}" method="POST">
                @csrf <!-- CSRF Token for security -->

                <div id="mobile-money-section" class="payment-section">
                    <div class="form-group">
                        <label for="amount-mobile">Amount (Kwacha)</label>
                        <input type="number" id="amount-mobile" name="amount" value="10" readonly required>
                    </div>

                    <div class="form-group">
                        <label for="mobile-network">Mobile Network Operator</label>
                        <select id="mobile-network" name="network" required>
                            <option value="" disabled selected>Select Network</option>
                            <option value="mtn">MTN</option>
                            <option value="airtel">Airtel</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone_num" placeholder="Enter your phone number"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" value="Zambia" readonly>
                    </div>

                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <input type="text" id="currency" name="currency" value="ZMW" readonly>
                    </div>
                </div>

                <!-- Submit Button for Mobile Money -->
                <button type="submit" class="submit-button">Proceed to Payment</button>
            </form>

            <!-- Card Payment Section -->
            <form id="card-payment-form" action="#" method="POST" style="display: none;">
                @csrf <!-- CSRF Token for security -->
                <div id="card-payment-section" class="payment-section">
                    <div class="form-group">
                        <label for="amount-card">Amount (USD)</label>
                        <input type="text" id="amount-card" name="amount" value="10" readonly>
                    </div>
                    <div class="form-group">
                        <label for="cardholder-name">Cardholder Name</label>
                        <input type="text" id="cardholder-name" name="cardholder_name"
                            placeholder="Enter cardholder name" required>
                    </div>
                    <div class="form-group">
                        <label for="card-number">Card Number</label>
                        <input type="text" id="card-number" name="card_number" placeholder="Enter card number"
                            required>
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
                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <input type="text" id="currency" name="currency" value="USD" readonly>
                    </div>
                </div>
                <!-- Submit Button for Card Payment -->
                <button type="submit" class="submit-button">Proceed to Payment</button>
            </form>
        </div>
    </div>

    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <!-- JavaScript for Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMoneySection = document.getElementById('mobile-money-form');
            const cardPaymentSection = document.getElementById('card-payment-form');
            const mobileMoneyRadio = document.getElementById('mobile-money');
            const cardPaymentRadio = document.getElementById('card-payment');

            function togglePaymentSections() {
                if (mobileMoneyRadio.checked) {
                    mobileMoneySection.style.display = 'block';
                    cardPaymentSection.style.display = 'none';
                } else if (cardPaymentRadio.checked) {
                    mobileMoneySection.style.display = 'none';
                    cardPaymentSection.style.display = 'block';
                }
            }

            mobileMoneyRadio.addEventListener('change', togglePaymentSections);
            cardPaymentRadio.addEventListener('change', togglePaymentSections);

            togglePaymentSections(); // Initialize the form with the correct section visible
        });
    </script>
</body>

</html>
