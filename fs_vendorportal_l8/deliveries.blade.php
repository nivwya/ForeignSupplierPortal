<style>
body {
    background: #f0f4f3;
    font-family: 'Inter', 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
    color: #2c3e50;
}
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}
.header-title {
    font-weight: 600;
    font-size: 1.1rem;
    color: #2c3e50;
}
.header-date {
    font-size: 0.97rem;
    color: #7b8a8b;
}
.tab-nav {
    display: flex;
    gap: 10px;
    margin-bottom: 18px;
}
.tab-btn {
    padding: 8px 24px;
    border-radius: 24px;
    border: 1.5px solid #28f639;
    background: #fff;
    color: #28a745;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}
.tab-btn.active,
.tab-btn:hover {
    background: #28f639;
    color: #fff;
    border: 1.5px solid #28f639;
}
form#deliveries-search-form {
    display: flex;
    gap: 8px;
    align-items: center;
    margin-bottom: 18px;
    flex-wrap: wrap;
}
form#deliveries-search-form input[type="text"],
form#deliveries-search-form select {
    padding: 6px;
    width: 120px;
    border: 1px solid #b3c1c7;
    border-radius: 4px;
    font-size: 0.9rem;
    color: #2c3e50;
    background: #fff;
}
.search-btn {
    background: #4caf50;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 0.93rem;
    cursor: pointer;
    transition: background 0.2s;
    font-weight: 600;
}
.search-btn:hover {
    background: #388e3c;
}
table {
    border-collapse: collapse;
    width: 100%;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    border: 1px solid #b3c1c7 !important;
}
th {
    background: rgb(40, 199, 48);
    color: #fff;
    font-weight: 600;
    padding: 12px 8px;
    font-size: 0.7rem;
    border-bottom: 2px solid #d0e2d8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid #b3c1c7 !important;
}
td {
    padding: 10px 8px;
    text-align: center;
    font-size: 0.85rem;
    border-bottom: 1px solid #b3c1c7 !important;
}
tr:last-child td {
    border-bottom: none;
}
tr:nth-child(even) td {
    background-color: #f7fdf8;
}
tr:hover td {
    background: #f0f8f5;
    transition: 0.3s;
}
.status-delivered {
    background: #e0f7ef;
    color: #1e8c5c;
    font-weight: 600;
    border-radius: 16px;
    padding: 4px 12px;
    font-size: 0.85rem;
}
.status-issued {
    background: #f2f2f2;
    color: #444;
    font-weight: 600;
    border-radius: 16px;
    padding: 4px 12px;
    font-size: 0.85rem;
}
.acknowledge-btn {
    background: #4caf50;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 0.93rem;
    cursor: pointer;
    transition: background 0.2s;
}
.acknowledge-btn:hover {
    background: #388e3c;
}
@media (max-width: 768px) {
    table {
        font-size: 0.85rem;
    }
    th, td {
        padding: 8px;
    }
    form#deliveries-search-form {
        flex-direction: column;
        gap: 6px;
    }
}
</style>
    <!-- Optional: Card header and tabs here if you use them -->
    <form id="deliveries-search-form" method="GET" action="{{ route('deliveries.tab') }}">
        <input type="text" name="order_number" placeholder="PO Number" value="{{ request('order_number') }}">
        <input type="text" name="company" placeholder="Company" value="{{ request('company') }}">
        <input type="text" name="department" placeholder="Department" value="{{ request('department') }}">
        <select name="status">
            <option value="">All Statuses</option>
            <option value="issued" @if(request('status')=='issued') selected @endif>Issued</option>
            <option value="acknowledged" @if(request('status')=='acknowledged') selected @endif>Acknowledged</option>
            <option value="delivered" @if(request('status')=='delivered') selected @endif>Delivered</option>
            <option value="partial delivery" @if(request('status')=='partial delivery') selected @endif>Partial Delivery</option>
        </select>
        <button type="submit" class="search-btn">Search</button>
    </form>

    <div id="deliveries-table-container">
        @include('tabs.deliveries_table', ['deliveries' => $deliveries, 'acknowledgedPOs' => $acknowledgedPOs])
    </div>
<script>
document.getElementById('deliveries-table').addEventListener('click', function(e) {
    if (e.target.classList.contains('po-link')) {
        e.preventDefault();
        const orderId = e.target.dataset.orderId;
        const itemsRow = document.getElementById('po-items-' + orderId);
        const container = itemsRow.querySelector('.po-items-container');
        // Toggle display
        if (itemsRow.style.display === 'none') {
            if (!container.dataset.loaded) {
                fetch(`/deliveries/${orderId}/items`)
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        container.dataset.loaded = "1";
                        itemsRow.style.display = '';
                    });
            } else {
                itemsRow.style.display = '';
            }
        } else {
            itemsRow.style.display = 'none';
        }
    }
});
</script>

