{{-- resources/views/admin/admin_orders_table.blade.php --}}
<style>
.attach-pdf-row-btn, .remove-pdf-row-btn, .issue-po-row-btn {
    border: none;
    color: #fff;
    padding: 6px 16px;
    font-size: 0.92rem;
    font-weight: 600;
    border-radius: 6px;
    margin: 2px 2px;
    cursor: pointer;
    transition: background 0.18s, box-shadow 0.18s;
    box-shadow: 0 2px 8px rgba(44, 62, 80, 0.08);
    outline: none;
}

.attach-pdf-row-btn {
    background: linear-gradient(90deg, #318c38 0%, #318c38 100%);
}
.attach-pdf-row-btn:hover {
    background: linear-gradient(90deg, #2e7d32 0%, #43e97b 100%);
}

.remove-pdf-row-btn {
    background: linear-gradient(90deg, #f85032 0%, #e73827 100%);
}
.remove-pdf-row-btn:hover {
    background: linear-gradient(90deg, #c0392b 0%, #e74c3c 100%);
}

.issue-po-row-btn {
    background: linear-gradient(90deg, #36d1c4 0%, #5b86e5 100%);
}
.issue-po-row-btn:hover {
    background: linear-gradient(90deg, #2980b9 0%, #6dd5fa 100%);
}
</style>
@if($orders->count() === 0)
    <p style="text-align:center;color:#888;">No purchase orders found.</p>
@else
<button id="release-all-btn"
    style="background: linear-gradient(90deg,#36d1c4 0%,#5b86e5 100%);
           color: #fff; padding: 0.7rem 2.2rem; border:none; border-radius:8px;
           font-weight:600; margin-bottom:1.3rem; font-size:1rem; cursor:pointer; display:none;">
    &#9889; Release All With PDF (Filtered)
</button>
<table id="orders-table">
    <thead>
        <tr>
            <th>Order Number</th>
            <th>Order Date</th>
            <th>Due Delivery Date</th>
            <th>Company</th>
            <th>Department</th>
            <th>Order Value</th>
            <th>Currency</th>
            <th>Payment-Term</th>
            <th>Status</th>
            <th>PDF</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr data-order-id="{{ $order->id }}">
            <td>
                <a href="#" class="order-link">{{ $order->order_number }}</a>
            </td>
            <td>{{ $order->order_date }}</td>
            <td>{{ $order->delivery_date }}</td>
            <td>{{ $order->company }}</td>
            <td>{{ $order->department }}</td>
            <td>{{ $order->order_value }}</td>
            <td>{{ $order->currency }}</td>
            <td>{{ $order->payment_term }}</td>
            <td>
                @if(strtolower($order->status) === 'delivered')
                    <span class="status-delivered">Delivered</span>
                @elseif(strtolower($order->status) === 'issued')
                    <span class="status-issued">Issued</span>
                @elseif(strtolower($order->status) === 'acknowledged')
                    <span class="status-notdelivered">Not Delivered</span>
                @elseif($order->status === 'partial delivery')
                    <span class="status-partial">Partial</span>
                @else
                    <span>{{ $order->status }}</span>
                @endif
            </td>
            <td>
                @if($order->po_pdf)
                    <a href="{{ Storage::url($order->po_pdf) }}" target="_blank"> <i class="fa fa-file-pdf-o" style="font-size:15px;color:red;margin-right:4px"></i>{{$order->order_number}}</a>
                @else
                    <span style="color:gray;">No PDF</span>
                @endif
            </td>
            <td>
                <form class="attach-pdf-row-form" data-poid="{{ $order->id }}" enctype="multipart/form-data" style="display:inline;">
                    <input type="file" name="po_file" accept="application/pdf" style="display:none;">
                    <button type="button" class="attach-pdf-row-btn" data-poid="{{ $order->id }}">Attach/Verify PDF</button>
                </form>
                 @if(strtolower($order->status) === 'not verified')
                     <button class="remove-pdf-row-btn" data-poid="{{ $order->id }}">Remove PDF</button>
                @elseif(strtolower($order->status) === 'issued')
                     <button class="remove-pdf-row-btn" data-poid="{{ $order->id }}">Remove PDF</button>
                @endif
                @if(strtolower($order->status) === 'not verified')
                    <button class="issue-po-row-btn" data-poid="{{ $order->id }}">Release</button>
                @elseif(strtolower($order->status) === 'issued')
                    <button class="issue-po-row-btn" data-poid="{{ $order->id }}">Release</button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
