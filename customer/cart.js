// customer/cart.js

// Initialize cart from localStorage or empty array
let cart = JSON.parse(localStorage.getItem('restaurant_cart')) || [];
const CART_CURRENCY_SYMBOL = '\u20b1';

// Current order detail after placing it
let currentOrder = {
    id: null,
    total: 0.00,
    table: null
};

// Initial load
document.addEventListener('DOMContentLoaded', () => {
    renderCart();
});

// Category filtering
function filterCategory(category) {
    // Update active filter button
    const buttons = document.querySelectorAll('.btn-category');
    buttons.forEach(btn => {
        if (btn.innerText.trim().toLowerCase() === category.toLowerCase() || 
            (category === 'all' && btn.innerText.trim().toLowerCase() === 'all menu')) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // Toggle menu sections
    const sections = document.querySelectorAll('.menu-section');
    sections.forEach(section => {
        const cat = section.getAttribute('data-category');
        if (category === 'all' || cat === category) {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    });
}

// Add Item to Cart
function addToCart(productId, name, price, image, stock) {
    const stockLimit = Number.isFinite(parseInt(stock)) ? parseInt(stock) : null;
    const existingItem = cart.find(item => item.product_id === productId);
    
    if (existingItem) {
        if (stockLimit !== null && existingItem.quantity >= stockLimit) {
            alert(`Only ${stockLimit} stock available for ${name}.`);
            return;
        }
        existingItem.quantity += 1;
        existingItem.stock_quantity = stockLimit;
    } else {
        cart.push({
            product_id: productId,
            product_name: name,
            price: parseFloat(price),
            image: image,
            stock_quantity: stockLimit,
            quantity: 1
        });
    }
    
    saveAndRender();
    
    // Animate cart badge for feedback
    const badge = document.getElementById('cart-count');
    if (badge) {
        badge.classList.add('animate-scale');
        setTimeout(() => badge.classList.remove('animate-scale'), 300);
    }
}

// Update quantity
function updateQuantity(productId, change) {
    const item = cart.find(item => item.product_id === productId);
    if (item) {
        if (change > 0 && item.stock_quantity !== null && item.stock_quantity !== undefined && item.quantity >= item.stock_quantity) {
            alert(`Only ${item.stock_quantity} stock available for ${item.product_name}.`);
            return;
        }

        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(productId);
        } else {
            saveAndRender();
        }
    }
}

// Remove Item from Cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.product_id !== productId);
    saveAndRender();
}

// Save Cart State and Re-render UI
function saveAndRender() {
    localStorage.setItem('restaurant_cart', JSON.stringify(cart));
    renderCart();
}

// Render Cart HTML
function renderCart() {
    const cartContainer = document.getElementById('cart-items');
    const countBadge = document.getElementById('cart-count');
    const subtotalText = document.getElementById('cart-subtotal');
    const totalText = document.getElementById('cart-total');
    const mobileTotalText = document.getElementById('mobile-cart-total');
    
    let totalItems = 0;
    let totalPrice = 0.00;
    
    if (cart.length === 0) {
        cartContainer.innerHTML = `
            <div class="text-center text-muted py-5" role="status">
                <i class="bi bi-basket3 fs-1 text-muted mb-3 d-block"></i>
                <span>Your cart is empty.</span>
            </div>
        `;
    } else {
        let html = '';
        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            totalItems += item.quantity;
            totalPrice += subtotal;
            
            html += `
                <div class="cart-item d-flex align-items-center mb-3 p-2 rounded-3">
                    <img src="${item.image}" alt="${item.product_name}" class="cart-item-img rounded-3 me-3" loading="lazy" decoding="async">
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold text-dark fs-7 text-truncate" style="max-width: 140px;">${item.product_name}</h6>
                        <span class="text-primary fw-semibold fs-7">${CART_CURRENCY_SYMBOL}${item.price.toFixed(2)}</span>
                    </div>
                    <div class="cart-controls d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-dark-control p-1 d-flex align-items-center justify-content-center rounded-circle" onclick="updateQuantity(${item.product_id}, -1)" aria-label="Decrease ${item.product_name} quantity">
                            <i class="bi bi-minus-lg fs-8 text-dark"></i>
                        </button>
                        <span class="cart-quantity fw-bold text-dark px-1 fs-7">${item.quantity}</span>
                        <button class="btn btn-sm btn-dark-control p-1 d-flex align-items-center justify-content-center rounded-circle" onclick="updateQuantity(${item.product_id}, 1)" aria-label="Increase ${item.product_name} quantity">
                            <i class="bi bi-plus-lg fs-8 text-dark"></i>
                        </button>
                        <button class="btn btn-sm text-danger-custom ms-2 p-1 bg-transparent border-0" onclick="removeFromCart(${item.product_id})" aria-label="Remove ${item.product_name} from cart">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        cartContainer.innerHTML = html;
    }
    
    // Update numeric values
    countBadge.innerText = totalItems;
    subtotalText.innerText = `${CART_CURRENCY_SYMBOL}${totalPrice.toFixed(2)}`;
    totalText.innerText = `${CART_CURRENCY_SYMBOL}${totalPrice.toFixed(2)}`;
    if (mobileTotalText) {
        mobileTotalText.innerText = `${CART_CURRENCY_SYMBOL}${totalPrice.toFixed(2)}`;
    }
}

// Scroll to Cart (Mobile)
function scrollToCart() {
    const checkoutPanel = document.querySelector('.checkout-panel');
    if (checkoutPanel) {
        checkoutPanel.scrollIntoView({ behavior: 'smooth' });
    }
}

// Submit Order to PHP Backend
function submitOrder(event) {
    event.preventDefault();
    
    if (cart.length === 0) {
        alert("Your cart is empty. Add some delicious food first!");
        return;
    }
    
    const customerName = document.getElementById('customerName').value.trim();
    const tableNumber = parseInt(document.getElementById('tableNumber').value);
    
    if (!customerName) {
        alert("Customer name is required.");
        return;
    }
    
    if (isNaN(tableNumber) || tableNumber <= 0) {
        alert("Please enter a valid table number.");
        return;
    }
    
    const payload = {
        customer_name: customerName,
        table_number: tableNumber,
        items: cart.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity
        }))
    };
    
    const submitBtn = document.querySelector('.checkout-submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Placing Order...`;
    
    fetch('../api/orders.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(result => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = `<i class="bi bi-send-fill me-2"></i> Place Order`;
        
        if (result.status === 200 && result.body.success) {
            // Save state globally for payment script
            currentOrder.id = result.body.order_id;
            currentOrder.total = result.body.total_amount;
            currentOrder.table = tableNumber;
            
            // Trigger payment modal using bootstrap API
            const payModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            payModal.show();
            
            // Initialize payment screen
            initPaymentScreen();
        } else {
            alert("Error: " + (result.body.message || "Failed to place order."));
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = `<i class="bi bi-send-fill me-2"></i> Place Order`;
        console.error("Error submitting order:", error);
        alert("Connection error. Could not reach server.");
    });
}
