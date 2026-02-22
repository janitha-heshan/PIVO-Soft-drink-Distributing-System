document.addEventListener('DOMContentLoaded', function () {

    const productSelect = document.getElementById('productSelect');
    const sizeSelect = document.getElementById('sizeSelect');
    const unitPriceDisplay = document.getElementById('unitPriceDisplay');
    const addItemForm = document.getElementById('addItemForm');
    const orderTableBody = document.getElementById('orderTableBody');
    const emptyRow = document.getElementById('emptyRow');
    const grandTotalEl = document.getElementById('grandTotal');
    const submitBtn = document.getElementById('submitOrderBtn');
    const shopSelect = document.getElementById('shopSelect');
    const orderMessage = document.getElementById('orderMessage');

    let cartTotal = 0;
    let itemCount = 0;
    let cartItems = [];

    // Helper: Get Price based on selection
    function getCurrentPrice() {
        const product = productSelect.value;
        const size = sizeSelect.value;
        if (product && size && prices[product] && prices[product][size]) {
            return parseFloat(prices[product][size]);
        }
        return 0;
    }

    // Populate Sizes based on Product
    productSelect.addEventListener('change', function () {
        const product = this.value;
        sizeSelect.innerHTML = '<option value="" disabled selected>-- Select Size --</option>';

        if (prices[product]) {
            for (let size in prices[product]) {
                if (!size.startsWith('id_')) { // Skip metadata keys
                    const option = document.createElement('option');
                    option.value = size;
                    option.textContent = size;
                    sizeSelect.appendChild(option);
                }
            }
        }
        updatePriceDisplay();
    });

    // Update Price Display
    function updatePriceDisplay() {
        const price = getCurrentPrice();
        unitPriceDisplay.textContent = `LKR ${price}.00`;
    }

    sizeSelect.addEventListener('change', updatePriceDisplay);

    // ADD ITEM TO TABLE
    addItemForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const product = productSelect.value;
        const size = sizeSelect.value;
        const qty = parseInt(document.getElementById('quantityInput').value);
        const unitPrice = getCurrentPrice();

        if (!product || !size || unitPrice === 0) {
            alert("Please select a valid product and size.");
            return;
        }

        // Get Product ID
        const productId = prices[product]['id_' + size];

        // Calculate Row Total
        const rowTotal = unitPrice * qty;

        // Hide "No items" row
        if (itemCount === 0 && emptyRow) {
            emptyRow.style.display = 'none';
            submitBtn.disabled = false;
        }

        // Add to Cart Array
        cartItems.push({
            productId: productId,
            name: product,
            size: size,
            qty: qty,
            price: unitPrice,
            total: rowTotal,
            domId: Date.now() // Simple unique ID
        });

        const currentItem = cartItems[cartItems.length - 1];

        // Create Table Row
        const row = document.createElement('tr');
        row.dataset.id = currentItem.domId;
        row.innerHTML = `
            <td>${product}</td>
            <td>${size}</td>
            <td>${qty}</td>
            <td>${rowTotal.toFixed(2)}</td>
            <td><button class="btn-delete">Remove</button></td>
        `;

        // Add Delete Functionality
        row.querySelector('.btn-delete').addEventListener('click', function () {
            // Find item in array
            const idx = cartItems.findIndex(i => i.domId == row.dataset.id);
            if (idx > -1) {
                cartTotal -= cartItems[idx].total;
                cartItems.splice(idx, 1);
            }

            row.remove();
            itemCount--;
            updateGrandTotal();

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

        // Reset Form Inputs (keep shop)
        document.getElementById('quantityInput').value = 1;
        productSelect.value = "";
        sizeSelect.innerHTML = '<option value="" disabled selected>-- Select Product First --</option>';
        unitPriceDisplay.textContent = "LKR 0.00";
    });

    function updateGrandTotal() {
        grandTotalEl.textContent = `LKR ${cartTotal.toFixed(2)}`;
    }

    // SUBMIT ORDER
    submitBtn.addEventListener('click', function () {
        if (cartItems.length === 0) return;

        const shopId = shopSelect.value;
        if (!shopId) {
            alert("Please register or select a shop first.");
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = "Processing...";

        // Prepare Payload
        const payload = {
            shop_id: shopId,
            items: cartItems,
            total_amount: cartTotal
        };

        // Send to Backend
        fetch('process_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    orderMessage.textContent = "Order Placed Successfully! Redirecting...";
                    orderMessage.style.color = "green";
                    setTimeout(() => {
                        window.location.href = 'shop_dashboard.php';
                    }, 1500);
                } else {
                    orderMessage.textContent = "Error: " + data.message;
                    orderMessage.style.color = "red";
                    submitBtn.disabled = false;
                    submitBtn.textContent = "Submit Order";
                }
            })
            .catch(err => {
                console.error(err);
                orderMessage.textContent = "Server Error. Try again.";
                submitBtn.disabled = false;
                submitBtn.textContent = "Submit Order";
            });
    });

});
