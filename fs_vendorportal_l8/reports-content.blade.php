<div class="report-tabs" style="display:flex; flex-wrap:wrap; gap:12px;">
  <button class="report-tab" data-report="po-ack">PO Acknowledgement</button>
  <button class="report-tab" data-report="delivery">Delivery Performance</button>
  <button class="report-tab" data-report="grn">GRN Status</button>
  <button class="report-tab" data-report="invoice">Invoice Status</button>
  <button class="report-tab" data-report="payment">Payment Status</button>
  <button class="report-tab" data-report="dashboard">Vendor Performance Dashboard</button>
  <button class="report-tab" data-report="backlog">Open Orders/ Backlog</button>
  <button class="report-tab" data-report="returns">Returns & Rejections</button>
</div>

<div id="report-content-area">
  {{-- All report-section contents remain same HTML-wise, but will follow cleaner styling below --}}
  {{-- PO Acknowledgement --}}
  <div class="report-section" id="report-po-ack" style="display:none;">
    <div class="container mt-4">
      <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; color: #333; font-family: 'Inter', sans-serif;">
        <h3 style="color:#1c3e2c; font-weight:600; ">
          PO Acknowledgement Report
        </h3>

        <form id="filterFormPO" style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
          <input type="text" id="ack_po_number" name="ack_po_number" placeholder="PO Number" style="padding:6px; border:1px solid #ccc; border-radius:4px;">
          <select id="ack_status" name="ack_status" style="padding:6px; border:1px solid #ccc; border-radius:4px;">
  <option value="">All Statuses</option>
  @foreach($poStatuses as $status)
    <option value="{{ $status }}" {{ request('ack_status') == $status ? 'selected' : '' }}>{{ $status }}</option>
  @endforeach
</select>

          <button type="button" id="filterButton" class="filter-button">Search</button>
        </form>

        <table style="width: 100%; border-collapse: collapse; background: white; font-size: 0.8rem; border: 1px solid #d4d4d4;">
          <thead style="background: #35c74b; color: white;">
            <tr>
              <th style="padding: 10px;">PO Number</th>
              <th style="padding: 10px;">Order Date</th>
              <th style="padding: 10px;">Order Value</th>
              <th style="padding: 10px;">Acknowledgment Status</th>
              <th style="padding: 10px;">Acknowledgement Date</th>
              <th style="padding: 10px;">SLA Status</th>
            </tr>
          </thead>
          <tbody id="reportBody">
            @forelse($poReports as $po)
            <tr data-order-date="{{ $po->order_date }}" data-delivery-date="{{ $po->delivery_date ?? '' }}" data-po-number="{{ $po->order_number }}" data-status="{{ $po->status }}">
              <td style="padding: 10px;">{{ $po->order_number }}</td>
              <td style="padding: 10px;">{{ $po->order_date }}</td>
              <td style="padding: 10px;">${{ number_format($po->order_value, 2) }}</td>
              <td style="padding: 10px;">{{ $po->ack_status }}</td>
              <td style="padding: 10px;">{{ $po->acknowledgement_date  }}</td>
              <td style="padding: 10px;">{{ $po->status  }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center; padding:10px;">No records found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Delivery Performance --}}
<div class="report-section" id="report-delivery" style="display:none;">
  <div class="container mt-4">
    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; color: #333; font-family: 'Inter', sans-serif;">
      <h3 style="color:#1c3e2c; font-weight:600; ">
        Delivery Performance Report
      </h3>

      <form id="filterFormDelivery" style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
        <input type="text" id="delivery_po_number" name="delivery_po_number" placeholder="PO Number" style="padding:6px; border:1px solid #ccc; border-radius:4px;">
        <select id="delivery_status" name="delivery_status" style="padding:6px; border:1px solid #ccc; border-radius:4px;">
          <option value="">All Statuses</option>
          <option value="On Time">On Time</option>
          <option value="Delayed">Delayed</option>
          <option value="Early">Early</option>
        </select>
        <button type="button" id="filterDeliveryBtn" class="filter-button">Search</button>
      </form>

      <table style="width: 100%; border-collapse: collapse; background: white; font-size: 0.8rem; border: 1px solid #d4d4d4;">
        <thead style="background-color: #35c74b; color: white; text-transform: uppercase; font-weight: 600;">
          <tr>
            <th style="padding: 10px;">PO Number</th>
            <th style="padding: 10px;">Material Code</th>
            <th style="padding: 10px;">Material Description</th>
            <th style="padding: 10px;">Ordered Qty</th>
            <th style="padding: 10px;">Delivered Qty</th>
            <th style="padding: 10px;">Promised Date</th>
            <th style="padding: 10px;">Actual Date</th>
            <th style="padding: 10px;">Status</th>
            <th style="padding: 10px;">Days Early/Delayed</th>
          </tr>
        </thead>
        <tbody>
          @forelse($deliveryReports as $delivery)
          <tr>
            <td style="padding: 10px;">{{ $delivery->po_number }}</td>
            <td style="padding: 10px;">{{ $delivery->material_code }}</td>
            <td style="padding: 10px;">{{ $delivery->material_description }}</td>
            <td style="padding: 10px;">{{ $delivery->ordered_qty }}</td>
            <td style="padding: 10px;">{{ $delivery->delivered_qty }}</td>
            <td style="padding: 10px;">{{ $delivery->promised_date }}</td>
            <td style="padding: 10px;">{{ $delivery->actual_date }}</td>
            <td style="padding: 10px;">
              @php
                $statusText = '-';
                if ($delivery->actual_date && $delivery->promised_date) {
                  $actual = \Carbon\Carbon::parse($delivery->actual_date);
                  $promised = \Carbon\Carbon::parse($delivery->promised_date);
                  if ($actual->lt($promised)) {
                      $statusText = 'Early';
                  } elseif ($actual->gt($promised)) {
                      $statusText = 'Delayed';
                  } else {
                      $statusText = 'On Time';
                  }
                }
              @endphp
              {{ $statusText }}
            </td>
            <td style="padding: 10px;">{{ $delivery->days_diff }}</td>
          </tr>
          @empty
          <tr><td colspan="9" style="text-align:center; padding:10px;">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>



  {{-- GRN Status Report --}}
  <div class="report-section" id="report-grn" style="display:none;">
  <div class="container mt-4">
    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; color: #333; font-family: 'Inter', sans-serif;">
      <h3 style="color:#1c3e2c; font-weight:600;">
        GRN Status Report
      </h3>

      <form id="filterFormGrn" style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
        <input type="text" id="grn_po_number" name="grn_po_number" placeholder="PO Number" style="padding:6px; border:1px solid #ccc; border-radius:4px;">
        <input type="text" id="grn_line_no" name="grn_line_no" placeholder="Line Item No." style="padding:6px; border:1px solid #ccc; border-radius:4px;">
        <button type="button" id="filterGrnBtn" class="filter-button">Search</button>
      </form>

      <table style="width: 100%; border-collapse: collapse; background: white; font-size: 0.8rem; border: 1px solid #d4d4d4;">
        <thead style="background-color: #35c74b; color: white; text-transform: uppercase; font-weight: 600;">
          <tr>
            <th style="padding: 10px;">PO Number</th>
            <th style="padding: 10px;">GRN Number</th>
            <th style="padding: 10px;">GRN Date</th>
            <th style="padding: 10px;">Line Item</th>
            <th style="padding: 10px;">Received Quantity</th>
            <th style="padding: 10px;">Quality Remarks</th>
            <th style="padding: 10px;">Shortfall / Excess</th>
          </tr>
        </thead>
        <tbody>
          @forelse($grnReports as $grn)
          <tr>
            <td style="padding: 10px;">{{ $grn->order_id }}</td>
            <td style="padding: 10px;">{{ $grn->grn_num }}</td>
            <td style="padding: 10px;">{{ $grn->grn_date }}</td>
            <td style="padding: 10px;">{{ $grn->line_item }}</td>
            <td style="padding: 10px;">{{ $grn->qty_received_by_amg }}</td>
            <td style="padding: 10px;">{{ $grn->remarks }}</td>
            <td style="padding: 10px;">{{ $grn->shortfall_excess }}</td>
          </tr>
          @empty
          <tr><td colspan="7" style="text-align:center; padding:10px;">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>


{{-- Invoice Status Report --}}
<div class="report-section" id="report-invoice" style="display:none;">
  <div class="container mt-4">
    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; color: #333; font-family: 'Inter', sans-serif;">
      <h3 style="color:#1c3e2c; font-weight:600; ">
        Invoice Status Report
      </h3>

      <form id="filterFormInvoice" style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;" onsubmit="return false;">
      <input type="text" id="invoice_po_number" name="invoice_po_number" placeholder="PO Number" style="padding:6px; border:1px solid #ccc; border-radius:4px;">
      <select id="invoice_status" name="invoice_status" style="padding:6px; border:1px solid #ccc; border-radius:4px;">
  <option value="">All Statuses</option>
  @foreach ($invoiceStatuses as $status)
    <option value="{{ $status }}" {{ request('invoice_status') == $status ? 'selected' : '' }}>
      {{ $status }}
    </option>
  @endforeach
</select>

      <button type="button" id="filterInvoiceBtn" class="filter-button">Search</button>
      </form>

      <table style="width: 100%; border-collapse: collapse; background: white; font-size: 0.8rem; border: 1px solid #d4d4d4;">
        <thead style="background-color: #35c74b; color: white; text-transform: uppercase; font-weight: 600;">
          <tr>
            <th style="padding: 10px;">Invoice Number</th>
            <th style="padding: 10px;">PO Number</th>
            <th style="padding: 10px;">Invoice Date</th>
            <th style="padding: 10px;">Invoice Amount</th>
            <th style="padding: 10px;">MIRO Document Number</th>
            <th style="padding: 10px;">Invoice Status</th>
            <th style="padding: 10px;">Rejection Reason</th>
            <th style="padding: 10px;">Expected Payment Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoiceReports as $inv)
          <tr>
            <td style="padding: 10px;">{{ $inv->invoice_number }}</td>
            <td style="padding: 10px;">{{ $inv->purchase_order_id }}</td>
            <td style="padding: 10px;">{{ $inv->invoice_date }}</td>
            <td style="padding: 10px;">${{ number_format($inv->invoice_amount, 2) }}</td>
            <td style="padding: 10px;">{{ $inv->miro_document ?? '-' }}</td>
            <td style="padding: 10px;">{{ $inv->invoice_status }}</td>
            <td style="padding: 10px;">{{ $inv->rejection_reason ?? '-' }}</td>
            <td style="padding: 10px;">{{ $inv->expected_payment_date }}</td>
          </tr>
          @empty
          <tr><td colspan="8" style="text-align:center; padding:10px;">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Payment Status Report --}}
<div class="report-section" id="report-payment" style="display:none;">
  <div class="container mt-4">
    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; color: #333; font-family: 'Inter', sans-serif;">
      <h3 style="color:#1c3e2c; font-weight:600;">Payment Status Report</h3>

      <form id="filterFormPayment" style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;" onsubmit="return false;">
        <input type="text" id="payment_po_number" name="payment_po_number" placeholder="PO / Ref Number" style="padding:6px; border:1px solid #ccc; border-radius:4px;">
        <select name="payment_status" id="payment_status" style="padding:6px; border:1px solid #ccc; border-radius:4px;">
          <option value="">All Statuses</option>
          @foreach($paymentReports->pluck('status')->unique() as $status)
            <option value="{{ $status }}" {{ request('payment_status') == $status ? 'selected' : '' }}>{{ $status }}</option>
          @endforeach
        </select>
        <button type="button" id="filterButtonPayment" class="filter-button">Search</button>
      </form>

      <table style="width: 100%; border-collapse: collapse; background: white; font-size: 0.8rem; border: 1px solid #d4d4d4;">
        <thead style="background: #35c74b; color: white;">
          <tr>
            <th style="padding: 10px;">Invoice Number</th>
            <th style="padding: 10px;">Payment Doc</th>
            <th style="padding: 10px;">Payment Date</th>
            <th style="padding: 10px;">Amount</th>
            <th style="padding: 10px;">Bank Ref</th>
            <th style="padding: 10px;">Status</th>
            <th style="padding: 10px;">Deductions</th>
            <th style="padding: 10px;">Balance</th>
          </tr>
        </thead>
        <tbody>
          @forelse($paymentReports as $pay)
          <tr>
            <td style="padding: 10px;">{{ $pay->invoice_num }}</td>
            <td style="padding: 10px;">{{ $pay->payment_document_number }}</td>
            <td style="padding: 10px;">{{ $pay->payment_date }}</td>
            <td style="padding: 10px;">${{ number_format($pay->amount, 2) }}</td>
            <td style="padding: 10px;">{{ $pay->reference_number }}</td>
            <td style="padding: 10px;">{{ $pay->status }}</td>
            <td style="padding: 10px;">${{ number_format($pay->deductions, 2) }}</td>
            <td style="padding: 10px;">${{ number_format($pay->balance_outstanding, 2) }}</td>
          </tr>
          @empty
          <tr><td colspan="8" style="text-align:center; padding:10px;">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>


{{-- Vendor Performance Dashboard --}}
<div class="report-section" id="report-dashboard" style="display:none;">
  <div class="container mt-4">
    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; color: #333; font-family: 'Inter', sans-serif;">
      <h3 style="color:#1c3e2c; font-weight:600;">
        Overall Vendor Performance Dashboard
      </h3>
      <ul style="background: #f9f9f9; border: 1px solid #d4d4d4; border-radius: 8px; padding: 20px; font-family: 'Inter', sans-serif; font-size: 0.9rem; margin-top: 20px; list-style: none;">
        <li style="padding: 10px 0; border-bottom: 1px solid #e4e4e4;">
          <strong>Total POs Issued:</strong> {{ $totalPos ?? 0 }}
        </li>
        <li style="padding: 10px 0; border-bottom: 1px solid #e4e4e4;">
          <strong>Acknowledged on Time:</strong> {{ $acknowledgedPercent ?? 0 }}%
        </li>
        <li style="padding: 10px 0; border-bottom: 1px solid #e4e4e4;">
          <strong>On-Time Delivery:</strong> {{ $onTimeDeliveryPercent ?? 0 }}%
        </li>
        <li style="padding: 10px 0; border-bottom: 1px solid #e4e4e4;">
          <strong>Vendor Rating Trend:</strong> <span style="color:#35c74b;">Graph coming soon</span>
        </li>
        <li style="padding: 10px 0;">
          <strong>Quarter-over-Quarter Delays:</strong> <span style="color:#35c74b;">Graph coming soon</span>
        </li>
      </ul>
    </div>
  </div>
</div>


{{-- Open Orders / Backlog --}}
<div class="report-section" id="report-backlog" style="display:none;">
  <div class="container mt-4">
    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; color: #333; font-family: 'Inter', sans-serif;">
      <h3 style="color:#1c3e2c; font-weight:600; ">
        Open Orders / Backlog Report
      </h3>

      <form id="filterFormBacklog" style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
        <input type="text" id="backlog_po_number" name="backlog_po_number" placeholder="PO Number" style="padding:6px; border:1px solid #ccc; border-radius:4px;"> 
        <input type="text" id="backlog_line_item" name="backlog_line_item" placeholder="Line Item No." style="padding:6px; border:1px solid #ccc; border-radius:4px;">
        <button type="button" id="filterButtonBacklog" class="filter-button">Search</button>
      </form>

      <table style="width: 100%; border-collapse: collapse; background: white; font-size: 0.8rem; border: 1px solid #d4d4d4;">
        <thead style="background: #35c74b; color: white;">
          <tr>
            <th style="padding: 10px;">PO Number</th>
            <th style="padding: 10px;">Line Item</th>
            <th style="padding: 10px;">Item Description</th>
            <th style="padding: 10px;">Ordered Qty</th>
            <th style="padding: 10px;">Delivered Qty</th>
            <th style="padding: 10px;">Pending Qty</th>
            <th style="padding: 10px;">Delivery Date</th>
          </tr>
        </thead>
        <tbody id="reportBodyBacklog">
          @forelse($backlogReports as $item)
          <tr class="backlog-row"
              data-po-number="{{ $item->po_number }}"
              data-material="{{ $item->line_item }}"
              data-order-date="{{ $item->order_date ?? '' }}"
              data-delivery-date="{{ $item->delivery_date ?? '' }}">
            <td style="padding: 10px;">{{ $item->po_number }}</td>
            <td style="padding: 10px;">{{ $item->line_item }}</td>
            <td style="padding: 10px;">{{ $item->item_description }}</td>
            <td style="padding: 10px;">{{ $item->ordered_qty }}</td>
            <td style="padding: 10px;">{{ $item->delivered_qty }}</td>
            <td style="padding: 10px;">{{ $item->pending_qty }}</td>
            <td style="padding: 10px;">{{ $item->delivery_date ?? '-' }}</td>
          </tr>
          @empty
          <tr><td colspan="7" style="text-align:center; padding:10px;">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>



{{-- Returns & Rejections Report --}}
<div class="report-section" id="report-returns" style="display:none;">
  <div class="container mt-4">
    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; color: #333; font-family: 'Inter', sans-serif;">
      <h3 style="color:#1c3e2c; font-weight:600;">
        Returns & Rejections Report
      </h3>

      <form id="filterFormReturns" style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
      <input type="text" id="returns_po_number" name="returns_po_number" placeholder="PO Number" style="padding:6px; border:1px solid #ccc; border-radius:4px;">
      <input type="text" id="returns_line_item" name="returns_line_item" placeholder="Line Item No." style="padding:6px; border:1px solid #ccc; border-radius:4px;">
      <select id="follow_up_status" name="returns_status" style="padding:6px; border:1px solid #ccc; border-radius:4px;">
  <option value="">All Statuses</option>
  @foreach ($returnStatuses as $status)
    <option value="{{ $status }}" {{ request('returns_status') == $status ? 'selected' : '' }}>
      {{ $status }}
    </option>
  @endforeach
</select>

        <button type="button" id="filterButtonReturns" class="filter-button">Search</button>
      </form>

      <table style="width: 100%; border-collapse: collapse; background: white; font-size: 0.8rem; border: 1px solid #d4d4d4;">
        <thead style="background: #35c74b; color: white;">
          <tr>
            <th style="padding: 10px;">PO Number</th>
            <th style="padding: 10px;">Line Item</th>
            <th style="padding: 10px;">Return Date</th>
            <th style="padding: 10px;">Quantity Returned</th>
            <th style="padding: 10px;">Reason</th>
            <th style="padding: 10px;">Credit Note Issued</th>
            <th style="padding: 10px;">Credit Note Amount</th>
            <th style="padding: 10px;">Follow-up Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($returnsReports as $ret)
          <tr>
            <td style="padding: 10px;">{{ $ret->order_number }}</td>
            <td style="padding: 10px;">{{ $ret->line_item }}</td>
            <td style="padding: 10px;">{{ $ret->return_date }}</td>
            <td style="padding: 10px;">{{ $ret->quantity_returned }}</td>
            <td style="padding: 10px;">{{ $ret->reason }}</td>
            <td style="padding: 10px;">{{ $ret->credit_note_issued }}</td>
            <td style="padding: 10px;">${{ number_format($ret->credit_note_amount, 2) }}</td>
            <td style="padding: 10px;">{{ $ret->follow_up_status }}</td>
          </tr>
          @empty
          <tr><td colspan="8" style="text-align:center; padding:10px;">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>



  {{-- Additional report sections will go here --}}
</div>

<style>
    :root {
      --green: #35c74b;
      --light-green: #d0edd9;
      --dark-green: #257f4fff;
      --red: #d62828;
      --gray: #e0e0e0;
      --bg: #f5f7f8;
      --font: 'Inter', sans-serif;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: var(--font);
      background: var(--bg);
      color: var(--dark-green);
      font-size: 13px;
      margin: 0;
    }

    .report-tabs {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      padding: 0 0 16px 7px;
    }

    .report-tab {
      background: var(--green);
      color: #fff;
      padding: 7px 15px;
      font-size: 14px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.2s ease;
    }

    .report-tab:hover {
      background: var(--dark-green);
    }

    .report-tab.active {
      background: var(--dark-green);
    }

    .report-section {
      display: none;
    }

    .report-section.active {
      display: block;
    }

    .report-section h3 {
      font-size: 15px;
      margin-bottom: 10px;
      border-bottom: 1px solid var(--dark-green);
      padding-bottom: 6px;
    }

    form {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      
    }

    input, select {
      padding: 6px 10px;
      font-size: 13px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background: #fff;
    }

    .filter-button {
      background: var(--red);
      color: #fff;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 13px;
    }

    .filter-button:hover {
      background: #b81e1e;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      font-size: 12px;
      border-radius: 6px;
      overflow: hidden;
      box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }

    th, td {
      text-align: left;
      padding: 8px 10px;
      border: 1px solid #ccc;
    }

    thead {
      background: var(--green);
      color: #fff;
      text-transform: uppercase;
    }

    tr:hover td {
      background: #f0fdf4;
    }

    @media (max-width: 768px) {
      form {
        flex-direction: column;
      }

      .report-tab {
        flex: 1 1 100%;
        text-align: center;
      }

      input, select, .filter-button {
        width: 100%;
      }
    }
  </style>
