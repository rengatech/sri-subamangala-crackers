@extends('layouts.admin')

@section('title', 'WhatsApp Templates')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">WhatsApp Templates</h2>

        @if(empty($templates))
            <div class="text-center py-8">
                <div class="text-gray-500 text-lg">No WhatsApp templates found.</div>
                <p class="text-gray-400 mt-2">Please check your API configuration.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($templates as $template)
                    <div class="template-card bg-white border-2 border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:shadow-lg transition-all duration-200 cursor-pointer"
                         data-template-id="{{ $template['id'] }}"
                         onclick="selectTemplate('{{ $template['id'] }}')">

                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-800 truncate">{{ $template['template_name'] }}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($template['status'] === 'APPROVED') bg-green-100 text-green-800
                                @elseif($template['status'] === 'PENDING') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $template['status'] }}
                            </span>
                        </div>

                        <div class="text-sm text-gray-600 mb-3 line-clamp-3">
                            {{ $template['body'] }}
                        </div>

                        @if(!empty($template['variables']))
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($template['variables'] as $variable)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                        {{ '{{' . $variable . '}}' }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="text-xs text-gray-500">
                            Language: {{ strtoupper($template['language']) }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
function selectTemplate(templateId) {
    // Remove previous selection
    document.querySelectorAll('.template-card').forEach(card => {
        card.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-300');
        card.classList.add('border-gray-200');
    });

    // Add selection to clicked card
    const selectedCard = document.querySelector(`[data-template-id="${templateId}"]`);
    if (selectedCard) {
        selectedCard.classList.remove('border-gray-200');
        selectedCard.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-300');
    }

    // You can add AJAX call here to handle template selection
    console.log('Selected template:', templateId);

    // Optional: Show success message
    showNotification('Template selected: ' + templateId);
}

function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    notification.textContent = message;

    document.body.appendChild(notification);

    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.template-card:hover {
    transform: translateY(-2px);
}
</style>
@endsection
