<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                {{-- <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    WhatsApp Templates
                </h1> --}}
            </div>
        </div>

        <!-- Templates Grid -->
        @if(empty($templates))
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    {{-- <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg> --}}
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                    No templates found
                </h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Please check your WhatsApp Business API configuration.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($templates as $template)
                    <div class="template-card relative bg-white dark:bg-gray-800 border-2 rounded-lg p-4 cursor-pointer transition-all duration-200 hover:shadow-lg hover:-translate-y-1 border-gray-200 dark:border-gray-700 hover:border-primary-300">
                        <!-- Template Header -->
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate pr-2">
                                {{ $template['template_name'] }}
                            </h3>
                            <span class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-full
                                @if($template['status'] === 'APPROVED')
                                    bg-green-100 text-green-900 dark:bg-green-900/30 dark:text-green-400
                                @elseif($template['status'] === 'PENDING')
                                    bg-yellow-100 text-yellow-900 dark:bg-yellow-900/30 dark:text-yellow-300
                                @else
                                    bg-red-100 text-red-900 dark:bg-red-900/30 dark:text-red-400
                                @endif">
                                {{ $template['status'] }}
                            </span>
                        </div>

                        <!-- Template Body -->
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-3">
                            {{ $template['body'] }}
                        </div>

                        <!-- Variables -->
                        @if(!empty($template['variables']))
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($template['variables'] as $variable)
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 text-xs rounded-full">
                                        @php echo '{{' . $variable . '}}'; @endphp
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Template Footer -->
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ strtoupper($template['language']) }}</span>
                            <span class="capitalize">{{ $template['category'] ?? '' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-filament-panels::page>
