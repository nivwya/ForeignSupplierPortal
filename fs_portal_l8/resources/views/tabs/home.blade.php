
<!-- changes made by niveditha -->
 
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

.overview-header {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  padding: 10px 20px;
  border-bottom: 2px solid #1fae4b;
  font-size: 1rem;
  color: #1fae4b;
  font-weight: 600;
}

.overview-tab, .summary-tab {
  cursor: pointer;
  border-bottom: 2px solid #1fae4b;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 12px;
  max-width: 1200px;
  margin: 0 auto 20px auto;
  padding: 10px;
}

.stat-card {
    background:rgb(34, 203, 85);
  background-image: url('data:image/svg+xml,%3Csvg fill="white" fill-opacity="0.08" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"%3E%3Cpath d="M20.285 2.859a3 3 0 0 0-4.243 0l-2.12 2.121 4.243 4.243 2.12-2.121a3 3 0 0 0 0-4.243zm-3.536 7.071-4.243-4.243L3.515 14.678l-.707 4.95a1 1 0 0 0 1.136 1.135l4.95-.707L16.75 9.93z"/%3E%3C/svg%3E');
  background-repeat: no-repeat;
  background-position: top right;
  background-size: 60px;
  border-radius: 3px;
  padding: 12px;
  color: white;
  text-align: left;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  min-height: 80px;
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

.section-title {
  margin: 16px auto 8px auto;
  max-width: 1200px;
  font-weight: 600;
  font-size: 1rem;
  padding-left: 10px;
  color: #1fae4b;
  border-left: 4px solid #1fae4b;
}

.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  max-width: 1200px;
  margin: 20px auto;
  padding: 0 10px;
}

.chart-container {
  background: white;
  border-radius: 6px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  height: 280px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.chart-header {
    top: -14px; left: 16px;
    background:rgb(34, 203, 85);
  color: white;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.95rem;
  padding: 4px 15px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  
}


.section-header { 
    top: -14px; left: 16px;
     background: #1fae4b; 
     color: white; 
     padding: 4px 15px; 
     border-radius: 20px; 
     font-weight: 600; 
     font-size: 0.9rem; 
    box-shadow: 0 2px 6px rgba(0,0,0,0.1); }

.chart-content {
  padding: 12px;
  flex-grow: 1;
  position: relative;
}

/* Line Chart */
.line-chart svg {
  width: 100%;
  height: 100%;
}

.chart-line {
  stroke: #28a745;
  stroke-width: 3;
  fill: none;
}

.chart-point {
  fill: #28a745;
  r: 4;
  cursor: pointer;
  transition: fill 0.2s;
}

.chart-point:hover {
  fill: #149937;
}

.chart-axis {
  position: absolute;
  bottom: 6px;
  left: 20px;
  right: 20px;
  display: flex;
  justify-content: space-between;
  font-size: 0.7rem;
  color: #444;
  user-select: none;
}

.chart-y-axis {
  position: absolute;
  left: 8px;
  top: 12px;
  bottom: 28px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  font-size: 0.7rem;
  color: #444;
  user-select: none;
}

/* Bar Chart */
.bar-chart {
  display: flex;
  align-items: flex-end;
  justify-content: center;
  height: 100%;
  gap: 8px;
  padding-bottom: 24px;
}

.chart-bar {
  background: #1fae4b;
  width: 30px;
  border-radius: 4px 4px 0 0;
  height: 100px;
  transition: background 0.3s;
}

.chart-bar:hover {
  background: #149937;
}

@media (max-width: 600px) {
  .stat-number {
    font-size: 1.4rem;
  }

  .chart-header {
    font-size: 0.9rem;
  }

  .stat-card {
    padding: 10px;
  }

  .charts-grid {
    grid-template-columns: 1fr;
  }
}
    </style>

<div class="overview-header">
    <div class="overview-tab">Overview</div>
    <div class="summary-tab">Summary</div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <a href="#" id="orders-stat-link" style="text-decoration:none;color:inherit;">
            <div class="stat-number" style="cursor:pointer;">3</div>
        </a>
        <div class="stat-label">Orders</div>
        
    </div>
    <div class="stat-card">
        <a href="#" id="deliveries-stat-link" style="text-decoration:none;color:inherit;">
            <div class="stat-number" style="cursor:pointer;">2</div>
        </a>
        <div class="stat-label">Partial Deliveries</div>
        
    </div>
    <div class="stat-card">
        <a href="#" id="payments-stat-link" style="text-decoration:none;color:inherit;">
            <div class="stat-number highlight">1</div>
        </a>
        <div class="stat-label">Payments Due</div>
        <div class="stat-sublabel"></div>
    </div>
    <div class="stat-card">
        <a href="#" id="invoices-stat-link" style="text-decoration:none;color:inherit;">
            <div class="stat-number" style="cursor:pointer;">2</div>
        </a>
        <div class="stat-label">Invoices</div>
        <div class="stat-sublabel"></div>
    </div>
    <div class="stat-card">
        <div class="stat-number">4</div>
        <div class="stat-label">Quotation submitted</div>
        <div class="stat-sublabel"></div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-container">
        <div class="chart-header">Purchase Orders</div>
        <div class="chart-content">
            <div class="line-chart">
                <svg viewBox="0 0 400 200">
                    <polyline class="chart-line" points="50,180 100,120 150,80 200,160 250,100 350,40"/>
                    <circle class="chart-point" cx="50" cy="180"/>
                    <circle class="chart-point" cx="100" cy="120"/>
                    <circle class="chart-point" cx="150" cy="80"/>
                    <circle class="chart-point" cx="200" cy="160"/>
                    <circle class="chart-point" cx="250" cy="100"/>
                    <circle class="chart-point" cx="350" cy="40"/>
                </svg>
                <div class="chart-axis">
                    <span>Jan</span>
                    <span>Feb</span>
                    <span>Mar</span>
                    <span>Apr</span>
                    <span>Jun</span>
                </div>
                <div class="chart-y-axis">
                    <span>50</span>
                    <span>40</span>
                    <span>30</span>
                    <span>20</span>
                    <span>10</span>
                </div>
            </div>
        </div>
    </div>
    <div class="chart-container">
        <div class="chart-header">Invoice</div>
        <div class="chart-content">
            <div class="bar-chart">
                <div class="chart-bar"></div>
                <div class="chart-axis">
                    <span>Jan</span>
                    <span>Feb</span>
                    <span>Mar</span>
                    <span>Apr</span>
                    <span>Jun</span>
                </div>
                <div class="chart-y-axis">
                    <span>50</span>
                    <span>40</span>
                    <span>30</span>
                    <span>20</span>
                    <span>10</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- changes end-->