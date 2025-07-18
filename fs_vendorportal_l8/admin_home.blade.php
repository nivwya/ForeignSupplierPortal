<!-- Admin Home Tab -->
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
  }

  body {
    background: #f9fdf9;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
    max-width: 1200px;
    margin: 0 auto 30px auto;
    padding: 10px;
  }

  .stat-card {
    background: rgb(34, 203, 85);
    background-image: url('data:image/svg+xml,%3Csvg fill="white" fill-opacity="0.08" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"%3E%3Cpath d="M20.285 2.859a3 3 0 0 0-4.243 0l-2.12 2.121 4.243 4.243 2.12-2.121a3 3 0 0 0 0-4.243zm-3.536 7.071-4.243-4.243L3.515 14.678l-.707 4.95a1 1 0 0 0 1.136 1.135l4.95-.707L16.75 9.93z"/%3E%3C/svg%3E');
    background-repeat: no-repeat;
    background-position: top right;
    background-size: 60px;
    border-radius: 3px;
    padding: 14px;
    color: white;
    text-align: left;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    justify-content: center;
    font-size: 0.9rem;
  }

  .stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    line-height: 1.2;
  }

  .stat-label {
    font-size: 0.85rem;
    font-weight: 400;
    margin-top: 4px;
  }

  .stat-sublabel {
    font-size: 0.75rem;
    color: #e0e0e0;
  }

  @media (max-width: 600px) {
    .stat-number {
      font-size: 1.4rem;
    }

    .stat-card {
      padding: 10px;
    }
  }
</style>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-number" id="stat-not-released">—</div>
    <div class="stat-label">POs Not Released</div>
  </div>

  <div class="stat-card">
    <div class="stat-number" id="stat-not-acknowledged">—</div>
    <div class="stat-label">POs Not Acknowledged</div>
  </div>

  <div class="stat-card">
    <div class="stat-number" id="stat-not-delivered">—</div>
    <div class="stat-label">Not Delivered (in Full)</div>
  </div>

  <div class="stat-card">
    <div class="stat-number" id="stat-partial-delivery">—</div>
    <div class="stat-label">Partial Deliveries</div>
  </div>
</div>

<script>
  function loadStatCards() {
    fetch('/admin/purchase-orders/all-not-released')
      .then(res => res.json())
      .then(data => {
        document.getElementById('stat-not-released').textContent = data.length;
      });

    fetch('/admin/purchase-orders/all-not-acknowledged')
      .then(res => res.json())
      .then(data => {
        document.getElementById('stat-not-acknowledged').textContent = data.length;
      });

    fetch('/admin/purchase-orders/all-not-delivered')
      .then(res => res.json())
      .then(data => {
        document.getElementById('stat-not-delivered').textContent = data.length;
      });

    fetch('/admin/purchase-orders/all-partial-delivery')
      .then(res => res.json())
      .then(data => {
        document.getElementById('stat-partial-delivery').textContent = data.length;
      });
  }

  document.addEventListener('DOMContentLoaded', loadStatCards);
</script>
