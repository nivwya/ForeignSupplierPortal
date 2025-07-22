<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Portal - AlMulla Group</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      background: #f4f6f8;
      color: #2e3a59;
    }

    .sidebar {
      width: 240px;
      background: #2b7c31ff;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
      transition: width 0.3s ease;
      position: relative;
      padding-top:70px;
      position: relative;
      top: 0;

    }

    .sidebar.collapsed {
      padding-top: 70px;
      width: 64px;
    }

    .sidebar-toggle {
      position: absolute;
      top: 10px;
      right: 10px;
      color: white;
      cursor: pointer;
      font-size: 1.2rem;
    }
    .sidebar-brand {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 30px;
  padding: 0 18px;
  cursor: pointer;
  transition: padding 0.3s ease;
}

.sidebar-logo {
  height: 28px;
  width: 28px;
  border-radius: 4px;
}

.sidebar-title {
  font-size: 1.05rem;
  font-weight: 600;
  white-space: nowrap;
  transition: opacity 0.3s ease, visibility 0.3s ease;
  color: #e0e0e0;
}
.sidebar.collapsed .sidebar-title {
  opacity: 0;
  visibility: hidden;
}

.sidebar.collapsed .sidebar-brand {
  padding: 0 10px;
}


    .sidebar h2 {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 30px;
      transition: opacity 0.3s ease;
    }

    .sidebar.collapsed h2 {
      opacity: 0;
    }

    .nav-tab {
      width: 100%;
      padding: 12px 24px;
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: all 0.3s ease;
    }
    .sidebar.collapsed .nav-tab {
  position: relative;
}

.sidebar.collapsed .nav-tab:hover::after {
  content: attr(data-label);
  position: absolute;
  left: 100%;
  top: 50%;
  transform: translateY(-50%);
  background: #1fae4b;
  color: white;
  font-size: 1rem; /* bigger font */
  padding: 8px 14px; /* bigger box */
  border-radius: 8px;
  white-space: nowrap;
  margin-left: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  z-index: 1000;
  font-weight: 600;
}



    .nav-tab:hover,
    .nav-tab.active {
      background-color: #256b2b;
      padding-left: 32px;
    }

    .nav-tab i {
      font-size: 18px;
      min-width: 20px;
      text-align: center;
      color: #e0e0e0;
      
    }

    .sidebar.collapsed .nav-tab .label {
      display: none;
    }

    .main-content {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }

    .header {
      background:rgba(129, 216, 136, 1);
      color: white;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header .left-section {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .header .right-section {
      display: flex;
      gap: 12px;
    }

    .almulla-logo {
      height: 40px;
      border-radius: 6px;
      transition: transform 0.3s ease;
    }

    .almulla-logo:hover {
      transform: scale(1.05);
    }

    .vendor-profile {
      background: rgba(255,255,255,0.2);
      color: white;
      padding: 8px 14px;
      border-radius: 5px;
      font-weight: 500;
      text-decoration: none;
    }

    .vendor-profile:hover {
      background: rgba(255,255,255,0.3);
    }

    .logout-form {
      display: inline-block;
    }

    .logout-form button {
      background: #e74c3c;
      border: none;
      padding: 8px 14px;
      color: white;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 500;
    }

    #tab-content {
      padding: 30px;
      margin: 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.05);
      min-height: 300px;
    }

    @media (max-width: 768px) {
  .sidebar {
    position: absolute;
    top:60px; /* adjust based on header height */
    left: 0;
    z-index: 999;
  }

  .main-content {
    margin-left: 0;
  }
}
.user-icon {
  font-size: 24px;
  color: white;
  cursor: pointer;
  padding: 6px;
  border-radius: 50%;
  transition: background 0.2s ease;
}

/*user icon*/
.user-icon:hover {
  background: rgba(255,255,255,0.2);
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown:hover .dropdown-menu {
  display: block;
}

.dropdown-menu {
  display: none;
  position: absolute;
  right: 0;
  top: 38px;
  background: white;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  border-radius: 8px;
  overflow: hidden;
  z-index: 100;
  min-width: 160px;
  font-size: 0.85rem;
  transition: all 0.3s ease;
}

.dropdown-menu a,
.dropdown-menu form button {
  display: block;
  width: 100%;
  text-align: left;
  padding: 10px 14px;
  background: white;
  border: none;
  outline: none;
  cursor: pointer;
  text-decoration: none;
  color: #2e3a59;
  font-weight: 500;
}

.dropdown-menu a:hover,
.dropdown-menu form button:hover {
  background: #cdcdcdff;
  color: #256b2b;
}
</style>
</head>
<body>
  <div style="display: flex; min-height: 100vh;">
  <div class="sidebar collapsed" id="sidebar">
  <a href="#" class="nav-tab active" data-tab="admin-home" data-label="Home"><i class="fa fa-home"></i><span class="label">Home</span></a>
  <a href="#" class="nav-tab" data-tab="orders" data-label="Orders"><i class="fa fa-list"></i><span class="label">Orders</span></a>
  <a href="#" class="nav-tab" data-tab="deliveries" data-label="Deliveries"><i class="fa fa-truck"></i><span class="label">Deliveries</span></a>
  <a href="#" class="nav-tab" data-tab="payments" data-label="Payments"><i class="fa fa-money"></i><span class="label">Payments</span></a>
  <a href="#" class="nav-tab" data-tab="invoices" data-label="Invoices"><i class="fa fa-file-text"></i><span class="label">Invoices</span></a>
  <a href="#" class="nav-tab" data-tab="reports" data-label="Reports"><i class="fa fa-bar-chart"></i><span class="label">Reports</span></a>
</div>

<div class="main-content">
  <div class="header">
  <div class="left-section" onclick="toggleSidebar()" style="cursor:pointer;">
    <img src="{{ asset('almulla-logo-small.png') }}" alt="Logo" class="almulla-logo">
    <h3>Admin Portal – Al Mulla Group</h3>
  </div>

  <div class="right-section">
  <div class="dropdown">
  <i class="fa fa-user-circle user-icon"></i>
  <div class="dropdown-menu" id="userDropdown">
    <!--changes made by niveditha-->
    <a href="{{ route('admin.profile') }}">Admin Profile</a>
    <!--changes end-->
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit">Logout</button>
    </form>
  </div>
</div>
</div>
</div>
      <div id="tab-content">
        <p>Welcome to the Admin Portal. Select a section from the sidebar to begin.</p>
      </div>
    </div>
  </div>
</body>
</html>

<script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('collapsed');
}
function loadTabContent(tab) {
    const tabContent = document.getElementById('tab-content');
    let file = '';
    if (tab === 'admin-home') file = '/admin/home-tab';
    else if (tab === 'orders') file = '/admin/orders'; // <-- Use the correct route for Orders tab
    else if (tab === 'deliveries') file = '/admin/deliveries-content';
    else if (tab === 'invoices') file = '/invoices-content';
    else if (tab === 'payments') file = '/payments-content';
    else if (tab === 'reports') file = '/reports-content';
    else { tabContent.innerHTML = ''; return; }

    fetch(file, { credentials: 'include' })
        .then(response => response.text())
        .then(html => {
            tabContent.innerHTML = html;
            if (tab === 'orders') {
                attachOrdersTabHandlers();
            }
            if (tab === 'admin-home') {
                attachAdminHomeTabHandlers();
                loadStatCards();
            }
            if (tab === 'reports') {
              attachReportsTabsHandler();
              attachPoAckFilterHandler();
              attachDeliveryFilterHandler();
              attachGrnFilterHandler();
              attachInvoiceFilterHandler();
              attachBacklogFilterHandler();
              attachReturnsFilterHandler();

            }
        });
}
document.querySelectorAll('.nav-tab').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        loadTabContent(this.getAttribute('data-tab'));
    });
});
loadTabContent('admin-home');
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
document.getElementById('tab-content').addEventListener('click', function(e) {
        // Expand/collapse PO line items
        const poLink = e.target.closest('.po-link');
        if (poLink) {
            e.preventDefault();
            const orderId = poLink.dataset.orderId;
            const itemsRow = document.getElementById('po-items-' + orderId);
            const container = itemsRow.querySelector('.po-items-container');
            if (itemsRow.style.display === 'none') {
                if (!container.dataset.loaded) {
                    fetch(`/deliveries/${orderId}/items`)
                        .then(response => response.text())
                        .then(html => {
                            container.innerHTML = html;
                            container.dataset.loaded = "1";
                            itemsRow.style.display = '';
                            // No need to re-attach handlers; delegation covers all
                        });
                } else {
                    itemsRow.style.display = '';
                }
            } else {
                itemsRow.style.display = 'none';
            }
            return;
        }
});

function attachOrdersTabHandlers() 
{
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
                // No need to re-attach per-row handlers!
            })
            .catch(() => {
                alert('Failed to search. Please try again.');
            });
        });
    }
     const poFileInput = document.getElementById('po_file');
    if (poFileInput) {
        poFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            document.getElementById('file-info').innerText = file ? 'Selected file: ' + file.name : '';
        });
    }
    const poUploadForm = document.getElementById('po-upload-form');
    if (poUploadForm) {
        poUploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            document.getElementById('upload-status').innerText = 'Uploading and processing...';
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('upload-status').innerText = '';
                if (data.success) {
                    let itemsRows = '';
                    data.items.forEach(item => {
                        itemsRows += `
                            <tr>
                                <td>${item.line_item_no}</td>
                                <td>${item.product_code}</td>
                                <td>${item.item_description}</td>
                                <td>${item.quantity}</td>
                                <td>${item.uom}</td>
                                <td>${item.price}</td>
                                <td>${(parseFloat(item.quantity) * parseFloat(item.price)).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                  const html = `
                        <div class="po-verify-flex" style="display: flex; gap: 2.5rem; align-items: flex-start; font-family: 'Inter', sans-serif; color: #2c3e50;">
                            <!-- PO Details Section (wider) -->
                            <div style="flex: 1.2; background: #f8fafc; border-radius: 10px; box-shadow: 0 2px 8px rgb(255, 255, 255); padding: 2rem;">
                            <h3 style="font-size: 1.1rem; color: #2e7d32; font-weight: 600; letter-spacing: 0.01em; margin-bottom: 1.5rem;">PO Details</h3>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem; border: 1px solid #e0e0e0;">
                                <tbody>
                                <tr style="border-bottom: 1px solid #e0e0e0;">
                                    <th style="text-align: left; color: #fff; padding: 10px;">PO Number</th>
                                    <td style="padding: 10px; font-weight: 500;">${data.po.order_number}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e0e0e0;">
                                    <th style="text-align: left; color: #fff; padding: 10px;">Vendor ID</th>
                                    <td style="padding: 10px;">${data.po.vendor_id || '<span style="color:#e74c3c;">N/A</span>'}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e0e0e0;">
                                    <th style="text-align: left; color: #fff; padding: 10px;">Order Date</th>
                                    <td style="padding: 10px;">${data.po.order_date}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left; color: #fff; padding: 10px;">Status</th>
                                    <td style="padding: 10px;">${data.po.status}</td>
                                </tr>
                                </tbody>
                            </table>

                            <h4 style="margin-top: 2rem; margin-bottom: 1rem; font-size: 1.05rem; color: #2e3a59; font-weight: 600;">Line Items</h4>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; border: 1px solid #d0d0d0; font-size: 0.92rem; background: #fff;">
                                <thead style="background: #e8f5e9; color: #2e7d32;">
                                    <tr>
                                    <th style="padding: 10px; border: 1px solid #d0d0d0; text-align: left;">Line</th>
                                    <th style="padding: 10px; border: 1px solid #d0d0d0; text-align: left;">Product Code</th>
                                    <th style="padding: 10px; border: 1px solid #d0d0d0; text-align: left;">Description</th>
                                    <th style="padding: 10px; border: 1px solid #d0d0d0; text-align: right;">Qty</th>
                                    <th style="padding: 10px; border: 1px solid #d0d0d0; text-align: left;">UOM</th>
                                    <th style="padding: 10px; border: 1px solid #d0d0d0; text-align: right;">Price</th>
                                    <th style="padding: 10px; border: 1px solid #d0d0d0; text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsRows}
                                </tbody>
                                </table>
                            </div>
                            </div>

                            <!-- PDF Viewer Section -->
                            <div style="flex: 1; background: #ffffff; border-radius: 10px; box-shadow: 0 2px 8px rgba(44, 62, 80, 0.06); padding: 2rem;">
                            <h3 style="font-size: 1.1rem; color: #2e7d32; font-weight: 600; letter-spacing: 0.01em; margin-bottom: 1.5rem;">PO PDF</h3>
                            <iframe src="${data.pdf_url}" width="100%" height="520px" style="border: 1px solid #e0e0e0; border-radius: 6px; background: #fafbfc;"></iframe>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div style="margin-top: 2.5rem; text-align: center;">
                            <button id="proceed-btn"
                            style="background: linear-gradient(90deg, #2e7d32 0%, #43e97b 100%);
                                    color: #ffffff;
                                    padding: 0.9rem 2.8rem;
                                    border: none;
                                    border-radius: 9px;
                                    font-size: 1.1rem;
                                    font-weight: 600;
                                    cursor: pointer;
                                    box-shadow: 0 2px 8px rgba(46, 125, 50, 0.1);
                                    transition: background 0.3s, transform 0.2s;">
                            &#10003; Proceed & Attach to PO
                            </button>
                        </div>

                        <style>
                            #proceed-btn:hover {
                            background: linear-gradient(90deg, #388e3c 0%, #66ffa6 100%);
                            transform: translateY(-2px);
                            }
                            table td, table th {
                            vertical-align: top;
                            }
                            tbody td {
                            border: 1px solid #d0d0d0;
                            padding: 10px;
                            }
                        </style>
                        `;
                    document.getElementById('verification-section').innerHTML = html;

                    // Attach event handler for proceed button
                    document.getElementById('proceed-btn').onclick = function() {
                        fetch('/admin/confirm-po-attachment', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                po_id: data.po.id,
                                tmp_pdf_path: data.tmp_pdf_path
                            })
                        })
                        .then(res => res.json())
                        .then(resp => {
                            alert(resp.message);
                            showCustomNotification(resp.message);
                            window.location.reload(); // Refresh after success
                        });
                    };
                } else {
                    document.getElementById('verification-section').innerHTML = '';
                    alert(data.message);
                }
            })
            .catch(() => {
                document.getElementById('upload-status').innerText = '';
                alert('Upload failed. Please try again.');
            });
        });
    }
 toggleReleaseAllBtn();
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('close-split-screen-btn')) {
        const splitRow = e.target.closest('.split-screen-row');
        if (splitRow) splitRow.remove();
    }
    if (e.target.classList.contains('attach-pdf-row-btn')) {
        // Remove any existing split-screen
        document.querySelectorAll('.split-screen-row').forEach(el => el.remove());

        const row = e.target.closest('tr');
        const poid = e.target.dataset.poid;
        fetch(`/admin/orders/${poid}/split-view`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            // Insert split-screen view after the row
            const newRow = document.createElement('tr');
            newRow.className = 'split-screen-row';
            newRow.innerHTML = `<td colspan="11">${html}</td>`;
            row.parentNode.insertBefore(newRow, row.nextSibling);
        });
    }
    if (e.target.classList.contains('remove-pdf-row-btn')) {
        const poid = e.target.dataset.poid;
        if (!confirm('Remove the attached PDF for this PO?')) return;
        fetch('/admin/orders/remove-pdf-row', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            //changes by niveditha
            body: JSON.stringify({ po_number: poid })
            //changes end
        })
        .then(res => res.json())
        .then(resp => {
            alert(resp.message);
            showCustomNotification(resp.message);
            loadTabContent('orders');
        });
        return;
    }
    // Per-row Issue PO
    if (e.target.classList.contains('issue-po-row-btn')) {
        const poid = e.target.dataset.poid;
        if (!confirm('Release this PO?')) return;
        fetch('/admin/orders/issue-po-row', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ po_id: poid })
        })
        .then(res => res.json())
        .then(resp => {
            alert(resp.message);
            showCustomNotification(resp.message);
            loadTabContent('orders');
        });
        return;
    }
    // Order row click (show items)
    if (e.target.classList.contains('order-link')) {
        e.preventDefault();
        const orderId = e.target.closest('tr').dataset.orderId;
        fetch(`/admin/orders/${orderId}/items`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('order-items-details').innerHTML = html;
        });
        return;
    }
    if (e.target.id === 'release-all-btn') {
            if (!confirm('Release all filtered orders with PDF attached?')) return;

            // Get current filter values
            const searchForm = document.getElementById('orders-search-form');
            const formData = new FormData(searchForm);
            const params = new URLSearchParams(formData).toString();

            fetch('/admin/orders/release-all?' + params, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(resp => {
                alert(resp.message);
                showCustomNotification(resp.message);
                loadTabContent('orders');
            });
        }
    toggleReleaseAllBtn();
});
document.addEventListener('submit', function(e) {
    if (e.target.classList.contains('attach-pdf-form')) {
        e.preventDefault();
        const form = e.target;
        const poid = form.dataset.poid;
        const fileInput = form.querySelector('input[type="file"]');
        const statusSpan = form.querySelector('.upload-status');
        const file = fileInput.files[0];
        if (!file) {
            statusSpan.textContent = 'Please select a PDF file.';
            return;
        }
        statusSpan.textContent = 'Uploading...';
        const formData = new FormData();
        formData.append('po_file', file);
        formData.append('po_id', poid);
        fetch('/admin/orders/attach-po-row', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            statusSpan.textContent = resp.message;
            setTimeout(() => { loadTabContent('orders'); }, 1200);
        })
        .catch(() => {
            statusSpan.textContent = 'Upload failed.';
        });
    }
    toggleReleaseAllBtn();
});
 function loadStatCards() {
    // Not Released
    fetch('/admin/purchase-orders/all-not-released')
        .then(res => res.json())
        .then(data => {
            document.getElementById('stat-not-released').textContent = data.length;
        });
    // Not Acknowledged
    fetch('/admin/purchase-orders/all-not-acknowledged')
        .then(res => res.json())
        .then(data => {
            document.getElementById('stat-not-acknowledged').textContent = data.length;
        });
    // Not Delivered
    fetch('/admin/purchase-orders/all-not-delivered')
        .then(res => res.json())
        .then(data => {
            document.getElementById('stat-not-delivered').textContent = data.length;
        });
    // Partial Delivery
    fetch('/admin/purchase-orders/all-partial-delivery')
        .then(res => res.json())
        .then(data => {
            document.getElementById('stat-partial-delivery').textContent = data.length;
        });
}

function showCustomNotification(message) {
    const notif = document.getElementById('custom-notification');
    notif.textContent = message;
    notif.style.display = 'block';
    setTimeout(() => {
        notif.style.display = 'none';
    }, 3000); // Hide after 3 seconds
}
function attachAdminHomeTabHandlers() {
  const assignForm = document.getElementById('assign-admin-form');
    if (assignForm) {
        assignForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(assignForm);
            fetch('/admin/assign-admin', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('admin-action-result').textContent = data.message || 'Admin assigned.';
                assignForm.reset();
            })
            .catch(() => {
                document.getElementById('admin-action-result').textContent = 'Failed to assign admin.';
            });
        });
    }
    const removeForm = document.getElementById('remove-admin-form');
    if (removeForm) {
        removeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(removeForm);
            fetch('/admin/remove-admin', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('admin-action-result').textContent = data.message || 'Admin revoked.';
                removeForm.reset();
            })
            .catch(() => {
                document.getElementById('admin-action-result').textContent = 'Failed to revoke admin.';
            });
        });
    }

}
function attachReportsTabsHandler() {
    const tabs = document.querySelectorAll(".report-tab");
    const sections = document.querySelectorAll(".report-section");

    if (!tabs.length || !sections.length) return;

    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            const reportId = tab.dataset.report;

            // Remove 'active' class from all buttons
            tabs.forEach(btn => btn.classList.remove("active"));
            tab.classList.add("active");

            // Hide all report sections
            sections.forEach(sec => sec.style.display = "none");

            // Show the clicked report section
            const activeSection = document.getElementById(`report-${reportId}`);
            if (activeSection) {
                activeSection.style.display = "block";
            }
        });
    });
    if (tabs.length > 0) {
        tabs[0].click();
    }
}
function attachPoAckFilterHandler() {
    const filterButton = document.querySelector('#report-po-ack #filterButton');
    if (!filterButton) return;

    filterButton.addEventListener('click', () => {
        const poNumber = document.querySelector('#report-po-ack #ack_po_number')?.value || '';
        const status = document.querySelector('#report-po-ack #ack_status')?.value || '';
        const params = new URLSearchParams({
            ack_po_number: poNumber,
            ack_status: status,
        });
        fetch(`/reports-content?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newSection = doc.querySelector('#report-po-ack');
            const currentSection = document.querySelector('#report-po-ack');
            if (newSection && currentSection) {
                currentSection.innerHTML = newSection.innerHTML;
                attachPoAckFilterHandler(); // Reattach after update
            }
        });
    });
}
function attachDeliveryFilterHandler() {
  const filterButton = document.querySelector('#report-delivery #filterDeliveryBtn');
  if (!filterButton) return;
  filterButton.addEventListener('click', () => {
    const status = document.querySelector('#report-delivery #delivery_status')?.value || '';
    const poNumber = document.querySelector('#report-delivery #delivery_po_number')?.value || '';
    const params = new URLSearchParams({
      delivery_status: status,
      po_number: poNumber,
    });
    fetch(`/reports-content?${params.toString()}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newSection = doc.querySelector('#report-delivery');
      const currentSection = document.querySelector('#report-delivery');
      if (newSection && currentSection) {
        currentSection.innerHTML = newSection.innerHTML;
        attachDeliveryFilterHandler();
      }
    });
  });
}

function attachGrnFilterHandler() {
  const filterButton = document.querySelector('#report-grn #filterGrnBtn');
  if (!filterButton) return;
  filterButton.addEventListener('click', () => {
    const poNumber = document.querySelector('#report-grn #grn_po_number')?.value || '';
    const materialCode = document.querySelector('#report-grn #grn_line_no')?.value || '';
    const params = new URLSearchParams({
      grn_po_number: poNumber,
      grn_line_no: materialCode,
    });
    fetch(`/reports-content?${params.toString()}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newSection = doc.querySelector('#report-grn');
      const currentSection = document.querySelector('#report-grn');
      if (newSection && currentSection) {
        currentSection.innerHTML = newSection.innerHTML;
        attachGrnFilterHandler();
      }
    });
  });
}
function attachInvoiceFilterHandler() {
  const filterBtn = document.querySelector('#report-invoice #filterInvoiceBtn');
  if (!filterBtn) return;
  filterBtn.addEventListener('click', () => {
    const status = document.querySelector('#report-invoice #invoice_status')?.value || '';
    const poNumber = document.querySelector('#report-invoice #invoice_po_number')?.value || '';
    const params = new URLSearchParams();
    if (status) params.append('invoice_status', status);
    if (poNumber) params.append('invoice_po_number', poNumber);
    fetch(`/reports-content?${params.toString()}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newSection = doc.querySelector('#report-invoice');
      const currentSection = document.querySelector('#report-invoice');
      if (newSection && currentSection) {
        currentSection.innerHTML = newSection.innerHTML;
        attachInvoiceFilterHandler(); // re-bind after replacement
      }
    });
  });
}

function attachBacklogFilterHandler() {
  const filterBtn = document.querySelector('#filterButtonBacklog');
  if (!filterBtn) return;
  filterBtn.addEventListener('click', () => {
    const poNumber = document.querySelector('#backlog_po_number')?.value || '';
    const lineItem = document.querySelector('#backlog_line_item')?.value || '';
    const params = new URLSearchParams({
      backlog_po_number: poNumber,
      backlog_line_item: lineItem
    });

    fetch(`/reports-content?${params.toString()}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newSection = doc.querySelector('#report-backlog');
      const currentSection = document.querySelector('#report-backlog');
      if (newSection && currentSection) {
        currentSection.innerHTML = newSection.innerHTML;
        attachBacklogFilterHandler();
      }
    });
  });
}
function attachReturnsFilterHandler() {
    const filterButton = document.querySelector('#filterButtonReturns');
    if (!filterButton) return;
    filterButton.addEventListener('click', () => {
        const lineItem = document.querySelector('#returns_line_item')?.value || '';
        const poNumber = document.querySelector('#returns_po_number')?.value || '';
        const returnStatus = document.querySelector('#follow_up_status')?.value || '';
        const params = new URLSearchParams({
            returns_line_item: lineItem,
            returns_po_number: poNumber,
            returns_status: returnStatus, // FIXED key
        });
        fetch(`/reports-content?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newSection = doc.querySelector('#report-returns');
            const currentSection = document.querySelector('#report-returns');
            if (newSection && currentSection) {
                currentSection.innerHTML = newSection.innerHTML;
                attachReturnsFilterHandler();
            }
        });
    });
}
</script>
</body>
</html>
