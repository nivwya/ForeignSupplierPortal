<style>

/* --- Input Fields --- */
input[type="number"].qty-supplied-input,
input[type="date"].supply-date-input {
    width: 110px;
    padding: 7px 10px;
    border: 1.5px solid #b3c1c7;
    border-radius: 5px;
    background: #f9fcf9;
    font-size: 1rem;
    transition: border 0.2s, box-shadow 0.2s;
    outline: none;
    margin: 0 2px;
}
input[type="number"].qty-supplied-input:focus,
input[type="date"].supply-date-input:focus {
    border-color: #4caf50;
    box-shadow: 0 0 0 2px #b6e2b6;
}

/* --- Error Highlighting (from JS inline style) --- */
input[style*="e74c3c"] {
    background: #fff0f0 !important;
}

/* --- Buttons --- */
button,
input[type="submit"],
.save-all-btn,
.add-delivery-btn {
    background: linear-gradient(90deg, #4caf50 60%, #43a047 100%);
    color: #fff !important;
    border: none !important;
    border-radius: 6px !important;
    padding: 8px 22px !important;
    font-size: 0.8rem !important;
    font-weight: 300 !important;
    cursor: pointer !important;
    box-shadow: 0 2px 6px rgba(76,175,80,0.08);
    transition: background 0.2s, box-shadow 0.2s;
    margin: 2px 0;
    outline: none !important;
}
button:hover,
input[type="submit"]:hover,
.save-all-btn:hover,
.add-delivery-btn:hover {
    background: linear-gradient(90deg, #43a047 60%, #388e3c 100%) !important;
    box-shadow: 0 4px 12px rgba(76,175,80,0.15);
}
.add-delivery-btn {
    background: linear-gradient(90deg, #2196f3 60%, #1976d2 100%) !important;
    max-width: 80px;
}
.add-delivery-btn:hover {
    background: linear-gradient(90deg, #1976d2 60%, #1565c0 100%) !important;
}
.save-all-btn {
    min-width: 120px;
}

</style>
<!-- changes made by niveditha -->

<div style="background:#f6fff6; padding:8px 0 0 8px; font-weight:bold; border-left:4px solid #b6e2b6; margin-bottom:5px;">
    Order Item details
</div>

<form method="POST" action="{{ route('deliveries.batchSave', ['orderId' => $order->id]) }}" id="batch-save-form" enctype="multipart/form-data">
    @csrf
    <div class="po-items-container">
        <table style="width:100%; border-collapse:collapse; border:1px solid #b3c1c7;">
            <thead>
                <tr>
                    <th style="border:1px solid #b3c1c7;">Order no.</th>
                    <th style="border:1px solid #b3c1c7;">Line Item</th>
                    <th style="border:1px solid #b3c1c7;">Item Desc</th>
                    <th style="border:1px solid #b3c1c7;">Order Qty</th>
                    <th style="border:1px solid #b3c1c7;">Qty Remaining</th>
                    <th style="border:1px solid #b3c1c7;">Expected Delv Date</th>
                    <th style="border:1px solid #b3c1c7;">UoM</th>
                    <th style="border:1px solid #b3c1c7;">Qty Supplied</th>
                    <th style="border:1px solid #b3c1c7;">Supply Date</th>
                    <th style="border:1px solid #b3c1c7;">AMG GRN</th>
                    <th style="border:1px solid #b3c1c7;">AMG GRN Date</th>
                    <th style="border:1px solid #b3c1c7;">Delivery Note</th>
                    <th style="border:1px solid #b3c1c7;">Qty Received by AMG</th>
                    <th style="border:1px solid #b3c1c7;">AMG Received Date</th>
                    <th style="border:1px solid #b3c1c7;">Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($order->items as $poItem)
                @php
                    $deliveryItems = DeliveryItem::where('purchase_order_item_id', $poItem->id)
                        ->orderBy('created_at')->get();
                    $totalSupplied = $deliveryItems->sum('goods_qty');
                    $qtyRemaining = $poItem->schedule_qty - $totalSupplied;
                @endphp

                @foreach($deliveryItems as $loopIndex => $item)
                    <tr @if($item->goods_qty > 0) style="background:#f7f7f7;" @endif>
                        <td style="border:1px solid #b3c1c7;">{{ $order->purchase_doc_no }}</td>
                        <td style="border:1px solid #b3c1c7;">{{ $item->item_no }}</td>
                        <td style="border:1px solid #b3c1c7;">{{ $item->ADD_TEXT1 }}</td>
                        <td style="border:1px solid #b3c1c7;">{{ $poItem->schedule_qty }}</td>
                        <td style="border:1px solid #b3c1c7;">
                            {{ max($poItem->schedule_qty - $deliveryItems->sum('goods_qty') + ($item->goods_qty ?? 0), 0) }}
                        </td>
                        <td style="border:1px solid #b3c1c7;">{{ $item->itm_delivery_dt ?? '-' }}</td>
                        <td style="border:1px solid #b3c1c7;">{{ $item->unit_measure }}</td>
                        <td style="border:1px solid #b3c1c7;">
                            @if(auth()->user()->hasRole('vendor') && ($item->goods_qty == 0 || $item->goods_qty === null))
                                <input type="hidden" name="delivery_item_id[]" value="{{ $item->id }}">
                                <input type="number"
                                    name="quantity_supplied[]"
                                    class="qty-supplied-input"
                                    data-item-id="{{ $item->id }}"
                                    min="0"
                                    max="{{ $poItem->schedule_qty - $deliveryItems->where('id', '!=', $item->id)->sum('goods_qty') }}"
                                    value="{{ $item->goods_qty ?? '' }}">
                            @else
                                {{ $item->goods_qty ?? '-' }}
                            @endif
                        </td>
                        <td style="border:1px solid #b3c1c7;">
                            @if(auth()->user()->hasRole('vendor') && ($item->goods_qty == 0 || $item->goods_qty === null))
                                <input type="date"
                                    name="supply_date[]"
                                    class="supply-date-input"
                                    data-item-id="{{ $item->id }}"
                                    value="{{ $item->stat_del_dt ? Carbon::parse($item->stat_del_dt)->format('Y-m-d') : '' }}">
                            @else
                                {{ $item->stat_del_dt ? Carbon::parse($item->stat_del_dt)->format('m/d/Y') : '-' }}
                            @endif
                        </td>
                        <td style="border:1px solid #b3c1c7;">{{ $item->purchase_doc_no ?? '-' }}</td>
                        <td style="border:1px solid #b3c1c7;">{{ $item->posting_date ?? '-' }}</td>
                        <td style="border:1px solid #b3c1c7;">
                            @if(auth()->user()->hasRole('vendor') && ($item->goods_qty == 0 || $item->goods_qty === null))
                                <input type="file"
                                    name="delivery_note[]"
                                    class="pdf-upload-input"
                                    data-item-id="{{ $item->id }}"
                                    accept="application/pdf">
                            @elseif($item->delivery_note)
                                <a href="{{ asset('storage/' . $item->delivery_note) }}" target="_blank">
                                    <i class="fa fa-file-pdf-o" style="font-size:20px;color:red;margin-right:4px"></i>
                                </a>
                            @endif
                        </td>
                        <td style="border:1px solid #b3c1c7;">{{ $item->qty_received_by_amg ?? '-' }}</td>
                        <td style="border:1px solid #b3c1c7;">{{ $item->amg_received_date ? Carbon::parse($item->amg_received_date)->format('m/d/Y') : '-' }}</td>
                        <td style="border:1px solid #b3c1c7;">
                            @if(auth()->user()->hasRole('vendor') && $item->goods_qty > 0 && $qtyRemaining > 0 && $loop->last)
                                <button type="button"
                                        class="add-delivery-btn"
                                        data-po-item-id="{{ $poItem->id }}"
                                        data-order-id="{{ $order->id }}">
                                    Add Delivery
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
<!-- changes end-->
        @if($order->items->flatMap(function($poItem) {
            return \App\Models\DeliveryItem::where('purchase_order_item_id', $poItem->id)
                ->where(function($query) {
                    $query->whereNull('goods_qty')->orWhere('goods_qty', 0);
                })->get();
        })->count() > 0 && auth()->user()->hasRole('vendor'))
            <div style="text-align:right; margin-top:16px;">
                <button type="submit" class="save-all-btn">
                    Save All
                </button>
            </div>
        @endif
    </div>
</form>

<script>
document.getElementById('batch-save-form').addEventListener('submit', function(e) {
    let valid = true;
    let errorMessages = [];

    let qtyInputs = document.querySelectorAll('.qty-supplied-input');
    let dateInputs = document.querySelectorAll('.supply-date-input');

    qtyInputs.forEach(function(qtyInput, idx) {
        let qty = qtyInput.value.trim();
        let dateInput = dateInputs[idx];
        let date = dateInput.value.trim();

        let qtyNum = parseFloat(qty);

        if ((qty && qtyNum > 0) || date) {
            if (!qty || qtyNum <= 0) {
                valid = false;
                errorMessages.push('If supply date is filled, quantity supplied must be greater than 0.');
                qtyInput.style.border = '2px solid #e74c3c';
            } else {
                qtyInput.style.border = '';
            }
            if (!date) {
                valid = false;
                errorMessages.push('If quantity supplied is entered, supply date is required.');
                dateInput.style.border = '2px solid #e74c3c';
            } else {
                dateInput.style.border = '';
            }
        } else {
            qtyInput.style.border = '';
            dateInput.style.border = '';
        }
    });

    if (!valid) {
        e.preventDefault();
        alert(errorMessages.join('\n'));
    }
});
</script>
