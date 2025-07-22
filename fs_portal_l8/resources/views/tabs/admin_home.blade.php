<!-- Admin Home Tab -->
 <!-- CHANGES MADE BY NIVEDITHA -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    * {
        font-family: 'Inter', sans-serif;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        background: transparent;
        max-width: 1200px;
        margin: 20px auto;
        padding: 0 10px;
    }

    .stat-card {
        background: #1fae4b;
        color: white;
        padding: 20px 16px;
        border-radius: 6px;
        text-align: left;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 100px;
    }

    .stat-card::after {
        content: '';
        background-image: url("data:image/svg+xml,%3Csvg fill='white' fill-opacity='0.08' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M20.285 2.859a3 3 0 0 0-4.243 0l-2.12 2.121 4.243 4.243 2.12-2.121a3 3 0 0 0 0-4.243zm-3.536 7.071-4.243-4.243L3.515 14.678l-.707 4.95a1 1 0 0 0 1.136 1.135l4.95-.707L16.75 9.93z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: top right;
        background-size: 60px;
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .stat-number.highlight {
        color: #fff;
    }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 500;
        opacity: 0.9;
    }

    .stat-sublabel {
        font-size: 0.75rem;
        color: #e0e0e0;
    }

    #admin-action-result {
        font-size: 0.9rem;
        color: #1fae4b;
    }

    form input, form button {
        font-size: 0.9rem;
    }

    form button {
        transition: background 0.3s ease;
    }

    form button:hover {
        opacity: 0.95;
    }

    @media (max-width: 768px) {
        .stat-card {
            padding: 14px 12px;
        }
        .stat-number {
            font-size: 1.5rem;
        }
        .stat-label {
            font-size: 0.8rem;
        }
    }
    
    .admin-company-codes-box {
        margin: 32px 0;
        padding: 18px 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px #e3e6f0;
        max-width: 520px;
        margin-left: auto;
        margin-right: auto;
        border: 1px solid #e3e6f0;
    }
    .admin-company-codes-title {
        font-size: 1.15rem;
        font-weight: 600;
        color: #222;
        margin-bottom: 14px;
        letter-spacing: 0.01em;
    }
    .admin-company-codes-form {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 10px;
        margin-bottom: 0;
    }
    .admin-company-codes-form + .admin-company-codes-form {
        margin-top: 18px;
    }
    .admin-company-codes-input {
        padding: 8px 10px;
        border-radius: 4px;
        border: 1px solid #b3c1c7;
        font-size: 0.98rem;
        min-width: 120px;
        max-width: 220px;
        background: #f7f9fa;
        transition: border 0.2s;
        height: 38px;
        flex: 1 1 160px;
        box-sizing: border-box;
    }
    .admin-company-codes-input:focus {
        border-color: #318c38;
        outline: none;
        background: #fff;
    }
    .admin-company-codes-btn {
        flex: 0 0 auto;
        max-width: 100px;
        background: #318c38;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 4px 12px;
        font-size: 0.88rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s, box-shadow 0.2s;
        box-shadow: 0 1px 2px rgba(49,140,56,0.04);
        height: 32px;
        display: flex;
        align-items: center;
        white-space: nowrap;
    }
    .admin-company-codes-btn.assign {
        background: #318c38;
    }
    .admin-company-codes-btn.revoke {
        background: #e74c3c;
    }
    .admin-company-codes-btn:hover {
        opacity: 0.93;
        box-shadow: 0 2px 6px rgba(49,140,56,0.08);
    }
    .admin-company-codes-result {
        margin-top: 10px;
        font-weight: 500;
        font-size: 0.95rem;
        color: #1fae4b;
    }
    @media (max-width: 700px) {
        .admin-company-codes-form {
            flex-wrap: wrap;
        }
        .admin-company-codes-input,
        .admin-company-codes-btn {
            width: 100%;
            min-width: 0;
            max-width: 100%;
        }
    }
</style>
<!-- changes end -->

<div class="stats-grid">
     <div class="stat-card">
        <div class="stat-label">POs Not Released</div>
        <div class="stat-number" id="stat-not-released">—</div>
    </div>
     <div class="stat-card">
        <div class="stat-label">POs Not Acknowledged</div>
        <div class="stat-number" id="stat-not-acknowledged">—</div>
    </div>
     <div class="stat-card">
        <div class="stat-label">Not Delivered (in Full)</div>
        <div class="stat-number" id="stat-not-delivered">—</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Partial Deliveries</div>
        <div class="stat-number" id="stat-partial-delivery">—</div>
    </div>
</div>

<!-- changes made by niveditha -->
@if(auth()->user() && auth()->user()->isSuperAdmin())
<div class="admin-company-codes-box">
    <h2 class="admin-company-codes-title">Assign/Revoke Admin Company Codes</h2>
    <form id="assign-admin-form" class="admin-company-codes-form">
        <input type="email" name="email" placeholder="Admin Email" required class="admin-company-codes-input">
        <input type="text" name="company_code" placeholder="Company Code" required class="admin-company-codes-input">
        <button type="submit" class="admin-company-codes-btn assign">Assign</button>
    </form>
    <form id="remove-admin-form" class="admin-company-codes-form">
        <input type="email" name="email" placeholder="Admin Email" required class="admin-company-codes-input">
        <input type="text" name="company_code" placeholder="Company Code" required class="admin-company-codes-input">
        <button type="submit" class="admin-company-codes-btn revoke">Revoke</button>
    </form>
    <div id="admin-action-result" class="admin-company-codes-result"></div>
</div>
@endif
<!-- changes end -->

<script>
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
document.addEventListener('DOMContentLoaded', function() {
    // Assign Admin
    const assignForm = document.getElementById('assign-admin-form');
    if (assignForm) {
        assignForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(assignForm);
            fetch('/admin/assign-admin', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
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

    // Remove Admin
    const removeForm = document.getElementById('remove-admin-form');
    if (removeForm) {
        removeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(removeForm);
            fetch('/admin/remove-admin', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
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
    loadStatCards();
});
</script>