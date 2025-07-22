{{-- resources/views/admin/admin_orders.blade.php --}}

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

    .side-by-side-container {
    display: flex;
    gap: 15rem;
    align-items: flex-start;
    margin-bottom: 3rem;
}

.po-upload-section {
    min-width: 260px;
}

.po-upload-section h3 {
    font-size: 1.2rem;
    color: #2e7d32;
    font-weight: 600;
}

#po-upload-form {
    display: flex;
    gap: 1rem;
    align-items: center;
}

#po-upload-form input[type="file"] {
    padding: 0.6rem 1rem;
    border: 2px dashed #cbd5e1;
    border-radius: 10px;
}

#po-upload-form button {
    background: linear-gradient(90deg, #2e7d32 0%, #43e97b 100%);
    color: #fff;
    border: none;
    border-radius: 9px;
    padding: 0.9rem 2.5rem;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 0.5rem;
}

#upload-status, #verification-section {
    margin-top: 1rem;
    color: #e67e22;
    font-weight: 500;
    grid-column: 1 / -1;
    margin-bottom: 3rem;
}

</style>
<div id="admin-orders-section">
    <div class="side-by-side-container">
        <div class="po-upload-section">
            <h3>📎 Attach Purchase Order (PO)</h3>
            <form id="po-upload-form" method="POST" action="{{ route('orders.attachPo') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" id="po_file" name="po_file" accept="application/pdf" required>
                <button type="submit">Upload & Preview</button>
            </form>
        </div
        <form id="orders-search-form" method="GET" action="{{ route('orders.table') }}">
            <input type="text" name="order_number" placeholder="PO Number" value="{{ request('order_number') }}">
            <input type="text" name="company" placeholder="Company" value="{{ request('company') }}">
            <input type="text" name="department" placeholder="Department" value="{{ request('department') }}">
            <select name="status">
                <option value="">All Statuses</option>
                <option value="not verified" @if(request('status')=='not verified') selected @endif>Not Verified</option>
                <option value="issued" @if(request('status')=='issued') selected @endif>Issued</option>
                <option value="acknowledged" @if(request('status')=='acknowledged') selected @endif>Acknowledged</option>
                <option value="delivered" @if(request('status')=='delivered') selected @endif>Delivered</option>
                <option value="partial delivery" @if(request('status')=='partial delivery') selected @endif>Partial Delivery</option>
            </select>
            <button type="submit">Search</button>
        </form>
    </div>

    <div id="upload-status" style="margin-top: 1rem; color: #e67e22; font-weight: 500; display: none;">
    <span id="upload-status-message"></span>
    <button id="upload-status-close" type="button" style="background: none; border: none; color: #b71c1c; font-size: 1.2rem; margin-left: 1rem; cursor: pointer; font-weight: bold; display: none;">&times;</button>
</div>
    <div id="verification-section"></div>

    <div id="orders-table-container">
        <button id="release-all-btn"
            style="background: linear-gradient(90deg,#36d1c4 0%,#5b86e5 100%);
                   color: #fff; padding: 0.7rem 2.2rem; border:none; border-radius:8px;
                   font-weight:600; margin-bottom:1.3rem; font-size:1rem; cursor:pointer; display:none;">
            &#9889; Release All With PDF (Filtered)
        </button>
        @include('admin.admin_orders_table', ['orders' => $orders])
        {{ $orders->links() }}
    </div>
    <div id="order-items-details"></div>
</div>


<script>
// Only static form handlers here!
document.addEventListener('DOMContentLoaded', function() {
    // Search form AJAX
    const searchForm = document.getElementById('orders-search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(searchForm);
            const params = new URLSearchParams(formData).toString();
            fetch(searchForm.action + '?' + params, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('orders-table-container').innerHTML = html;
                attachOrdersTablePagination();
                toggleReleaseAllBtn(); // <--- Add this line
            })
            .catch(() => {
                alert('Failed to search. Please try again.');
            });
        });
    }

    const poUploadForm = document.getElementById('po-upload-form');
const uploadStatus = document.getElementById('upload-status');
const uploadStatusMessage = document.getElementById('upload-status-message');
const uploadStatusClose = document.getElementById('upload-status-close');

if (poUploadForm) {
    poUploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        // Show status and close button
        uploadStatus.style.display = 'block';
        uploadStatusMessage.innerText = 'Uploading and processing...';
        uploadStatusClose.style.display = 'inline';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            uploadStatusMessage.innerText = 'Upload complete!'; // Or your custom logic
            // ... Your PO verification/preview logic here ...
        })
        .catch(() => {
            uploadStatusMessage.innerText = 'Upload failed. Please try again.';
        });
    });

    // Close button handler
    uploadStatusClose.addEventListener('click', function() {
        uploadStatus.style.display = 'none';
        uploadStatusMessage.innerText = '';
        uploadStatusClose.style.display = 'none';
        // Optionally, reset the file input if you want:
        poUploadForm.reset();
    });
}
    function attachOrdersTablePagination() {
    document.querySelectorAll('#orders-table-container .pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            fetch(this.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    // THIS IS THE SUCCESS HANDLER:
                    document.getElementById('orders-table-container').innerHTML = html;
                    attachOrdersTablePagination(); // re-attach for new links
                    toggleReleaseAllBtn(); // <--- Add this line
                });
        });
    });
    function toggleReleaseAllBtn() {
    const table = document.getElementById('orders-table');
    const releaseAllBtn = document.getElementById('release-all-btn');
    if (!table || !releaseAllBtn) return;
    let show = false;
    table.querySelectorAll('tbody tr').forEach(tr => {
        const pdfCell = tr.querySelector('td:nth-child(10)');
        if (pdfCell && pdfCell.querySelector('a')) {
            show = true;
        }
    });
    releaseAllBtn.style.display = show ? 'inline-block' : 'none';
}
}
attachOrdersTablePagination();
toggleReleaseAllBtn();

});


function toggleReleaseAllBtn() {
    const table = document.getElementById('orders-table');
    const releaseAllBtn = document.getElementById('release-all-btn');
    if (!table || !releaseAllBtn) return;
    let show = false;
    table.querySelectorAll('tbody tr').forEach(tr => {
        const pdfCell = tr.querySelector('td:nth-child(10)');
        if (pdfCell && pdfCell.querySelector('a')) {
            show = true;
        }
    });
    releaseAllBtn.style.display = show ? 'inline-block' : 'none';
}

</script>
