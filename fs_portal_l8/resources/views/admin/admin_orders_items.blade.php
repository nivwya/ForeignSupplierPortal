{{-- resources/views/admin/admin_orders_items.blade.php --}}
@if(empty($items) || count($items) === 0)
    <p style="color:gray;">No items for this purchase order.</p>
@else
    <div style="margin-top:20px;">
        <!--changes made by niveditha-->
        <h4>Line Items for PO: {{ $order->purchase_doc_no }}</h4>
        <!--changes end-->
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
                    <td>{{ $item->purchase_doc_no }}</td>
                    <td>{{ $item->item_number_doc }}</td>
                    <td>{{ $item->desc_purchase_org }}</td>
                    <td>{{ $item->purchase_order_qty }}</td>
                    <td>{{ $item->unit_of_mesaure }}</td>
                    <td>{{ number_format($item->net_price ?? 0, 2) }}</td>
                    <td>{{ number_format(($item->net_price ?? 0) * ($item->purchase_order_qty ?? 0), 2) }}</td>
                    <td>{{ $item->plant }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
