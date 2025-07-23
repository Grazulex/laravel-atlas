<div class="bg-white rounded-xl shadow-md p-6">
    <h2 class="text-2xl font-bold text-indigo-800 mb-6">📊 Application Summary</h2>

    {{-- Overview Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @if (!empty($summary['total_models']))
            <div class="bg-blue-50 rounded-lg p-4 text-center border border-blue-100">
                <div class="text-2xl font-bold text-blue-600">{{ $summary['total_models'] }}</div>
                <div class="text-sm text-blue-800">Models</div>
            </div>
        @endif

        @if (!empty($summary['total_routes']))
            <div class="bg-green-50 rounded-lg p-4 text-center border border-green-100">
                <div class="text-2xl font-bold text-green-600">{{ $summary['total_routes'] }}</div>
                <div class="text-sm text-green-800">Routes</div>
            </div>
        @endif

        @if (!empty($summary['total_controllers']))
            <div class="bg-yellow-50 rounded-lg p-4 text-center border border-yellow-100">
                <div class="text-2xl font-bold text-yellow-600">{{ $summary['total_controllers'] }}</div>
                <div class="text-sm text-yellow-800">Controllers</div>
            </div>
        @endif

        @if (!empty($summary['total_services']))
            <div class="bg-purple-50 rounded-lg p-4 text-center border border-purple-100">
                <div class="text-2xl font-bold text-purple-600">{{ $summary['total_services'] }}</div>
                <div class="text-sm text-purple-800">Services</div>
            </div>
        @endif

        @if (!empty($summary['total_commands']))
            <div class="bg-indigo-50 rounded-lg p-4 text-center border border-indigo-100">
                <div class="text-2xl font-bold text-indigo-600">{{ $summary['total_commands'] }}</div>
                <div class="text-sm text-indigo-800">Commands</div>
            </div>
        @endif

        @if (!empty($summary['total_jobs']))
            <div class="bg-pink-50 rounded-lg p-4 text-center border border-pink-100">
                <div class="text-2xl font-bold text-pink-600">{{ $summary['total_jobs'] }}</div>
                <div class="text-sm text-pink-800">Jobs</div>
            </div>
        @endif

        @if (!empty($summary['total_events']))
            <div class="bg-teal-50 rounded-lg p-4 text-center border border-teal-100">
                <div class="text-2xl font-bold text-teal-600">{{ $summary['total_events'] }}</div>
                <div class="text-sm text-teal-800">Events</div>
            </div>
        @endif

        @if (!empty($summary['total_middleware']))
            <div class="bg-orange-50 rounded-lg p-4 text-center border border-orange-100">
                <div class="text-2xl font-bold text-orange-600">{{ $summary['total_middleware'] }}</div>
                <div class="text-sm text-orange-800">Middleware</div>
            </div>
        @endif
    </div>

    {{-- Architecture Insights --}}
    @if (!empty($summary['insights']))
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🎯 Architecture Insights</h3>
            <div class="grid md:grid-cols-2 gap-6">
                @if (!empty($summary['insights']['complexity']))
                    <div class="bg-gray-50 rounded-lg p-4 border">
                        <h4 class="font-medium text-gray-700 mb-2">Complexity Analysis</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Average Methods per Controller:</span>
                                <span class="font-medium">{{ $summary['insights']['complexity']['avg_methods_per_controller'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Average Relations per Model:</span>
                                <span class="font-medium">{{ $summary['insights']['complexity']['avg_relations_per_model'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Route/Controller Ratio:</span>
                                <span class="font-medium">{{ $summary['insights']['complexity']['route_controller_ratio'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if (!empty($summary['insights']['patterns']))
                    <div class="bg-gray-50 rounded-lg p-4 border">
                        <h4 class="font-medium text-gray-700 mb-2">Design Patterns</h4>
                        <div class="space-y-2">
                            @foreach ($summary['insights']['patterns'] as $pattern => $count)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $pattern)) }}:</span>
                                    <span class="font-medium">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Security Overview --}}
    @if (!empty($summary['security']))
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🔒 Security Overview</h3>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $summary['security']['protected_routes'] ?? 0 }}</div>
                        <div class="text-sm text-blue-800">Protected Routes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $summary['security']['public_routes'] ?? 0 }}</div>
                        <div class="text-sm text-blue-800">Public Routes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $summary['security']['middleware_usage'] ?? 0 }}%</div>
                        <div class="text-sm text-blue-800">Middleware Coverage</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Performance Metrics --}}
    @if (!empty($summary['performance']))
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">⚡ Performance Metrics</h3>
            <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-green-700 mb-2">Analysis Performance</div>
                        <div class="space-y-1">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Scan Duration:</span>
                                <span class="font-medium">{{ $summary['performance']['scan_duration'] ?? 'N/A' }}s</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Memory Usage:</span>
                                <span class="font-medium">{{ $summary['performance']['memory_usage'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Files Analyzed:</span>
                                <span class="font-medium">{{ $summary['performance']['files_analyzed'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-green-700 mb-2">Application Metrics</div>
                        <div class="space-y-1">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Lines of Code:</span>
                                <span class="font-medium">{{ number_format($summary['performance']['lines_of_code'] ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Classes:</span>
                                <span class="font-medium">{{ $summary['performance']['total_classes'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Methods:</span>
                                <span class="font-medium">{{ $summary['performance']['total_methods'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Generation Info --}}
    <div class="border-t pt-6">
        <div class="flex justify-between items-center text-sm text-gray-600">
            <div>
                Generated by <strong>Laravel Atlas</strong>
                @if (!empty($summary['version']))
                    v{{ $summary['version'] }}
                @endif
            </div>
            <div>
                @if (!empty($summary['generated_at']))
                    {{ $summary['generated_at'] }}
                @else
                    {{ now()->format('Y-m-d H:i:s') }}
                @endif
            </div>
        </div>
        @if (!empty($summary['application_info']))
            <div class="mt-2 text-xs text-gray-500">
                {{ $summary['application_info']['name'] ?? 'Laravel Application' }} 
                @if (!empty($summary['application_info']['version']))
                    v{{ $summary['application_info']['version'] }}
                @endif
                ({{ $summary['application_info']['environment'] ?? 'production' }})
            </div>
        @endif
    </div>
</div>