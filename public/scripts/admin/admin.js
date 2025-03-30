function updateOrderStatus(orderId) {
    const status = document.getElementById('status-' + orderId).value;

    fetch('/admin/updateOrderStatus', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ order_id: orderId, status: status }),
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => console.error('Error:', error));
}

function deleteOrder(orderId) {
    fetch('/admin/deleteOrder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ order_id: orderId }),
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            const row = document.getElementById('order-row-' + orderId);
            if (row) {
                row.remove();
            }
        })
        .catch(error => console.error('Error:', error));
}
