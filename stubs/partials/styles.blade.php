<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #333;
        line-height: 1.6;
    }
    
    .container {
        max-width: 1400px;
        margin: 0 auto;
        background: white;
        min-height: 100vh;
        display: grid;
        grid-template-columns: 280px 1fr;
        grid-template-rows: auto 1fr;
    }
    
    /* Header */
    .header {
        grid-column: 1 / -1;
        background: linear-gradient(45deg, #2c3e50, #3498db);
        color: white;
        padding: 20px;
        text-align: center;
    }
    
    .header h1 {
        font-size: 2.5em;
        margin-bottom: 10px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .app-info {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 15px;
        font-size: 0.9em;
    }
    
    /* Sidebar Navigation */
    .sidebar {
        background: #f8f9fa;
        border-right: 2px solid #e9ecef;
        padding: 20px 0;
        overflow-y: auto;
    }
    
    .nav-section {
        margin-bottom: 25px;
    }
    
    .nav-section h3 {
        color: #495057;
        font-size: 0.9em;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 0 20px 10px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .nav-item {
        display: block;
        padding: 12px 20px;
        text-decoration: none;
        color: #6c757d;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .nav-item:hover, .nav-item.active {
        background: #e9ecef;
        color: #495057;
        border-left-color: #007bff;
    }
    
    .nav-badge {
        background: #6c757d;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8em;
        margin-left: auto;
    }
    
    /* Main Content */
    .content {
        padding: 30px;
        overflow-y: auto;
    }
    
    .page {
        display: none;
    }
    
    .page.active {
        display: block;
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Cards */
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        margin-bottom: 25px;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }
    
    .card-header {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .card-header h2 {
        color: #495057;
        font-size: 1.4em;
    }
    
    .card-body {
        padding: 25px;
    }
    
    /* Entry Points */
    .entry-points {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .entry-point {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }
    
    .entry-point:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    
    .entry-point.route {
        border-left: 4px solid #28a745;
    }
    
    .entry-point.command {
        border-left: 4px solid #ffc107;
    }
    
    .entry-point-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    
    .entry-point-details {
        font-size: 0.9em;
        color: #6c757d;
    }
    
    /* Flow Visualization */
    .flow {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin: 15px 0;
    }
    
    .flow-step {
        display: flex;
        align-items: center;
        margin: 10px 0;
        padding: 8px 12px;
        background: white;
        border-radius: 6px;
        border-left: 3px solid #007bff;
    }
    
    .flow-step.async {
        border-left-color: #fd7e14;
        background: #fff3cd;
    }
    
    .flow-step-icon {
        width: 24px;
        height: 24px;
        margin-right: 12px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #007bff;
        color: white;
        font-size: 0.8em;
    }
    
    .flow-step.async .flow-step-icon {
        background: #fd7e14;
    }
    
    /* Component Details */
    .component-connections {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    
    .connection-group {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
    }
    
    .connection-group h4 {
        color: #495057;
        margin-bottom: 10px;
        font-size: 0.9em;
    }
    
    .connection-item {
        display: block;
        padding: 5px 0;
        color: #6c757d;
        text-decoration: none;
        font-size: 0.85em;
        border-bottom: 1px solid #dee2e6;
    }
    
    .connection-item:hover {
        color: #007bff;
    }
    
    /* Badges and Labels */
    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75em;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge-primary { background: #007bff; color: white; }
    .badge-success { background: #28a745; color: white; }
    .badge-warning { background: #ffc107; color: #212529; }
    .badge-info { background: #17a2b8; color: white; }
    .badge-secondary { background: #6c757d; color: white; }
    
    /* Responsive */
    @media (max-width: 768px) {
        .container {
            grid-template-columns: 1fr;
            grid-template-rows: auto auto 1fr;
        }
        
        .sidebar {
            order: 2;
        }
        
        .content {
            order: 3;
        }
        
        .entry-points {
            grid-template-columns: 1fr;
        }
    }
    
    /* Tables */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    
    .data-table th,
    .data-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
    }
    
    .data-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #495057;
    }
    
    .data-table tbody tr:hover {
        background: #f8f9fa;
    }
</style>
