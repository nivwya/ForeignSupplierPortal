<style>
    .make-delivery-btn,
.make-delivery-btn *,
.make-delivery-btn :after,
.make-delivery-btn :before,
.make-delivery-btn:after,
.make-delivery-btn:before {
  border: 0 solid;
  box-sizing: border-box;
}
.make-delivery-btn {
  -webkit-tap-highlight-color: transparent;
  -webkit-appearance: button;
  background-color: #318c38;
  background-image: none;
  color: green;
  cursor: pointer;
  font-size: 100%;
  font-weight: 500;
  line-height: 1;
  margin: 0;
  -webkit-mask-image: -webkit-radial-gradient(#000, #fff);
  padding: 0;
}
.make-delivery-btn:disabled {
  cursor: default;
}
.make-delivery-btn:-moz-focusring {
  outline: auto;
}
.make-delivery-btn svg {
  display: block;
  vertical-align: middle;
}
.make-delivery-btn [hidden] {
  display: none;
}
.make-delivery-btn {
  background: none;
  border-radius: 999px;
  box-sizing: border-box;
  display: block;
  overflow: hidden;
  padding: 0.4rem 1rem;
  position: relative;
  text-transform: uppercase;
  margin-left:50px;
}
.make-delivery-btn span {
  font-weight: 500;
  mix-blend-mode: difference;
  transition: opacity 0.2s;
}
.make-delivery-btn:hover span {
  -webkit-animation: text-reset 0.2s 0.8s forwards;
  animation: text-reset 0.2s 0.8s forwards;
  opacity: 0;
}
.make-delivery-btn:after,
.make-delivery-btn:before {
  border: 4px solid #318c38;
  border-radius: 999px;
  content: "";
  height: 100%;
  left: 0;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  transition: height 0.2s;
  width: 100%;
}
.make-delivery-btn:after {
  background: #318c38;
  border: none;
  height: 2rem;
  width: 0;
  z-index: -1;
}
.make-delivery-btn:hover:before {
  -webkit-animation: border-reset 0.2s linear 0.78s forwards;
  animation: border-reset 0.2s linear 0.78s forwards;
  height: 2rem;
}
.make-delivery-btn:hover:after {
  -webkit-animation: progress-bar 1s;
  animation: progress-bar 1s;
}
@-webkit-keyframes progress-bar {
  0% {
    opacity: 1;
    width: 0;
  }
  10% {
    opacity: 1;
    width: 15%;
  }
  25% {
    opacity: 1;
    width: 25%;
  }
  40% {
    opacity: 1;
    width: 35%;
  }
  55% {
    opacity: 1;
    width: 75%;
  }
  60% {
    opacity: 1;
    width: 100%;
  }
  to {
    opacity: 0;
    width: 100%;
  }
}
@keyframes progress-bar {
  0% {
    opacity: 1;
    width: 0;
  }
  10% {
    opacity: 1;
    width: 15%;
  }
  25% {
    opacity: 1;
    width: 25%;
  }
  40% {
    opacity: 1;
    width: 35%;
  }
  55% {
    opacity: 1;
    width: 75%;
  }
  60% {
    opacity: 1;
    width: 100%;
  }
  to {
    opacity: 0;
    width: 100%;
  }
}
@-webkit-keyframes border-reset {
  0% {
    height: 2rem !important;
  }
  to {
    height: 100% !important;
  }
}
@keyframes border-reset {
  0% {
    height: 2rem !important;
  }
  to {
    height: 100% !important;
  }
}
@-webkit-keyframes text-reset {
  0% {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
@keyframes text-reset {
  0% {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>

<div class="container">
    {{-- 1. Show acknowledged POs with "Make Delivery" button --}}
    @if(count($acknowledgedPOs))
        <h3>Acknowledged Purchase Orders (No Delivery Yet)</h3>
        <table>
            <thead>
                <tr>
                    <th>Order no.</th>
                    <th>Order date</th>
                    <th>Delivery due date</th>
                    <th>Company</th>
                    <th>Department</th>
                    <th>Order Value</th>
                    <th>Currency</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($acknowledgedPOs as $po)
            <!-- changes made by niveditha -->
                <tr>
                    <td>{{ $po->purchase_doc_no }}</td>
                    <td>{{ $po->po_date }}</td>
                    <td>{{ $po->delivery_date }}</td>
                    <td>{{ $po->company_name }}</td>
                    <td>{{ $po->department_name }}</td>
                    <td>{{ number_format($po->order_total_value ?? 0, 2) }}</td>
                    <td>{{ $po->currency }}</td>
                    <td><span style="color:green;">Acknowledged</span></td>
                    <td>
                        <button class="make-delivery-btn" data-po-id="{{ $po->id }}" style="make-delivery-btn">Make Delivery <i class="fa fa-truck"></i></button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    {{-- 2. Show PO headers only, line items expand on click --}}
    <table id="deliveries-table">
        <thead>
            <tr>
                <th>Order no.</th>
                <th>Order date</th>
                <th>Delivery due date</th>
                <th>Company</th>
                <th>Department</th>
                <th>Order Value</th>
                <th>Currency</th>
                <th>Status</th>
                <th>AMG GRN</th>
                <th>AMG GRN Date</th>
            </tr>
        </thead>
        <tbody>
        @foreach($deliveries as $delivery)
            <tr class="po-header-row" data-order-id="{{ $delivery->prchase_doc_number }}">
                <td>
                    <a href="#" class="po-link" data-order-id="{{ $delivery->prchase_doc_number }}" style="color:#0074d9;text-decoration:underline;">
                        {{ $delivery->prchase_doc_number }}
                    </a>
                </td>
                <td>{{ $delivery->order_dt }}</td>
                <td>{{ $delivery->itm_delivery_dt }}</td>
                <td>{{ $delivery->company_name }}</td>
                <td>{{ $delivery->department }}</td>
                <td>{{ number_format($delivery->net_price * $delivery->purchase_order_qty, 2) }}</td>
                <td>{{ $delivery->currency_key }}</td>
                <td>
                    @if(strtolower($delivery->status_from_deliveries ?? '') === 'delivered')
                        <span style="color:green;">Delivered</span>
                    @else
                        <span>{{ ucfirst($delivery->status_from_deliveries ?? '-') }}</span>
                    @endif
                </td>
                <td>{{ $delivery->purchase_doc_no ?? '-' }}</td>
                <td>{{ $delivery->posting_date ?? '-' }}</td>
            </tr>
            <tr class="po-items-row" id="po-items-{{ $delivery->prchase_doc_number }}" style="display:none;">
                <td colspan="10">
                    <div class="po-items-container"></div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<!-- changes end-->
