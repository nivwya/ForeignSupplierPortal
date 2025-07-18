<style>
    .acknowledge-btn,
.acknowledge-btn *,
.acknowledge-btn :after,
.acknowledge-btn :before,
.acknowledge-btn:after,
.acknowledge-btn:before {
  border: 0 solid;
  box-sizing: border-box;
}
.acknowledge-btn {
  -webkit-tap-highlight-color: transparent;
  -webkit-appearance: button;
  background-color: #318c38;
  background-image: none;
  color: #000;
  cursor: pointer;
  font-size: 100%;
  font-weight: 100;
  line-height: 1;
  margin: 0;
  -webkit-mask-image: -webkit-radial-gradient(#000, #fff);
  padding: 0;
}
.acknowledge-btn:disabled {
  cursor: default;
}
.acknowledge-btn:-moz-focusring {
  outline: auto;
}
.acknowledge-btn svg {
  display: block;
  vertical-align: middle;
}
.acknowledge-btn [hidden] {
  display: none;
}
.acknowledge-btn {
  background: none;
  border-radius: 999px;
  box-sizing: border-box;
  display: block;
  overflow: hidden;
  padding: 0.7rem 2rem;
  position: relative;
  text-transform: uppercase;
  margin-left:34px;
}
.acknowledge-btn span {
  font-weight: 100;
  mix-blend-mode: difference;
  transition: opacity 0.2s;
}
.acknowledge-btn:hover span {
  -webkit-animation: text-reset 0.2s 0.8s forwards;
  animation: text-reset 0.2s 0.8s forwards;
  opacity: 0;
}
.acknowledge-btn:after,
.acknowledge-btn:before {
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
.acknowledge-btn:after {
  background: #318c38;
  border: none;
  height: 2rem;
  width: 0;
  z-index: -1;
}
.acknowledge-btn:hover:before {
  -webkit-animation: border-reset 0.2s linear 0.78s forwards;
  animation: border-reset 0.2s linear 0.78s forwards;
  height: 2rem;
}
.acknowledge-btn:hover:after {
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

@if(empty($orders) || count($orders) === 0)
    <p>No purchase orders found.</p>
@else
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
                <th>View</th>
                <th>Acknowledge</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr data-order-id="{{ $order['id'] }}">
                <td>
                    <a href="#" class="order-link" data-order="{{ $order['order_number'] }}">
                        {{ $order['order_number'] }}
                    </a>
                </td>
                <td>{{ $order['order_date'] }}</td>
                <td>{{ $order['delivery_date'] }}</td>
                <td>{{ $order['company'] }}</td>
                <td>{{ $order['department'] }}</td>
                <td>{{ $order['order_value'] }}</td>
                <td>{{ $order['currency'] }}</td>
                <td>{{ $order['payment_term'] }}</td>
                <td>
                    @if(strtolower($order['status']) === 'delivered')
                        <span class="status-delivered">Delivered</span>
                    @elseif(strtolower($order['status']) === 'issued')
                        <span class="status-issued">Issued</span>
                    @elseif(strtolower($order['status']) === 'acknowledged')
                        <span class="status-notdelivered">Not Delivered</span>
                    @elseif($order['status'] === 'partial delivery')
                        <span class="status-partial">Partial</span>
                    @endif
                </td>
                <td>
                    @if($order['po_pdf'])
                        <a href="{{ Storage::url($order->po_pdf) }}" target="_blank"> <i class="fa fa-file-pdf-o" style="font-size:15px;color:red;margin-right:4px"></i>{{$order->order_number}}</a>
                    @else
                        <span style="color:gray;">No PDF</span>
                    @endif
                </td>
                <td>
                    @if($order['status'] === 'issued')
                        <button class="acknowledge-btn" style="acknowledge-btn" data-id="{{ $order['id'] }}"> <i class="fa fa-pencil" style="font-size:20px;color:green"></i></button>
                    @else
                        <span style="color:green;"> &#10004;</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div id="order-items-details"></div>
@endif
