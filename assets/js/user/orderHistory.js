// Order History JavaScript with Admin Status Edit
function cargarPedidos() {
    showLoadingState();
    
    // Handle undefined BASE_URL by getting it from current page URL
    const baseUrl = (typeof BASE_URL !== 'undefined' && BASE_URL) ? BASE_URL : window.location.origin + window.location.pathname.replace('/index.php', '/').replace(/\/[^\/]*$/, '/');
    
    // Use different endpoint for admin vs regular users
    const endpoint = (typeof IS_ADMIN !== 'undefined' && IS_ADMIN) ? 'getAllOrders' : 'getOrders';
    const url = baseUrl + 'index.php?controller=user&action=' + endpoint;
    
    console.log('BASE_URL:', typeof BASE_URL !== 'undefined' ? BASE_URL : 'undefined');
    console.log('IS_ADMIN:', typeof IS_ADMIN !== 'undefined' ? IS_ADMIN : 'undefined');
    console.log('Fetching orders from:', url);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text);
            
            try {
                const data = JSON.parse(text);
                console.log('Parsed JSON:', data);
                
                if (data.status === 'success') {
                    mostrarPedidos(data.data);
                } else {
                    showErrorState(data.message || 'Error desconocido');
                }
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                console.error('Response text:', text);
                showErrorState('Error en la respuesta del servidor');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            showErrorState('Error de conexión con el servidor');
        });
}

function mostrarPedidos(pedidos) {
    console.log('mostrarPedidos called with:', pedidos);
    
    const tableWrapper = document.getElementById('ordersTableWrapper');
    const tbody = document.getElementById('ordersTableBody');
    const isAdmin = (typeof IS_ADMIN !== 'undefined' && IS_ADMIN);
    
    if (!tableWrapper) {
        console.error('ordersTableWrapper element not found!');
        return;
    }
    
    if (!tbody) {
        console.error('ordersTableBody element not found!');
        return;
    }
    
    hideAllStates();
    
    if (!pedidos || pedidos.length === 0) {
        console.log('No orders to display, showing empty state');
        showEmptyState();
        return;
    }

    console.log('Processing', pedidos.length, 'orders');
    tbody.innerHTML = '';

    pedidos.forEach(function (pedido, index) {
        console.log(`Processing order ${index + 1}:`, pedido);
        
        try {
            const statusClass = getStatusClass(pedido.status);
            const statusText = getStatusText(pedido.status);
            
            // Crear la fila principal
            const row = document.createElement('tr');
            row.className = 'order-row';

            // Celda del ID del pedido
            const idCell = document.createElement('td');
            idCell.className = 'order-id';
            const orderNumber = document.createElement('span');
            orderNumber.className = 'order-number';
            orderNumber.textContent = `#${pedido.id}`;
            idCell.appendChild(orderNumber);

            // Celda de usuario (solo para admin)
            let userCell = null;
            if (isAdmin) {
                userCell = document.createElement('td');
                userCell.className = 'order-user';
                const userInfo = document.createElement('div');
                userInfo.className = 'user-info';
                userInfo.textContent = `Usuario ID: ${pedido.user_id}`;
                userCell.appendChild(userInfo);
            }

            // Celda de ubicación
            const locationCell = document.createElement('td');
            locationCell.className = 'order-location';
            const locationInfo = document.createElement('div');
            locationInfo.className = 'location-info';
            const province = document.createElement('span');
            province.className = 'province';
            province.textContent = pedido.province || 'N/A';
            const locality = document.createElement('span');
            locality.className = 'locality';
            locality.textContent = pedido.locality || 'N/A';
            locationInfo.appendChild(province);
            locationInfo.appendChild(locality);
            locationCell.appendChild(locationInfo);

            // Celda de dirección
            const addressCell = document.createElement('td');
            addressCell.className = 'order-address';
            const addressText = document.createElement('span');
            addressText.className = 'address-text';
            addressText.textContent = pedido.address || 'N/A';
            addressCell.appendChild(addressText);

            // Celda del costo
            const costCell = document.createElement('td');
            costCell.className = 'order-cost';
            const costAmount = document.createElement('span');
            costAmount.className = 'cost-amount';
            costAmount.textContent = `€${parseFloat(pedido.cost || 0).toFixed(2)}`;
            costCell.appendChild(costAmount);

            // Celda del estado
            const statusCell = document.createElement('td');
            statusCell.className = 'order-status';
            
            if (isAdmin) {
                // Admin: Show editable select styled like status badge
                const statusSelect = createStatusSelect(pedido.id, pedido.status);
                statusSelect.className = `status-select status-badge ${statusClass}`;
                statusCell.appendChild(statusSelect);
            } else {
                // Regular user: Show status badge
                const statusBadge = document.createElement('span');
                statusBadge.className = `status-badge ${statusClass}`;
                statusBadge.textContent = statusText;
                statusCell.appendChild(statusBadge);
            }

            // Celda de fecha
            const dateCell = document.createElement('td');
            dateCell.className = 'order-date';
            const dateInfo = document.createElement('div');
            dateInfo.className = 'date-info';
            const dateSpan = document.createElement('span');
            dateSpan.className = 'date';
            dateSpan.textContent = formatDate(pedido.date);
            const timeSpan = document.createElement('span');
            timeSpan.className = 'time';
            timeSpan.textContent = pedido.time || '';
            dateInfo.appendChild(dateSpan);
            dateInfo.appendChild(timeSpan);
            dateCell.appendChild(dateInfo);

            // Agregar todas las celdas a la fila
            row.appendChild(idCell);
            if (isAdmin && userCell) row.appendChild(userCell);
            row.appendChild(locationCell);
            row.appendChild(addressCell);
            row.appendChild(costCell);
            row.appendChild(statusCell);
            row.appendChild(dateCell);

            // Agregar la fila al tbody
            tbody.appendChild(row);
            console.log(`Order ${index + 1} row added successfully`);
            
        } catch (error) {
            console.error(`Error processing order ${index + 1}:`, error);
            console.error('Order data:', pedido);
        }
    });

    console.log('All orders processed, showing table');
    
    // Force display with important styles
    tableWrapper.style.setProperty('display', 'block', 'important');
    tableWrapper.style.visibility = 'visible';
    tableWrapper.style.opacity = '1';
    
    console.log('Table wrapper display styles applied');
}

// Create status select dropdown for admin
function createStatusSelect(orderId, currentStatus) {
    const select = document.createElement('select');
    select.className = 'status-select';
    select.dataset.orderId = orderId;
    
    const statuses = [
        { value: 'pending', text: 'Pendiente' },
        { value: 'paid', text: 'Pagado' },
        { value: 'shipped', text: 'Enviado' },
        { value: 'delivered', text: 'Entregado' },
        { value: 'canceled', text: 'Cancelado' }
    ];
    
    statuses.forEach(status => {
        const option = document.createElement('option');
        option.value = status.value;
        option.textContent = status.text;
        
        if (status.value === currentStatus) {
            option.selected = true;
        }
        
        select.appendChild(option);
    });
    
    // Add change event listener
    select.addEventListener('change', function() {
        updateOrderStatus(orderId, this.value, this);
    });
    
    return select;
}

// Update order status (admin only)
function updateOrderStatus(orderId, newStatus, selectElement) {
    const originalValue = selectElement.dataset.originalValue || selectElement.value;
    
    // Show loading state
    selectElement.disabled = true;
    selectElement.style.opacity = '0.6';
    
    const baseUrl = (typeof BASE_URL !== 'undefined' && BASE_URL) ? BASE_URL : window.location.origin + window.location.pathname.replace('/index.php', '/').replace(/\/[^\/]*$/, '/');
    
    fetch(baseUrl + 'index.php?controller=user&action=updateOrderStatus', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `order_id=${orderId}&status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Order status updated successfully:', data);
            selectElement.dataset.originalValue = newStatus;
            
            // Update the select styling to match new status
            const newStatusClass = getStatusClass(newStatus);
            selectElement.className = `status-select status-badge ${newStatusClass}`;
            
            // Show success message
            showTemporaryMessage('Estado actualizado correctamente', 'success');
        } else {
            console.error('Failed to update order status:', data.message);
            selectElement.value = originalValue; // Revert to original value
            showTemporaryMessage('Error al actualizar el estado: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error updating order status:', error);
        selectElement.value = originalValue; // Revert to original value
        showTemporaryMessage('Error de conexión al actualizar el estado', 'error');
    })
    .finally(() => {
        // Restore normal state
        selectElement.disabled = false;
        selectElement.style.opacity = '1';
    });
}

// Show temporary message
function showTemporaryMessage(message, type = 'info') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `temp-message temp-message-${type}`;
    messageDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'}"></i> ${message}`;
    
    // Style the message
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#d4edda' : '#f8d7da'};
        color: ${type === 'success' ? '#155724' : '#721c24'};
        border: 1px solid ${type === 'success' ? '#c3e6cb' : '#f5c6cb'};
        border-radius: 5px;
        padding: 12px 16px;
        z-index: 9999;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        font-size: 14px;
        max-width: 300px;
    `;
    
    document.body.appendChild(messageDiv);
    
    // Remove after 3 seconds
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.parentNode.removeChild(messageDiv);
        }
    }, 3000);
}

// Utility functions
function getStatusClass(status) {
    const statusClasses = {
        'pending': 'status-pending',
        'paid': 'status-paid',
        'shipped': 'status-shipped',
        'delivered': 'status-delivered',
        'canceled': 'status-canceled'
    };
    return statusClasses[status] || 'status-default';
}

function getStatusText(status) {
    const statusTexts = {
        'pending': 'Pendiente',
        'paid': 'Pagado',
        'shipped': 'Enviado',
        'delivered': 'Entregado',
        'canceled': 'Cancelado'
    };
    return statusTexts[status] || status;
}

function formatDate(dateString) {
    try {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (error) {
        console.error('Error formatting date:', dateString, error);
        return dateString || 'N/A';
    }
}

// State management functions
function showLoadingState() {
    console.log('Showing loading state');
    hideAllStates();
    const loadingElement = document.getElementById('loadingState');
    if (loadingElement) {
        loadingElement.style.display = 'flex';
    } else {
        console.error('loadingState element not found!');
    }
}

function showEmptyState() {
    console.log('Showing empty state');
    hideAllStates();
    const emptyElement = document.getElementById('emptyState');
    if (emptyElement) {
        emptyElement.style.display = 'block';
    } else {
        console.error('emptyState element not found!');
    }
}

function showErrorState(message) {
    console.log('Showing error state:', message);
    hideAllStates();
    const errorElement = document.getElementById('errorState');
    const errorMessageElement = document.getElementById('errorMessage');
    
    if (errorElement) {
        errorElement.style.display = 'block';
    } else {
        console.error('errorState element not found!');
    }
    
    if (errorMessageElement) {
        errorMessageElement.textContent = message;
    } else {
        console.error('errorMessage element not found!');
    }
}

function hideAllStates() {
    console.log('Hiding all states');
    const elements = [
        'loadingState',
        'emptyState', 
        'errorState',
        'ordersTableWrapper'
    ];
    
    elements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.style.display = 'none';
        } else {
            console.warn(`Element ${id} not found during hideAllStates`);
        }
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing order history');
    console.log('BASE_URL available:', typeof BASE_URL !== 'undefined' ? BASE_URL : 'undefined');
    console.log('IS_ADMIN:', typeof IS_ADMIN !== 'undefined' ? IS_ADMIN : 'undefined');
    cargarPedidos();
});