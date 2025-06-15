/**
 * PayPal Integration for checkout
 */

/**
 * Initialize PayPal button (only called when form is valid)
 */
function initializePayPal() {
    const cartData = getCartFromLocalStorage();

    if (!cartData || !cartData.items || cartData.items.length === 0) {
        document.getElementById('paypal-button-container').innerHTML =
            '<p class="no-cart-message">Agrega productos al carrito para continuar</p>';
        return;
    }

    // Clear any existing PayPal buttons
    document.getElementById('paypal-button-container').innerHTML = '';

    if (!window.paypal) {
        console.error('PayPal SDK not loaded');
        return;
    }

    paypal.Buttons({
        style: {
            color: 'blue',
            shape: 'pill',
            label: 'pay',
            height: 45
        },

        createOrder: function (data, actions) {
            console.log('Creating PayPal order...');

            // Double-check validation (safety net)
            if (!validateShippingForm()) {
                return Promise.reject(new Error('Shipping form validation failed'));
            }

            const cartData = getCartFromLocalStorage();
            if (!cartData || cartData.totalCost <= 0) {
                return Promise.reject(new Error('Invalid cart data'));
            }

            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: cartData.totalCost.toFixed(2),
                        currency_code: 'EUR',
                        breakdown: {
                            item_total: {
                                value: cartData.totalCost.toFixed(2),
                                currency_code: 'EUR'
                            }
                        }
                    },
                    items: cartData.items.map(item => ({
                        name: item.product_name || item.name || 'Producto',
                        unit_amount: {
                            value: parseFloat(item.price).toFixed(2),
                            currency_code: 'EUR'
                        },
                        quantity: item.quantity.toString()
                    }))
                }]
            });
        },

        onApprove: function (data, actions) {
            console.log('PayPal payment approved, capturing...');

            // Show loading
            document.getElementById('paymentLoading').style.display = 'block';
            document.getElementById('paypal-button-container').style.display = 'none';

            return actions.order.capture().then(function (details) {
                console.log('Payment captured successfully:', details);

                // Prepare data for server
                const requestData = {
                    paypalDetails: details,
                    cartData: getCartFromLocalStorage(),
                    shippingInfo: getShippingFormData()
                };

                console.log('Sending order data to server:', requestData);

                // Send to server to create order
                const baseUrl = window.BASE_URL || '/dashboard/TFG/';
                return fetch(`${baseUrl}index.php?controller=checkout&action=processPayment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                })
                    .then(response => {
                        console.log('Server response received:', response.status);
                        return response.json();
                    })
                    .then(result => {
                        console.log('Server response parsed:', result);

                        if (result.success) {
                            console.log('🎉 Order processed successfully, sending confirmation email...');

                            // Send confirmation email with proper error handling
                            const emailPromise = window.sendOrderConfirmationEmail
                                ? window.sendOrderConfirmationEmail(details)
                                : Promise.resolve();

                            return emailPromise
                                .then(() => {
                                    console.log('✅ Email sent successfully');
                                })
                                .catch((emailError) => {
                                    console.error('❌ Email failed but order was successful:', emailError);
                                    // Don't fail the whole process if email fails
                                })
                                .finally(() => {
                                    // Clear cart and redirect regardless of email status
                                    clearCartAfterPurchase();

                                    // Redirect to success page
                                    const baseUrl = window.BASE_URL || '/dashboard/TFG/';
                                    window.location.href = `${baseUrl}index.php?controller=checkout&action=success&orderId=${result.orderId}`;
                                });
                        } else {
                            throw new Error(result.message || 'Error processing order');
                        }
                    })
                    .catch(error => {
                        console.error('Error processing order:', error);
                        alert('Error al procesar el pedido: ' + error.message);

                        // Hide loading, show PayPal button again
                        document.getElementById('paymentLoading').style.display = 'none';
                        document.getElementById('paypal-button-container').style.display = 'block';
                    });
            });
        },

        onCancel: function (data) {
            console.log('Payment cancelled:', data);
            alert('Pago cancelado. Puedes intentarlo de nuevo cuando gustes.');
        },

        onError: function (err) {
            console.error('PayPal error:', err);
            alert('Error en el procesamiento del pago. Por favor, inténtalo de nuevo.');
        }

    }).render('#paypal-button-container');
}

// Make initializePayPal available globally
window.initializePayPal = initializePayPal;