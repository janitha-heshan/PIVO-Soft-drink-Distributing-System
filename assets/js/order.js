document.addEventListener('DOMContentLoaded', function(){
    
    // 1. Update Footer & Check Session
    document.getElementById('year').textContent = new Date().getFullYear();
    document.getElementById('logout').addEventListener('click', function(){
        window.location.href = 'login.html';
    });

    // --- ORDER LOGIC ---

    // Mock Prices Database
    const prices = {
        'Mango Nectar': { '200ml': 120, '500ml': 280, '1L': 550 },
        'Mix Fruit':    { '200ml': 130, '500ml': 300, '1L': 580 },
        'Aloe Vera':    { '200ml': 150, '500ml': 350, '1L': 650 },
        'Wood Apple':   { '200ml': 110, '500ml': 260, '1L': 500 }
    };

    const productSelect = document.getElementById('productSelect');
    const sizeSelect = document.getElementById('sizeSelect');
    const unitPriceDisplay = document.getElementById('unitPriceDisplay');
    const addItemForm = document.getElementById('addItemForm');
    const orderTableBody = document.getElementById('orderTableBody');
    const emptyRow = document.getElementById('emptyRow');
    const grandTotalEl = document.getElementById('grandTotal');
    const submitBtn = document.getElementById('submitOrderBtn');

    let cartTotal = 0;
    let itemCount = 0;

    // Helper: Get Price based on selection
    function getCurrentPrice() {
        const product = productSelect.value;
        const size = sizeSelect.value;
        if (product && size && prices[product]) {
            return prices[product][size];
        }
        return 0;
    }

    // Update Price Display when User Selects Options
    function updatePriceDisplay() {
        const price = getCurrentPrice();
        unitPriceDisplay.textContent = `LKR ${price}.00`;
    }

    productSelect.addEventListener('change', updatePriceDisplay);
    sizeSelect.addEventListener('change', updatePriceDisplay);

    // ADD ITEM TO TABLE
    addItemForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const product = productSelect.value;
        const size = sizeSelect.value;
        const qty = parseInt(document.getElementById('quantityInput').value);
        const unitPrice = getCurrentPrice();

        if (!product || unitPrice === 0) {
            alert("Please select a valid product.");
            return;
        }

        // Calculate Row Total
        const rowTotal = unitPrice * qty;

        // Hide "No items" row
        if(itemCount === 0 && emptyRow) {
            emptyRow.style.display = 'none';
            submitBtn.disabled = false;
        }

        // Create Table Row
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${product}</td>
            <td>${size}</td>
            <td>${qty}</td>
            <td>${rowTotal}</td>
            <td><button class="btn-delete">Remove</button></td>
        `;

        // Add Delete Functionality
        row.querySelector('.btn-delete').addEventListener('click', function() {
            row.remove();
            cartTotal -= rowTotal;
            itemCount--;
            updateGrandTotal();
            
            // If empty, show placeholder again
            if (itemCount === 0) {
                emptyRow.style.display = 'table-row';
                submitBtn.disabled = true;
            }
        });

        orderTableBody.appendChild(row);

        // Update Totals
        cartTotal += rowTotal;
        itemCount++;
        updateGrandTotal();

        // Reset Form
        addItemForm.reset();
        unitPriceDisplay.textContent = "LKR 0.00";
    });

    function updateGrandTotal() {
        grandTotalEl.textContent = `LKR ${cartTotal}.00`;
    }

    // SUBMIT ORDER
    submitBtn.addEventListener('click', function() {
        // In place-order.js (inside the submitBtn event listener)
        if(confirm("Are you sure you want to place this order?")) {
            
            // 1. Create Order Object from current table data
            const rows = document.querySelectorAll('#orderTableBody tr');
            let items = [];
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                // Skip empty row if present
                if(cells.length > 1) {
                    items.push({
                        product: cells[0].textContent,
                        size: cells[1].textContent,
                        qty: cells[2].textContent,
                        total: cells[3].textContent
                    });
                }
            });

            const orderObj = {
                id: '#ORD-' + Math.floor(Date.now() / 1000), // Generate ID based on time
                date: new Date().toLocaleDateString(),
                total: document.getElementById('grandTotal').textContent.replace('LKR ', '').replace('.00', ''),
                items: items
            };

            // 2. Save to LocalStorage
            localStorage.setItem('pivo_last_order', JSON.stringify(orderObj));

            // 3. Redirect to Summary Page
            window.location.href = 'ord_summary.html';
        }
    });

});