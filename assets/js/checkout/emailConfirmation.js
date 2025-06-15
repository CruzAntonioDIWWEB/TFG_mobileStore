/**
 * Email confirmation functionality using Formspree
 */

/**
 * Send order confirmation email via Formspree
 */
function sendOrderConfirmationEmail(paypalDetails) {
    console.log('🚀 Starting email confirmation process...');
    console.log('PayPal details received:', paypalDetails);

    // Get user email from localStorage or session
    const userData = getUserFromLocalStorage();
    console.log('User data:', userData);

    const userEmail = userData ? userData.email : null;

    if (!userEmail) {
        console.error('❌ No user email found for order confirmation');
        return Promise.reject('No user email found');
    }

    console.log('✅ User email found:', userEmail);

    // Get cart data and shipping info
    const cartData = getCartFromLocalStorage();
    const shippingInfo = getShippingFormData();

    console.log('Cart data:', cartData);
    console.log('Shipping info:', shippingInfo);

    // Prepare email content
    const orderSummary = generateOrderSummary(cartData, shippingInfo, paypalDetails);

    // Send email via Formspree
    const formspreeUrl = 'https://formspree.io/f/xpwrdpkz';

    const emailData = {
        email: userEmail,
        _replyto: userEmail,
        _subject: `Confirmación de Pedido - Crusertel - ${paypalDetails.id}`,
        message: orderSummary,
        order_id: paypalDetails.id,
        order_total: paypalDetails.purchase_units[0].amount.value,
        customer_name: (paypalDetails.payer && paypalDetails.payer.name)
            ? `${paypalDetails.payer.name.given_name || ''} ${paypalDetails.payer.name.surname || ''}`.trim()
            : userData.username || 'Cliente'
    };

    console.log('📧 Sending email with data:', emailData);

    return fetch(formspreeUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(emailData)
    })
        .then(response => {
            console.log('📧 Email service response status:', response.status);
            if (response.ok) {
                console.log('✅ Order confirmation email sent successfully');
                return { success: true };
            } else {
                console.error('❌ Failed to send order confirmation email, status:', response.status);
                return response.text().then(text => {
                    console.error('Error response:', text);
                    throw new Error(`Email service error: ${response.status}`);
                });
            }
        })
        .catch(error => {
            console.error('❌ Error sending order confirmation email:', error);
            throw error;
        });
}

/**
 * Generate order summary for email
 */
function generateOrderSummary(cartData, shippingInfo, paypalDetails) {
    console.log('📝 Generating order summary...');

    let summary = `

¡Gracias por tu compra en Crusertel! Tu pedido ha sido procesado con éxito.

=====================================
📋 DETALLES DEL PEDIDO:
=====================================
ID de Pedido: ${paypalDetails.id}
Fecha: ${new Date().toLocaleDateString('es-ES')}
Método de Pago: PayPal

=====================================
🛒 PRODUCTOS COMPRADOS:
=====================================`;

    if (cartData && cartData.items) {
        cartData.items.forEach(item => {
            summary += `
• ${item.product_name || item.name}
  Cantidad: ${item.quantity}
  Precio unitario: €${parseFloat(item.price).toFixed(2)}
  Subtotal: €${(item.price * item.quantity).toFixed(2)}`;
        });
    }

    summary += `

💰 TOTAL: €${paypalDetails.purchase_units[0].amount.value}

=====================================
🚚 INFORMACIÓN DE ENVÍO:
=====================================
Provincia: ${shippingInfo.province}
Localidad: ${shippingInfo.locality}
Dirección: ${shippingInfo.address}

¡Gracias por elegir Crusertel!

---
Este es un email automático de confirmación de pedido.
Crusertel - Tu tienda de confianza
    `;

    console.log('✅ Order summary generated');
    return summary;
}

// Make functions available globally
window.sendOrderConfirmationEmail = sendOrderConfirmationEmail;
window.generateOrderSummary = generateOrderSummary;