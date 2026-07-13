// customer/payment.js
const PAYMENT_CURRENCY_SYMBOL = '\u20b1';

// Initialize the modal views
function initPaymentScreen() {
    const processingSection = document.getElementById('payment-processing');
    const successSection = document.getElementById('payment-success');
    const failedSection = document.getElementById('payment-failed');

    processingSection.classList.remove('d-none');
    successSection.classList.add('d-none');
    failedSection.classList.add('d-none');
}

// Simulate user decision
function simulatePayment(status) {
    if (!currentOrder.id) {
        alert("No active order session found.");
        return;
    }

    const payload = {
        order_id: currentOrder.id,
        payment_status: status === 'success' ? 'paid' : 'failed',
        payment_result: status === 'success' ? 'success' : 'failed'
    };

    // Show small loading states if needed
    const processingHeader = document.querySelector('#payment-processing h4');
    processingHeader.innerText = "Authorizing Transaction...";

    fetch('../api/update_payment_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(result => {
        if (result.status === 200 && result.body.success) {
            document.getElementById('payment-processing').classList.add('d-none');

            if (status === 'success') {
                // Populate Success Information
                document.getElementById('success-table-number').innerText = currentOrder.table;
                document.getElementById('success-order-ref').innerText = '#' + currentOrder.id;
                document.getElementById('success-order-amount').innerText = PAYMENT_CURRENCY_SYMBOL + parseFloat(currentOrder.total).toFixed(2);
                
                // Show success screen
                document.getElementById('payment-success').classList.remove('d-none');
                
                // Clear cart from local state and UI
                cart = [];
                localStorage.removeItem('restaurant_cart');
                renderCart();
            } else {
                // Populate and Show Failure Screen
                document.getElementById('failed-reason-text').innerText = "The payment simulation returned: Transaction Declined.";
                document.getElementById('payment-failed').classList.remove('d-none');
            }
        } else {
            alert("Error updating payment simulation: " + (result.body.message || "Unknown error."));
            // Return back to default choices
            initPaymentScreen();
        }
    })
    .catch(error => {
        console.error("Error updating payment simulation status:", error);
        alert("Network error. Failed to send simulation status to server.");
        initPaymentScreen();
    });
}

// Retry payment: goes back to the selection screen
function retryPayment() {
    initPaymentScreen();
}

// Close modal and let customer review cart (the order is stored as unpaid in the DB)
function cancelPaymentSim() {
    const payModalElement = document.getElementById('paymentModal');
    const payModalInstance = bootstrap.Modal.getInstance(payModalElement);
    if (payModalInstance) {
        payModalInstance.hide();
    }
}

// Complete flow and refresh or close
function resetSystem() {
    const payModalElement = document.getElementById('paymentModal');
    const payModalInstance = bootstrap.Modal.getInstance(payModalElement);
    if (payModalInstance) {
        payModalInstance.hide();
    }
    
    // Clear the form fields
    document.getElementById('customerName').value = '';
    // If table number was not set by URL query, let them edit again
    const tableInput = document.getElementById('tableNumber');
    if (!tableInput.hasAttribute('readonly')) {
        tableInput.value = '';
    }

    // Reset current order reference
    currentOrder = { id: null, total: 0.00, table: null };
    
    // Scroll back to top smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
