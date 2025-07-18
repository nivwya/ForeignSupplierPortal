<style>
    body {
    background: #f0f4f3;
    font-family: 'Inter', 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
    color: #2c3e50;
}

form#orders-search-form input[type="text"],
form#orders-search-form select {
    padding: 6px;
    width: 120px;
    margin-right: 8px;
    border: 1px solid #b3c1c7;
    border-radius: 4px;
    font-size: 0.9rem;
    color: #2c3e50;
    background: #fff;
}

form#orders-search-form button {
    background: #4caf50;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 0.93rem;
    cursor: pointer;
    transition: background 0.2s;
}

form#orders-search-form button:hover {
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

.status-partial {
    background: #f9fbe7;
    color: #7d6f00;
    font-weight: 600;
    border-radius: 16px;
    padding: 4px 12px;
    font-size: 0.8rem;
}

.status-notdelivered {
    background: #f8d7da;
    color: #b71c1c;
    font-weight: 600;
    border-radius: 16px;
    padding: 4px 12px;
    font-size: 0.85rem;
}

@media (max-width: 768px) {
    form#orders-search-form input[type="text"],
    form#orders-search-form select {
        width: 100px;
        padding: 5px;
        font-size: 0.85rem;
    }
    table {
        font-size: 0.85rem;
    }
    th, td {
        padding: 8px;
    }
}
</style>


<form id="orders-search-form" method="GET" action="{{ route('orders.tab') }}" style="margin-bottom: 18px;">
    <input type="text" name="order_number" placeholder="PO Number" value="{{ request('order_number') }}" style="padding:6px; width:120px;">
    <input type="text" name="company" placeholder="Company" value="{{ request('company') }}" style="padding:6px; width:120px;">
    <input type="text" name="department" placeholder="Department" value="{{ request('department') }}" style="padding:6px; width:120px;">
    <select name="status" style="padding:6px;">
        <option value="">All Statuses</option>
        <option value="issued" @if(request('status')=='issued') selected @endif>Issued</option>
        <option value="acknowledged" @if(request('status')=='acknowledged') selected @endif>Acknowledged</option>
        <option value="delivered" @if(request('status')=='delivered') selected @endif>Delivered</option>
        <option value="partial delivery" @if(request('status')=='partial delivery') selected @endif>Partial Delivery</option>
    </select>
    <button type="submit">Search</button>
</form>

<div id="orders-table-container">
    @include('tabs.orders_table', ['orders' => $orders])
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('orders-search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(searchForm);
            const params = new URLSearchParams(formData).toString();

            fetch(searchForm.action + '?' + params, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('orders-table-container').innerHTML = html;
            })
            .catch(() => {
                alert('Failed to search. Please try again.');
            });
        });
    }
});
</script>