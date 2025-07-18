{{-- resources/views/admin/admin_orders_items.blade.php --}}
@if(empty($items) || count($items) === 0)
    <p style="color:gray;">No items for this purchase order.</p>
@else
    <div style="margin-top:20px;">
        <h4>Line Items for PO: {{ $order->order_number ?? $order->id }}</h4>
        <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
            <thead>
                <tr>
                    <th>Order NO</th>
                    <th>Line Item No</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>UoM</th>
                    <th>Price</th>
                    <th>Value</th>
                    <th>Plant</th>
                    <th>Slocc</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->order_id ?? 'N/A' }}</td>
                    <td>{{ $item->line_item_no ?? 'N/A'}}</td>
                    <td>{{ $item->description ?? '' }}</td>
                    <td>{{ $item->quantity ?? 0 }}</td>
                    <td>{{ $item->uom ?? 'EA' }}</td>
                    <td>{{ number_format($item->price ?? 0, 2) }}</td>
                    <td>{{ number_format(($item->price ?? 0) * ($item->quantity ?? 0), 2) }}</td>
                    <td>{{ $item->plant ?? 'N/A' }}</td>
                    <td>{{ $item->slocc ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
