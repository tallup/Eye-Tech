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
        .activity-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            background: #f9fafb;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
        }
        .activity-item:last-child {
            margin-bottom: 0;
        }
        .activity-info h4 {
            font-weight: 500;
            color: #111827;
            margin-bottom: 0.25rem;
        }
        .activity-info p {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .activity-value {
            text-align: right;
        }
        .activity-amount {
            font-weight: 700;
            color: #059669;
            margin-bottom: 0.25rem;
        }
        .activity-date {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-completed {
            background: #dcfce7;
            color: #166534;
        }
        .status-in-progress {
            background: #dbeafe;
            color: #1e40af;
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
        .grid-6 {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
        }
        @media (max-width: 1024px) {
            .grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }
            .grid-6 {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 768px) {
            .grid-4, .grid-2, .grid-6 {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Dashboard</h1>
            <p style="color: #fecaca; font-size: 1.125rem; margin-bottom: 1rem;">Welcome to your EyeTech inventory management system</p>
            <div style="text-align: right;">
                <div style="font-size: 3rem; font-weight: 700;">D{{ number_format($data['totalRevenue'], 2) }}</div>
                <div style="color: #fecaca;">Total Revenue</div>
            </div>
        </div>

        <!-- Key Performance Indicators -->
        <div class="grid-4">
            <!-- Total Sales -->
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

            <!-- Total Products -->
            <div class="kpi-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div class="kpi-icon" style="background: #dcfce7;">
                        <svg style="width: 1rem; height: 1rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div style="text-align: right;">
                        <div class="kpi-value">{{ number_format($data['totalProducts']) }}</div>
                        <div class="kpi-label">Total Products</div>
                    </div>
                </div>
            </div>

            <!-- Active Suppliers -->
            <div class="kpi-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div class="kpi-icon" style="background: #f3e8ff;">
                        <svg style="width: 1rem; height: 1rem; color: #9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div style="text-align: right;">
                        <div class="kpi-value">{{ number_format($data['activeSuppliers']) }}</div>
                        <div class="kpi-label">Active Suppliers</div>
                    </div>
                </div>
            </div>

            <!-- Pending Service Requests -->
            <div class="kpi-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div class="kpi-icon" style="background: #fed7aa;">
                        <svg style="width: 1rem; height: 1rem; color: #ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div style="text-align: right;">
                        <div class="kpi-value">{{ number_format($data['pendingRequests']) }}</div>
                        <div class="kpi-label">Pending Requests</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Status -->
        <div class="dashboard-card">
            <div class="section-header">Inventory Status</div>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
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

        <!-- Sales Performance -->
        <div class="grid-2">
            <!-- Today's Sales -->
            <div class="dashboard-card">
                <div class="section-header">Today's Performance</div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div class="stat-item">
                        <div class="stat-value">{{ $data['todaySales'] }}</div>
                        <div class="stat-label">Sales Today</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #16a34a;">D{{ number_format($data['todayRevenue'], 2) }}</div>
                        <div class="stat-label">Revenue Today</div>
                    </div>
                </div>
            </div>

            <!-- This Week's Sales -->
            <div class="dashboard-card">
                <div class="section-header">This Week's Performance</div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div class="stat-item">
                        <div class="stat-value">{{ $data['weekSales'] }}</div>
                        <div class="stat-label">Sales This Week</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #2563eb;">D{{ number_format($data['weekRevenue'], 2) }}</div>
                        <div class="stat-label">Revenue This Week</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid-2">
            <!-- Recent Sales -->
            <div class="dashboard-card">
                <div class="section-header">Recent Sales</div>
                @if($data['recentSales']->count() > 0)
                    <div>
                        @foreach($data['recentSales'] as $sale)
                            <div class="activity-item">
                                <div class="activity-info">
                                    <h4>Sale #{{ $sale->id }}</h4>
                                    <p>{{ $sale->user->name ?? 'Unknown' }}</p>
                                </div>
                                <div class="activity-value">
                                    <div class="activity-amount">D{{ number_format($sale->total_amount, 2) }}</div>
                                    <div class="activity-date">{{ $sale->created_at->format('M j, Y') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <svg style="width: 3rem; height: 3rem; margin: 0 auto 1rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p>No recent sales</p>
                    </div>
                @endif
            </div>

            <!-- Recent Service Requests -->
            <div class="dashboard-card">
                <div class="section-header">Recent Service Requests</div>
                @if($data['recentServiceRequests']->count() > 0)
                    <div>
                        @foreach($data['recentServiceRequests'] as $request)
                            <div class="activity-item">
                                <div class="activity-info">
                                    <h4>{{ $request->customer_name }}</h4>
                                    <p>{{ $request->service->name ?? 'Unknown Service' }}</p>
                                </div>
                                <div class="activity-value">
                                    <span class="status-badge 
                                        @if($request->status == 'pending') status-pending
                                        @elseif($request->status == 'completed') status-completed
                                        @elseif($request->status == 'in_progress') status-in-progress
                                        @else status-neutral
                                        @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                    <div class="activity-date">{{ $request->created_at->format('M j, Y') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <svg style="width: 3rem; height: 3rem; margin: 0 auto 1rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p>No recent service requests</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats Summary -->
        <div class="dashboard-card">
            <div class="section-header">Quick Stats Summary</div>
            <div class="grid-6">
                <div class="stat-item">
                    <div class="stat-value">{{ $data['totalCategories'] }}</div>
                    <div class="stat-label">Categories</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $data['totalUsers'] }}</div>
                    <div class="stat-label">Users</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $data['completedRequests'] }}</div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $data['inProgressRequests'] }}</div>
                    <div class="stat-label">In Progress</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">D{{ number_format($data['averageOrderValue'], 2) }}</div>
                    <div class="stat-label">Avg Order</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $data['totalServiceRequests'] }}</div>
                    <div class="stat-label">Total Requests</div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
