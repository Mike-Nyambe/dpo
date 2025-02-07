document.addEventListener('DOMContentLoaded', function () {
    const mobileMoneySection = document.getElementById('mobile-money-section');
    const cardPaymentSection = document.getElementById('card-payment-section');
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

    togglePaymentSections();
});
