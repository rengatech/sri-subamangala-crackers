{{-- resources/views/filament/resources/order-status-summary.blade.php --}}
<x-filament::page>
    <div class="space-y-6">
        {{-- Modern Header Section --}}
        <div class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-pink-500/10"></div>
            <div class="relative p-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2a2 2 0 00-2-2V7a2 2 0 00-2-2H9a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Order Dashboard</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Comprehensive order analytics and insights</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-full">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Live</span>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ now()->format('M j, Y • H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $selectedYear = request()->get('year');
            $selectedStatus = request()->get('status');
            $years = \App\Models\Order::selectRaw('YEAR(updated_at) as year, COUNT(*) as count')
                ->groupBy('year')
                ->orderByDesc('year')
                ->get();
        @endphp

        @if($selectedYear && $selectedStatus)
            {{-- Status Detail View --}}
            <div class="space-y-6">
                {{-- Breadcrumb Navigation --}}
                <div class="flex items-center justify-between">
                    <nav class="flex items-center space-x-2 text-sm">
                        <a href="{{ request()->url() }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Dashboard</a>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">{{ $selectedYear }}</a>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="text-gray-900 dark:text-white font-medium">{{ ucfirst($selectedStatus) }}</span>
                    </nav>
                    <div class="flex gap-2">
                        <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back
                        </a>
                    </div>
                </div>

                @php
                    $orders = \App\Models\Order::where('status', $selectedStatus)
                        ->whereYear('updated_at', $selectedYear)
                        ->orderByDesc('updated_at')
                        ->paginate(20);
                        
                    $statusInfo = [
                        'placed' => ['label' => 'Confirmed Orders', 'icon' => 'check-circle', 'color' => 'emerald'],
                        'packing' => ['label' => 'Orders Being Packed', 'icon' => 'package', 'color' => 'amber'],
                        'cancelled' => ['label' => 'Cancelled Orders', 'icon' => 'x-circle', 'color' => 'red'],
                        'dispatched' => ['label' => 'Dispatched Orders', 'icon' => 'truck', 'color' => 'blue'],
                    ];
                    
                    $currentStatusInfo = $statusInfo[$selectedStatus] ?? ['label' => 'Orders', 'icon' => 'clipboard-list', 'color' => 'gray'];
                @endphp

                {{-- Status Header Card --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-{{ $currentStatusInfo['color'] }}-100 dark:bg-{{ $currentStatusInfo['color'] }}-900 rounded-xl flex items-center justify-center">
                                @if($currentStatusInfo['icon'] == 'check-circle')
                                    <svg class="w-6 h-6 text-{{ $currentStatusInfo['color'] }}-600 dark:text-{{ $currentStatusInfo['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($currentStatusInfo['icon'] == 'package')
                                    <svg class="w-6 h-6 text-{{ $currentStatusInfo['color'] }}-600 dark:text-{{ $currentStatusInfo['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                @elseif($currentStatusInfo['icon'] == 'x-circle')
                                    <svg class="w-6 h-6 text-{{ $currentStatusInfo['color'] }}-600 dark:text-{{ $currentStatusInfo['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($currentStatusInfo['icon'] == 'truck')
                                    <svg class="w-6 h-6 text-{{ $currentStatusInfo['color'] }}-600 dark:text-{{ $currentStatusInfo['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $currentStatusInfo['label'] }}</h2>
                                <p class="text-gray-600 dark:text-gray-400">{{ $selectedYear }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($orders->total()) }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Orders</div>
                        </div>
                    </div>
                </div>

                {{-- Orders Table --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Order</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($orders as $order)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                                                    {{ substr($order->id, -2) }}
                                                </div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    #{{ $order->id }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $order->customer_name ?? $order->name ?? 'Guest Customer' }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $order->customer_email ?? $order->email ?? 'No email' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                ₹{{ number_format($order->total_amount, 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $order->created_at->format('M j, Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $order->created_at->format('H:i A') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'placed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
                                                    'packing' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
                                                    'dispatched' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @php
                                                $routeName = null;
                                                $possibleRoutes = [
                                                    'filament.admin.resources.orders.view',
                                                    'filament.admin.resources.order.view', 
                                                    'filament.resources.orders.view',
                                                    'filament.resources.order.view',
                                                    'filament.admin.resources.orders.edit',
                                                    'filament.admin.resources.order.edit'
                                                ];
                                                
                                                foreach($possibleRoutes as $route) {
                                                    if(Route::has($route)) {
                                                        $routeName = $route;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            
                                            @if($routeName)
                                                <a href="{{ route($routeName, $order) }}" 
                                                   class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded-lg hover:bg-indigo-200 dark:hover:bg-indigo-800 transition-colors text-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    View
                                                </a>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-16 text-center">
                                            <div class="text-gray-500 dark:text-gray-400">
                                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full mx-auto mb-4 flex items-center justify-center">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-lg font-medium mb-2">No orders found</p>
                                                <p class="text-sm">No {{ $selectedStatus }} orders for {{ $selectedYear }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($orders->hasPages())
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                            {{ $orders->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

        @elseif($selectedYear)
            {{-- Year Detail View --}}
            <div class="space-y-6">
                {{-- Breadcrumb --}}
                <div class="flex items-center justify-between">
                    <nav class="flex items-center space-x-2 text-sm">
                        <a href="{{ request()->url() }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Dashboard</a>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $selectedYear }}</span>
                    </nav>
                    <a href="{{ request()->url() }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back
                    </a>
                </div>

                @php
                    $statusCounts = \App\Models\Order::selectRaw('status, COUNT(*) as count')
                        ->whereYear('updated_at', $selectedYear)
                        ->groupBy('status')
                        ->get()
                        ->pluck('count', 'status')
                        ->toArray();
                    
                    $totalOrders = \App\Models\Order::whereYear('updated_at', $selectedYear)->count();
                    
                    $statusConfig = [
                        'placed' => ['label' => 'Confirmed', 'icon' => 'check-circle', 'color' => 'emerald'],
                        'packing' => ['label' => 'Packing', 'icon' => 'package', 'color' => 'amber'],
                        'cancelled' => ['label' => 'Cancelled', 'icon' => 'x-circle', 'color' => 'red'],
                        'dispatched' => ['label' => 'Dispatched', 'icon' => 'truck', 'color' => 'blue'],
                    ];
                @endphp

                {{-- Status Cards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($statusConfig as $status => $config)
                        @php
                            $count = $statusCounts[$status] ?? 0;
                            $percentage = $totalOrders > 0 ? round(($count / $totalOrders) * 100, 1) : 0;
                        @endphp
                        <a href="{{ $count > 0 ? request()->fullUrlWithQuery(['status' => $status]) : '#' }}" 
                           class="group block {{ $count > 0 ? 'cursor-pointer' : 'cursor-not-allowed' }}">
                            <div class="relative bg-white dark:bg-gray-900 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 {{ $count > 0 ? 'hover:shadow-xl hover:-translate-y-1' : 'opacity-60' }} transition-all duration-300">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-{{ $config['color'] }}-100 dark:bg-{{ $config['color'] }}-900 rounded-xl flex items-center justify-center">
                                        @if($config['icon'] == 'check-circle')
                                            <svg class="w-6 h-6 text-{{ $config['color'] }}-600 dark:text-{{ $config['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @elseif($config['icon'] == 'package')
                                            <svg class="w-6 h-6 text-{{ $config['color'] }}-600 dark:text-{{ $config['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        @elseif($config['icon'] == 'x-circle')
                                            <svg class="w-6 h-6 text-{{ $config['color'] }}-600 dark:text-{{ $config['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @elseif($config['icon'] == 'truck')
                                            <svg class="w-6 h-6 text-{{ $config['color'] }}-600 dark:text-{{ $config['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    @if($count > 0)
                                        <div class="text-gray-400 group-hover:text-{{ $config['color'] }}-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $config['label'] }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $selectedYear }}</p>
                                    </div>
                                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($count) }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-{{ $config['color'] }}-500 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $percentage }}%</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Year Overview --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-xl">
                        📊
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Years Overview</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($years as $yearData)
                        <a href="{{ request()->fullUrlWithQuery(['year' => $yearData->year]) }}" 
                           class="group relative block">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur"></div>
                            <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg group-hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 group-hover:border-transparent">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="text-4xl">📅</div>
                                    <div class="text-gray-400 group-hover:text-blue-500 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="text-2xl font-black text-gray-900 dark:text-white">
                                        {{ $yearData->year }}
                                    </div>
                                    <div class="text-3xl font-black text-blue-600 dark:text-blue-400">
                                        {{ number_format($yearData->count) }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        total orders
                                    </div>
                                </div>
                                <div class="mt-4 h-1 bg-gray-200 dark:bg-gray-700 rounded-full">
                                    <div class="h-1 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full group-hover:from-purple-500 group-hover:to-pink-500 transition-all duration-300" 
                                         style="width: {{ min(($yearData->count / $years->max('count')) * 100, 100) }}%"></div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-filament::page>