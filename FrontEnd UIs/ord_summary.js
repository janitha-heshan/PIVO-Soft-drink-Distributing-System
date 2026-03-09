document.addEventListener('DOMContentLoaded', function(){
    
    // Standard Footer & Logout
    document.getElementById('year').textContent = new Date().getFullYear();
    document.getElementById('logout').addEventListener('click', () => window.location.href = 'login.html');

    // --- POPULATE SUMMARY ---
    const orderIdEl = document.getElementById('orderId');
    const orderDateEl = document.getElementById('orderDate');
    const tableBody = document.getElementById('summaryTableBody');
    const subTotalEl = document.getElementById('subTotal');
    const grandTotalEl = document.getElementById('grandTotal');

    // 1. Try to retrieve order from localStorage (Passed from Place Order Page)
    const storedOrder = localStorage.getItem('pivo_last_order');
    let orderData = null;

    if(storedOrder) {
        orderData = JSON.parse(storedOrder);
    } else {
        // 2. Fallback: Dummy Data for demonstration
        orderData = {
            id: '#ORD-' + Math.floor(1000 + Math.random() * 9000),
            date: new Date().toLocaleDateString(),
            total: 1160,
            items: [
                { product: 'Mango Nectar', size: '1L', qty: 1, total: 550 },
                { product: 'Mix Fruit', size: '200ml', qty: 2, total: 260 },
                { product: 'Aloe Vera', size: '500ml', qty: 1, total: 350 }
            ]
        };
    }

    // 3. Render Data
    if(orderData) {
        orderIdEl.textContent = orderData.id;
        orderDateEl.textContent = orderData.date;
        grandTotalEl.textContent = `LKR ${orderData.total}.00`;
        subTotalEl.textContent = `LKR ${orderData.total}.00`; // Assuming no tax/shipping for now

        orderData.items.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.product}</td>
                <td>${item.size}</td>
                <td>${item.qty}</td>
                <td style="text-align:right;">${item.total}</td>
            `;
            tableBody.appendChild(row);
        });
    }

});