<div style="display: flex; gap: 2rem; align-items: flex-start;">
    <!-- Left: PO Details and Items Table -->
    <div style="flex: 1;">
        <h3>PO Details</h3>
        <table>
            <tr><th>PO Number</th><td>{{ $po['order_number'] }}</td></tr>
            <tr><th>Vendor</th><td>{{ $po['vendor']['name'] ?? 'N/A' }}</td></tr>
            <tr><th>Order Date</th><td>{{ $po['order_date'] }}</td></tr>
            <tr><th>Status</th><td>{{ $po['status'] }}</td></tr>
            <!-- Add more fields as needed -->
        </table>
        <h4 style="margin-top:1rem;">Line Items</h4>
        <table border="1" cellpadding="6" style="width:100%; margin-top:0.5rem;">
            <thead>
                <tr>
                    <th>Line</th>
                    <th>Product Code</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>UOM</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            @foreach($po['items'] as $item)
                <tr>
                    <td>{{ $item['line_item_no'] }}</td>
                    <td>{{ $item['product_code'] }}</td>
                    <td>{{ $item['item_description'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ $item['uom'] }}</td>
                    <td>{{ $item['price'] }}</td>
                    <td>{{ $item['quantity'] * $item['price'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- Right: PDF Preview -->
    <div style="flex: 1;">
        <h3>PO PDF</h3>
        <iframe src="{{ $pdf_url }}" width="100%" height="600px" style="border:1px solid #ccc;"></iframe>
    </div>
</div>
<!-- Proceed Button -->
<div style="margin-top:2rem; text-align:center;">
    <button id="proceed-btn" style="background:#2e7d32; color:#fff; padding:0.7rem 2rem; border:none; border-radius:7px; font-size:1.1rem; font-weight:600; cursor:pointer;">
        Proceed & Attach to PO
    </button>
</div>