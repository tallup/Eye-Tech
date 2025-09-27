<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Analytics Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        }
        .card-shadow {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .metric-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
            transition: transform 0.2s ease-in-out;
        }
        .metric-card:hover {
            transform: translateY(-2px);
        }
        .metric-card.blue { border-left-color: #3b82f6; }
        .metric-card.green { border-left-color: #10b981; }
        .metric-card.purple { border-left-color: #8b5cf6; }
        .metric-card.orange { border-left-color: #f59e0b; }
        .chart-bar {
            background: linear-gradient(to top, #3b82f6, #60a5fa);
            border-radius: 4px 4px 0 0;
            transition: all 0.3s ease;
        }
        .chart-bar:hover {
            background: linear-gradient(to top, #1d4ed8, #3b82f6);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto p-6 space-y-8">
        <!-- Header -->
        <div class="gradient-bg rounded-2xl p-8 text-white card-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Custom Analytics Dashboard</h1>
                    <p class="text-red-100 text-lg">Real-time business insights and performance metrics</p>
                </div>
                <div class="text-right">
                    <div class="text-6xl font-bold">D{{ number_format($data['totalRevenue'], 2) }}</div>
                    <div class="text-red-100 text-xl">Total Revenue</div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="bg-white rounded-2xl p-6 card-shadow">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Key Performance Indicators</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="metric-card blue">
                    <div class="text-3xl font-bold text-blue-600">{{ number_format($data['totalSales']) }}</div>
                    <div class="text-blue-600 font-medium">Total Sales</div>
                </div>
                <div class="metric-card green">
                    <div class="text-3xl font-bold text-green-600">{{ $data['todaySales'] }}</div>
                    <div class="text-green-600 font-medium">Today's Sales</div>
                </div>
                <div class="metric-card purple">
                    <div class="text-3xl font-bold text-purple-600">D{{ number_format($data['averageOrderValue'], 2) }}</div>
                    <div class="text-purple-600 font-medium">Avg Order Value</div>
                </div>
                <div class="metric-card orange">
                    <div class="text-3xl font-bold text-orange-600">{{ number_format($data['conversionRate'], 1) }}%</div>
                    <div class="text-orange-600 font-medium">Conversion Rate</div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="bg-white rounded-2xl p-6 card-shadow">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Analytics & Charts</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Sales Chart -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Sales Trend (Last 14 Days)</h3>
                    <div class="h-48 flex items-end space-x-2">
                        @php
                            $maxSales = collect($data['dailySalesChart'])->max('sales_count') ?: 1;
                        @endphp
                        @foreach(array_slice($data['dailySalesChart'], -14) as $dayData)
                            @php
                                $height = ($dayData['sales_count'] / $maxSales) * 100;
                            @endphp
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full chart-bar" style="height: {{ $height }}%"></div>
                                <div class="text-xs text-gray-500 mt-2">{{ $dayData['date'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Status Chart -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Sales by Status</h3>
                    @if($data['salesByStatus']->count() > 0)
                        <div class="space-y-4">
                            @foreach($data['salesByStatus'] as $status)
                                @php
                                    $total = $data['salesByStatus']->sum('count');
                                    $percentage = $total > 0 ? ($status->count / $total) * 100 : 0;
                                @endphp
                                <div class="flex items-center justify-between">
                                    <span class="capitalize text-gray-900 font-medium">{{ $status->status }}</span>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-32 bg-gray-200 rounded-full h-3">
                                            <div class="h-3 rounded-full bg-blue-500" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900 w-8 text-right">{{ $status->count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No sales data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Products and Staff -->
        <div class="bg-white rounded-2xl p-6 card-shadow">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Performance Tracking</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Top Products -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Top Products</h3>
                    @if($data['topProducts']->count() > 0)
                        <div class="space-y-3">
                            @foreach($data['topProducts']->take(5) as $index => $product)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">{{ $index + 1 }}</span>
                                        <span class="font-medium text-gray-900">{{ Str::limit($product->name, 25) }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-900">{{ $product->total_quantity }}</div>
                                        <div class="text-xs text-gray-500">D{{ number_format($product->total_revenue, 0) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No sales data available</p>
                    @endif
                </div>

                <!-- Top Staff -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Top Staff</h3>
                    @if($data['topStaff']->count() > 0)
                        <div class="space-y-3">
                            @foreach($data['topStaff']->take(5) as $index => $staff)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">{{ $index + 1 }}</span>
                                        <span class="font-medium text-gray-900">{{ $staff->name }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-900">D{{ number_format($staff->total_revenue, 0) }}</div>
                                        <div class="text-xs text-gray-500">{{ $staff->sales_count }} sales</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No staff data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Service Requests and Inventory -->
        <div class="bg-white rounded-2xl p-6 card-shadow">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Business Overview</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Service Requests -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Service Requests</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-3xl font-bold text-blue-600">{{ $data['totalServiceRequests'] }}</div>
                            <div class="text-sm text-blue-600">Total</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-3xl font-bold text-yellow-600">{{ $data['pendingRequests'] }}</div>
                            <div class="text-sm text-yellow-600">Pending</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-3xl font-bold text-purple-600">{{ $data['inProgressRequests'] }}</div>
                            <div class="text-sm text-purple-600">In Progress</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-3xl font-bold text-green-600">{{ $data['completedRequests'] }}</div>
                            <div class="text-sm text-green-600">Completed</div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Status -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Inventory Status</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-3xl font-bold text-gray-600">{{ $data['totalProducts'] }}</div>
                            <div class="text-sm text-gray-600">Total Products</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-3xl font-bold text-green-600">{{ $data['activeProducts'] }}</div>
                            <div class="text-sm text-green-600">Active</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-3xl font-bold text-yellow-600">{{ $data['lowStockProducts'] }}</div>
                            <div class="text-sm text-yellow-600">Low Stock</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <div class="text-3xl font-bold text-red-600">{{ $data['outOfStockProducts'] }}</div>
                            <div class="text-sm text-red-600">Out of Stock</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


