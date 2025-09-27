<x-filament-panels::page>
    @php
        $data = $this->getViewData();
    @endphp

    <style>
        .dashboard-container {
            padding: 2rem;
            background: #f8fafc;
            min-height: 100vh;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border-radius: 1rem;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);
        }
        .dashboard-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        .kpi-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            transition: transform 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        .kpi-icon {
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .kpi-value {
            font-size: 1.875rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.25rem;
        }
        .kpi-label {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .section-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            padding: 1rem 1.5rem;
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            border-radius: 1rem 1rem 0 0;
        }
        .stat-item {
            text-align: center;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 0.75rem;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.25rem;
        }
        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .chart-container {
            height: 16rem;
            display: flex;
            align-items: end;
            gap: 0.5rem;
        }
        .chart-bar {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .bar {
            width: 100%;
            background: #dc2626;
            border-radius: 0.25rem 0.25rem 0 0;
            transition: all 0.3s ease;
        }
        .bar:hover {
            background: #b91c1c;
        }
        .chart-label {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }
        .chart-value {
            font-size: 0.75rem;
            font-weight: 600;
            color: #111827;
            margin-top: 0.25rem;
        }
        .progress-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .progress-item:last-child {
            margin-bottom: 0;
        }
        .progress-info {
            display: flex;
            align-items: center;
        }
        .progress-dot {
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            margin-right: 0.75rem;
        }
        .progress-label {
            font-weight: 500;
            color: #111827;
            text-transform: capitalize;
        }
        .progress-bar-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .progress-bar {
            width: 6rem;
            background: #f3f4f6;
            border-radius: 9999px;
            height: 0.5rem;
        }
        .progress-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }
        .progress-count {
            font-size: 0.875rem;
            font-weight: 600;
            color: #111827;
        }
        .list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .list-item:last-child {
            margin-bottom: 0;
        }
        .list-info {
            display: flex;
            align-items: center;
        }
        .list-rank {
            width: 1.5rem;
            height: 1.5rem;
            background: #dc2626;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
        }
        .list-rank-text {
            color: white;
            font-weight: 700;
            font-size: 0.75rem;
        }
        .list-details h4 {
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }
        .list-details p {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .list-value {
            text-align: right;
        }
        .list-amount {
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.25rem;
        }
        .list-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        .grid-2x2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        @media (max-width: 1024px) {
            .grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .grid-4, .grid-2, .grid-2x2 {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Reports & Analytics</h1>
            <p style="color: #fecaca; font-size: 1.125rem; margin-bottom: 1rem;">Comprehensive business insights and performance metrics</p>
            <div style="text-align: right;">
                <div style="font-size: 3rem; font-weight: 700;">D{{ number_format($data['totalRevenue'], 2) }}</div>
                <div style="color: #fecaca;">Total Revenue</div>
            </div>
        </div>

        <!-- Key Performance Indicators -->
        <div class="grid-4">
            <div class="kpi-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div class="kpi-icon" style="background: #dbeafe;">
                        <svg style="width: 1rem; height: 1rem; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div style="text-align: right;">
                        <div class="kpi-value">{{ number_format($data['totalSales']) }}</div>
                        <div class="kpi-label">Total Sales</div>
                    </div>
                </div>
            </div>

            <div class="kpi-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div class="kpi-icon" style="background: #dcfce7;">
                        <svg style="width: 1rem; height: 1rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div style="text-align: right;">
                        <div class="kpi-value">D{{ number_format($data['averageOrderValue'], 2) }}</div>
                        <div class="kpi-label">Avg Order Value</div>
                    </div>
                </div>
            </div>

            <div class="kpi-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div class="kpi-icon" style="background: #f3e8ff;">
                        <svg style="width: 1rem; height: 1rem; color: #9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div style="text-align: right;">
                        <div class="kpi-value">{{ number_format($data['conversionRate'], 1) }}%</div>
                        <div class="kpi-label">Conversion Rate</div>
                    </div>
                </div>
            </div>

            <div class="kpi-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div class="kpi-icon" style="background: #fed7aa;">
                        <svg style="width: 1rem; height: 1rem; color: #ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div style="text-align: right;">
                        <div class="kpi-value">{{ $data['todaySales'] }}</div>
                        <div class="kpi-label">Today's Sales</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid-2">
            <!-- Daily Sales Chart -->
            <div class="dashboard-card">
                <div class="section-header">Daily Sales (Last 7 Days)</div>
                <div class="chart-container">
                        @php
                            $maxSales = collect($data['dailySalesChart'])->max('sales_count') ?: 1;
                        @endphp
                        @foreach($data['dailySalesChart'] as $dayData)
                            @php
                                $height = ($dayData['sales_count'] / $maxSales) * 100;
                            @endphp
                        <div class="chart-bar">
                            <div class="bar" style="height: {{ $height }}%"></div>
                            <div class="chart-label">{{ $dayData['date'] }}</div>
                            <div class="chart-value">{{ $dayData['sales_count'] }}</div>
                            </div>
                        @endforeach
                </div>
            </div>

            <!-- Sales by Status Chart -->
            <div class="dashboard-card">
                <div class="section-header">Sales by Status</div>
                    @if($data['salesByStatus']->count() > 0)
                    <div>
                            @foreach($data['salesByStatus'] as $status)
                                @php
                                    $total = $data['salesByStatus']->sum('count');
                                    $percentage = $total > 0 ? ($status->count / $total) * 100 : 0;
                                @endphp
                            <div class="progress-item">
                                <div class="progress-info">
                                    <div class="progress-dot" style="background: 
                                        @if($status->status === 'completed') #16a34a
                                        @elseif($status->status === 'pending') #ca8a04
                                        @elseif($status->status === 'cancelled') #dc2626
                                        @else #2563eb @endif">
                                    </div>
                                    <span class="progress-label">{{ $status->status }}</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ $percentage }}%; background: 
                                            @if($status->status === 'completed') #16a34a
                                            @elseif($status->status === 'pending') #ca8a04
                                            @elseif($status->status === 'cancelled') #dc2626
                                            @else #2563eb @endif">
                                        </div>
                                    </div>
                                    <span class="progress-count">{{ $status->count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <p>No sales data available</p>
                    </div>
                    @endif
            </div>
        </div>

        <!-- Top Products and Staff Performance -->
        <div class="grid-2">
            <!-- Top Products -->
            <div class="dashboard-card">
                <div class="section-header">Top Products</div>
                    @if($data['topProducts']->count() > 0)
                    <div>
                            @foreach($data['topProducts'] as $index => $product)
                            <div class="list-item">
                                <div class="list-info">
                                    <div class="list-rank">
                                        <span class="list-rank-text">{{ $index + 1 }}</span>
                                    </div>
                                    <div class="list-details">
                                        <h4>{{ Str::limit($product->name, 30) }}</h4>
                                        <p>{{ $product->sku }}</p>
                                    </div>
                                </div>
                                <div class="list-value">
                                    <div class="list-amount">{{ $product->total_quantity }} sold</div>
                                    <div class="list-subtitle">D{{ number_format($product->total_revenue, 2) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <p>No sales data available</p>
                    </div>
                    @endif
            </div>

            <!-- Top Staff Performance -->
            <div class="dashboard-card">
                <div class="section-header">Top Staff Performance</div>
                    @if($data['topStaff']->count() > 0)
                    <div>
                            @foreach($data['topStaff'] as $index => $staff)
                            <div class="list-item">
                                <div class="list-info">
                                    <div class="list-rank">
                                        <span class="list-rank-text">{{ $index + 1 }}</span>
                                    </div>
                                    <div class="list-details">
                                        <h4>{{ $staff->name }}</h4>
                                        <p>{{ $staff->sales_count }} sales</p>
                                    </div>
                                </div>
                                <div class="list-value">
                                    <div class="list-amount">D{{ number_format($staff->total_revenue, 2) }}</div>
                                    <div class="list-subtitle">Revenue</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <p>No staff data available</p>
                    </div>
                    @endif
            </div>
        </div>

        <!-- Service Requests and Inventory Status -->
        <div class="grid-2">
            <!-- Service Requests -->
            <div class="dashboard-card">
                <div class="section-header">Service Requests</div>
                <div class="grid-2x2">
                    <div class="stat-item">
                        <div class="stat-value">{{ $data['totalServiceRequests'] }}</div>
                        <div class="stat-label">Total</div>
                </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #ca8a04;">{{ $data['pendingRequests'] }}</div>
                        <div class="stat-label">Pending</div>
                        </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #2563eb;">{{ $data['inProgressRequests'] }}</div>
                        <div class="stat-label">In Progress</div>
                        </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #16a34a;">{{ $data['completedRequests'] }}</div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>
            </div>

            <!-- Inventory Status -->
            <div class="dashboard-card">
                <div class="section-header">Inventory Status</div>
                <div class="grid-2x2">
                    <div class="stat-item">
                        <div class="stat-value">{{ $data['totalProducts'] }}</div>
                        <div class="stat-label">Total Products</div>
                </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #16a34a;">{{ $data['activeProducts'] }}</div>
                        <div class="stat-label">Active</div>
                        </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #ca8a04;">{{ $data['lowStockProducts'] }}</div>
                        <div class="stat-label">Low Stock</div>
                        </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #dc2626;">{{ $data['outOfStockProducts'] }}</div>
                        <div class="stat-label">Out of Stock</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
