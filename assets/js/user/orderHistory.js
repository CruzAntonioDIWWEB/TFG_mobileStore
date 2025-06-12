// Order History JavaScript
function cargarPedidos() {
    showLoadingState();
    
    fetch('obtainOrders.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                mostrarPedidos(data.data);
            } else {
                showErrorState(data.message || 'Error desconocido');
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            showErrorState('Error de conexión con el servidor');
        });
}

function mostrarPedidos(pedidos) {
    const tableWrapper = document.getElementById('ordersTableWrapper');
    const tbody = document.getElementById('ordersTableBody');
    
    hideAllStates();
    
    if (!pedidos || pedidos.length === 0) {
        showEmptyState();
        return;
    }

    tbody.innerHTML = '';

    pedidos.forEach(function (pedido) {
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

        // Celda de ubicación
        const locationCell = document.createElement('td');
        locationCell.className = 'order-location';
        const locationInfo = document.createElement('div');
        locationInfo.className = 'location-info';
        const province = document.createElement('span');
        province.className = 'province';
        province.textContent = pedido.province;
        const locality = document.createElement('span');
        locality.className = 'locality';
        locality.textContent = pedido.locality;
        locationInfo.appendChild(province);
        locationInfo.appendChild(locality);
        locationCell.appendChild(locationInfo);

        // Celda de dirección
        const addressCell = document.createElement('td');
        addressCell.className = 'order-address';
        const addressText = document.createElement('span');
        addressText.className = 'address-text';
        addressText.textContent = pedido.address;
        addressCell.appendChild(addressText);

        // Celda del costo
        const costCell = document.createElement('td');
        costCell.className = 'order-cost';
        const costAmount = document.createElement('span');
        costAmount.className = 'cost-amount';
        costAmount.textContent = `€${parseFloat(pedido.cost).toFixed(2)}`;
        costCell.appendChild(costAmount);

        // Celda del estado
        const statusCell = document.createElement('td');
        statusCell.className = 'order-status';
        const statusBadge = document.createElement('span');
        statusBadge.className = `status-badge ${statusClass}`;
        statusBadge.textContent = statusText;
        statusCell.appendChild(statusBadge);

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
        timeSpan.textContent = pedido.time;
        dateInfo.appendChild(dateSpan);
        dateInfo.appendChild(timeSpan);
        dateCell.appendChild(dateInfo);

        // Agregar todas las celdas a la fila
        row.appendChild(idCell);
        row.appendChild(locationCell);
        row.appendChild(addressCell);
        row.appendChild(costCell);
        row.appendChild(statusCell);
        row.appendChild(dateCell);

        // Agregar la fila al tbody
        tbody.appendChild(row);
    });

    tableWrapper.style.display = 'block';
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
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// State management functions
function showLoadingState() {
    hideAllStates();
    document.getElementById('loadingState').style.display = 'flex';
}

function showEmptyState() {
    hideAllStates();
    document.getElementById('emptyState').style.display = 'block';
}

function showErrorState(message) {
    hideAllStates();
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('errorState').style.display = 'block';
}

function hideAllStates() {
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('errorState').style.display = 'none';
    document.getElementById('ordersTableWrapper').style.display = 'none';
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    cargarPedidos();
});