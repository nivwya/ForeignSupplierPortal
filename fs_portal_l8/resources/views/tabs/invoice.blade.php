<style>
        body {
        background: #eef5ed;
        font-family: 'Inter', Arial, sans-serif;
        margin: 0;
        padding: 0;
        }
        .container {
    width: 95%; /* Add this line to control width responsively */
    max-width: 1400px; /* Increased from 1300px to make tables slightly wider */
    margin: 40px auto;
    background: #f5f9f4;
    padding: 30px 30px 50px 30px;
    border-radius: 8px;
}
        table {
        border-collapse: collapse;
        width: 100%;
        background: #fff;
        margin-bottom: 32px;
        }
        th, td {
        border: 1.5px solid #b7cbb3;
        padding: 8px 11px;
        text-align: center;
        font-size: 1rem;
        }
        th {
        background: #f7e9b0;
        color: #222;
        font-weight: 600;
        }
        tr:nth-child(even) td {
        background: #f8fbe9;
        }
        .status-delivered {
        background: #b6eab7;
        color: #187f3c;
        font-weight: 600;
        border-radius: 4px;
        padding: 4px 10px;
        display: inline-block;
        }
        .status-issued {
        background:rgb(228, 238, 228);
        color:rgb(107, 107, 107);
        font-weight: 600;
        border-radius: 4px;
        padding: 4px 10px;
        display: inline-block;
        }
        .status-notdelivered {
        background: #f6c7c7;
        color: #a81d1d;
        font-weight: 500;
        border-radius: 4px;
        padding: 3px 1px;
        display: inline-block;
        }
        .section-title {
        background: #f7e9b0;
        color: #222;
        font-weight: bold;
        font-size: 1.15rem;
        padding: 7px 16px;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        display: inline-block;
        margin-bottom: 0;
        }
</style>
<div class="container">
    <h2>Invoices</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order no.</th>
                <th>Order date</th>
                <th>Company</th>
                <th>Department</th>
                <th>Order Value</th>
                <th>Currency</th>
                <th>Invoice amount Received by AMG</th>
                <th>Invoice amount paid by AMG</th>
                <th>Invoice amount to be paid by AMG</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($invoices as $invoice)
            <tr>
                <td>{{ $invoice->purchaseOrder->order_number }}</td>
                <td>{{ $invoice->purchaseOrder->order_date }}</td>
                <td>{{ $invoice->purchaseOrder->company }}</td>
                <td>{{ $invoice->purchaseOrder->department }}</td>
                <td>{{ $invoice->purchaseOrder->order_value }}</td>
                <td>{{ $invoice->purchaseOrder->currency }}</td>
                <td>{{ $invoice->amount }}</td>
                <td>{{ $invoice->amount_paid }}</td>
                <td>{{ $invoice->amount_due }}</td>
                <td>
                    @if(!$invoice->invoice_pdf)
                        <!-- Show upload form if invoice not yet uploaded -->
                        <form method="POST" action="{{ route('invoices.upload', $invoice->id) }}" enctype="multipart/form-data" style="display:inline;">
                            @csrf
                            <input type="file" name="invoice_pdf" accept="application/pdf" required>
                            <input type="text" name="invoice_number" placeholder="Invoice Number" required>
                            <input type="date" name="invoice_date" required>
                            <input type="number" name="amount" placeholder="Amount" required min="1" step="0.01">
                            <button type="submit">Upload Invoice</button>
                        </form>
                    @else
                        <a href="{{ url('/invoices/'.$invoice->id.'/download-pdf') }}" target="_blank">Download PDF</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
