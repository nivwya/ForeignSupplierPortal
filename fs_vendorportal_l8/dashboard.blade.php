<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Vendor Portal - AlMulla Group</title>
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
  <a href="#" class="nav-tab active" data-tab="home" data-label="Home"><i class="fa fa-home"></i><span class="label">Home</span></a>
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
    <h3>Vendor Portal – Al Mulla Industries</h3>
  </div>

  <div class="right-section">
  <div class="dropdown">
  <i class="fa fa-user-circle user-icon"></i>
  <div class="dropdown-menu" id="userDropdown">
    <a href="/vendor/profile/{{ str_pad(auth()->user()->id, 10, '0', STR_PAD_LEFT) }}">Profile</a>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit">Logout</button>
    </form>
  </div>
</div>
</div>

</div>

      <div id="tab-content">
        <p>Welcome to the Vendor Portal. Select a section from the sidebar to begin.</p>
      </div>
    </div>
  </div>
</body>
</html>


<script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('collapsed');
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

document.addEventListener('DOMContentLoaded', function() {
    // Main tab loader
    function loadTabContent(tab) {
        const tabContent = document.getElementById('tab-content');
        let file = '';
        if (tab === 'home') file = '/home-content';
        else if (tab === 'orders') file = '/orders-content';
        else if (tab === 'deliveries') file = '/deliveries-content';
        else if (tab === 'invoices') file = '/invoices-content';
        else if (tab === 'payments') file = '/payments-content';
        else if (tab === 'reports') file = '/reports-content';
        else { tabContent.innerHTML = ''; return; }

        fetch(file, { credentials: 'include' })
            .then(response => response.text())
            .then(html => {
                tabContent.innerHTML = html;
                if (tab === 'orders') {
                    attachOrderTableHandler();
                    attachOrdersSearchHandler(); 
                }
                if (tab === 'deliveries') {
                // Attach deliveries-specific handlers here
                attachDeliveriesSearchHandler();
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



    //reports content tab fetch
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

    // Auto-click first tab (optional)
    if (tabs.length > 0) {
        tabs[0].click();
    }
}

//report po-ack filter
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


// reports filter delivery
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

//reports filter grn
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


//report filter invoice
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

//report payment filter
function attachPaymentFilterHandler() {
  const filterBtn = document.querySelector('#report-payment-status #filterButtonPayment');
  if (!filterBtn) return;

  filterBtn.addEventListener('click', () => {
    const poNumber = document.querySelector('#report-payment-status #payment_po_number')?.value || '';
    const status = document.querySelector('#report-payment-status #payment_status')?.value || '';

    const params = new URLSearchParams({
      payment_po_number: poNumber,
      payment_status: status
    });

    fetch(`/reports-content?${params.toString()}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newSection = doc.querySelector('#report-payment-status');
      const currentSection = document.querySelector('#report-payment-status');
      if (newSection && currentSection) {
        currentSection.innerHTML = newSection.innerHTML;
        attachPaymentFilterHandler(); // Rebind for new DOM
      }
    });
  });
}


//reports filter backglog
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

//report filter returns
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

    // Event delegation for ALL dynamic delivery tab actions
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

        // Make Delivery
        if (e.target.classList.contains('make-delivery-btn')) {
            e.preventDefault();
            const poId = e.target.dataset.poId;
            if (!confirm('Create delivery for this PO?')) return;
            fetch(`/purchase-orders/${poId}/make-delivery`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                loadTabContent('deliveries');
            });
            return;
        }

        // Vendor: Save supplied quantity and supply date

        // Vendor: Add another (partial) delivery for a PO line item
        if (e.target.classList.contains('add-delivery-btn')) {
        e.preventDefault();
        const poItemId = e.target.dataset.poItemId;
        // Find the parent PO header row to get the orderId
        const poHeaderRow = e.target.closest('tr.po-items-row') 
            ? document.querySelector(`tr.po-header-row[data-order-id]`)
            : e.target.closest('tr').previousElementSibling;
        let orderId = null;
        if (poHeaderRow && poHeaderRow.dataset.orderId) {
            orderId = poHeaderRow.dataset.orderId;
        } else {
            // Fallback: try to get from a data attribute higher up or pass it from the backend
            orderId = e.target.closest('[data-order-id]')?.dataset.orderId;
        }
        fetch(`/purchase-order-items/${poItemId}/add-delivery`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Now fetch the updated delivery items partial and replace the table
                fetch(`/deliveries/${orderId}/items`)
                    .then(response => response.text())
                    .then(html => {
                        // Replace the .po-items-container for this PO
                        const container = document.getElementById('po-items-' + orderId)
                            .querySelector('.po-items-container');
                        if (container) {
                            container.innerHTML = html;
                        }
                        alert(data.message || 'Partial delivery added!');
                    });
            } else {
                alert(data.message || 'Failed to add partial delivery.');
            }
        });
        return;
    }
    });

    // Orders tab handler (unchanged)
    function attachOrderTableHandler() {
        var ordersTable = document.getElementById('orders-table');
        if (!ordersTable) return;
        ordersTable.addEventListener('click', function(e) {
            if (e.target.classList.contains('order-link')) {
                e.preventDefault();
                const orderId = e.target.closest('tr').getAttribute('data-order-id');
                fetch(`/purchase-orders/${orderId}/items`, {
                    credentials: 'include',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('order-items-details').innerHTML = html;
                });
            }
            if (e.target.classList.contains('acknowledge-btn')) {
                e.preventDefault();
                const orderId = e.target.getAttribute('data-id');
                if (!confirm('Acknowledge this PO?')) return;
                fetch(`purchase-orders/${orderId}/acknowledge`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    credentials: 'include'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        loadTabContent('orders');
                    } else {
                        alert(data.message || 'Failed to acknowledge');
                    }
                })
                .catch(() => alert('Failed to acknowledge. Please try again.'));
            }
        });
    }
    // Inside DOMContentLoaded, after other handlers:
 document.body.addEventListener('submit', function(e) {
        if (e.target && e.target.id === 'batch-save-form') {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Replace only the delivery items table
                const container = form.closest('.po-items-container');
                if (container) {
                    container.innerHTML = data.html;
                }
                alert('Batch saved successfully!');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to save batch.');
            });
        }
    });

    function attachOrdersSearchHandler() {
    const searchForm = document.getElementById('orders-search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(searchForm);
            const params = new URLSearchParams(formData).toString();

            fetch(searchForm.action + '?' + params, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('orders-table-container').innerHTML = html;
            })
            .catch(() => {
                alert('Failed to search. Please try again.');
            });
        });
       }
      }

  function attachDeliveriesSearchHandler() {
      const searchForm = document.getElementById('deliveries-search-form');
      if (searchForm) {
          searchForm.addEventListener('submit', function(e) {
              e.preventDefault();
              const formData = new FormData(searchForm);
              const params = new URLSearchParams(formData).toString();

              fetch(searchForm.action + '?' + params, {
                  headers: {
                      'X-Requested-With': 'XMLHttpRequest'
                  }
              })
              .then(response => response.text())
              .then(html => {
                  document.getElementById('deliveries-table-container').innerHTML = html;
              })
              .catch(() => {
                  alert('Failed to search. Please try again.');
              });
          });
      }
  }

    // Tab navigation
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            loadTabContent(this.getAttribute('data-tab'));
        });
    });

    // Stat card shortcuts
    function makeStatCardLoadTab(statCardId, tabName) {
        const link = document.getElementById(statCardId);
        if (link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
                const tab = document.querySelector('.nav-tab[data-tab="' + tabName + '"]');
                if (tab) tab.classList.add('active');
                loadTabContent(tabName);
            });
        }
    }
    makeStatCardLoadTab('orders-stat-link', 'orders');
    makeStatCardLoadTab('deliveries-stat-link', 'deliveries');
    makeStatCardLoadTab('invoices-stat-link', 'invoices');
    makeStatCardLoadTab('payments-stat-link', 'payments');

    // Load Home tab by default
    loadTabContent('home');
});

</script>


