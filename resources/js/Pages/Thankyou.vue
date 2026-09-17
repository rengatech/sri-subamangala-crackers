<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    order: Object,
    mobile_number_1: String,
    company_address: String,
    global_discount: Number,
    download_link: String,
})

const whatsappText = computed(() => {
    return `Hello! I have placed an order.\n\n` + 
           `*Customer Details*\n` + 
           `Name: ${props.order?.customer?.name || 'N/A'}\n` +
           `Phone: ${props.order?.customer?.whatsapp_number || 'N/A'}\n` +
           `City: ${props.order?.address?.city_town || 'N/A'}\n` +
           `Address: ${props.order?.address?.address || 'N/A'}\n\n` +
           `*Order Summary*\n` +
           `Order ID: #${props.order?.id || ''}\n` +
           `Total Products: ${props.order?.items?.length || 0}\n` +
           `Net Amount: ₹${props.order?.net_total || 0}\n\n` +
           `*Bill Link*\n` + 
           `${props.download_link}`;
});

const whatsappUrl = computed(() => {
    return `https://api.whatsapp.com/send?phone=919003660673&text=${encodeURIComponent(whatsappText.value)}`;
});
</script>

<template>
    <Head title="Thank You" />

    <AppLayout>
        <div class="flex min-h-[70vh] items-center justify-center px-4 py-12">
            <div class="w-full max-w-md text-center">
                
                <!-- Thank you card -->
                <div class="mx-auto rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 md:p-8">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto mb-4 h-16 w-16 text-green-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    
                    <h3 class="mb-2 font-heading text-2xl font-bold text-brand-dark">Thank You!</h3>
                    <p class="mb-6 text-base text-gray-600">Your order has been placed successfully.</p>
                    
                    <!-- Order Summary -->
                    <div class="mb-6 rounded-lg border border-gray-100 bg-gray-50 p-4 text-left">
                        <h4 class="mb-3 border-b border-gray-200 pb-2 font-semibold text-gray-800">Order Summary <span v-if="order?.id">(ID: #{{ order.id }})</span></h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Customer Name:</span>
                                <span class="font-medium text-gray-800">{{ order?.customer?.name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Items:</span>
                                <span class="font-medium text-gray-800">{{ order?.items?.length }} Products</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Total Amount:</span>
                                <span class="font-bold text-gray-900">₹{{ order?.net_total }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <p class="mb-6 text-sm text-gray-600">
                        Please send your order details via WhatsApp to proceed with the payment and confirmation.
                    </p>
                    
                    <div class="flex flex-col gap-3">
                        <a :href="whatsappUrl" target="_blank" class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#25D366] px-6 py-3 font-semibold text-white shadow-md transition-all hover:bg-[#128C7E] hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                            </svg>
                            Send via WhatsApp
                        </a>
                        
                        <a :href="download_link" target="_blank" class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition-all hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Download Bill PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
