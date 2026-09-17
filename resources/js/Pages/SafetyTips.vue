<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    mobile_number_1: String,
    mobile_number_2: String,
    mobile_number_3: String,
    mobile_number_4: String,
    mobile_number_5: String,
    company_address: String,
})

const safetyTips = [
    {
        title: 'Open Spaces Only',
        description: 'Always light crackers in open grounds, away from houses, vehicles, and easily flammable materials.',
        icon: 'M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0v-1M12 16.5v-1m-4.5 4.5v2.25m0-2.25v-2.25m9 2.25v-2.25m0 2.25v2.25'
    },
    {
        title: 'Supervise Children',
        description: 'Never let children light fireworks unsupervised. Always ensure an adult is present to guide them.',
        icon: 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z'
    },
    {
        title: 'Wear Comfortable Clothing',
        description: 'Avoid wearing loose, synthetic, or flowing silk clothes. Cotton clothing is highly recommended for safety while handling crackers.',
        icon: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'
    },
    {
        title: 'Keep Water Nearby',
        description: 'Always maintain a bucket of water or sand nearby. In case of a small accident, this can extinguish fires immediately.',
        icon: 'M14.25 9.75L16.5 12l2.25-2.25M7.5 9.75L5.25 12l2.25 2.25M12 9v12m0-12l3-3m-3 3l-3-3M3.375 12A8.625 8.625 0 0012 20.625 8.625 8.625 0 0020.625 12M12 3.375A8.625 8.625 0 0120.625 12a8.625 8.625 0 011-8.625'
    },
    {
        title: 'Light One at a Time',
        description: 'Ignite fireworks one by one. Do not try to light multiple crackers simultaneously, as it poses immense risk.',
        icon: 'M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5'
    },
    {
        title: 'Safe Distance',
        description: 'Once a cracker is lit, immediately move back to a safe distance. Never lean directly over fireworks.',
        icon: 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-4.5v9M12 7.5L9 10.5M12 7.5l3 3M15 15l-3 3M9 15l3 3'
    },
]
</script>

<template>
    <AppLayout
        :company_address="company_address"
        :mobile_number_1="mobile_number_1"
        :mobile_number_2="mobile_number_2"
        :mobile_number_3="mobile_number_3"
        :mobile_number_4="mobile_number_4"
        :mobile_number_5="mobile_number_5"
    >
        <Head title="Safety Tips - Sri Subamangala Crackers" />

        <!-- Banner Section -->
        <div class="w-full">
            <img
                src="/assets/img/sri-mangala-crackers/safetytips-banner.webp"
                alt="Safety Tips - Sri Subamangala Crackers"
                class="w-full h-auto"
            />
        </div>

        <div class="bg-white py-16 sm:py-24">
            <div class="mx-auto max-w-6xl px-4">
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div 
                        v-for="(tip, index) in safetyTips" 
                        :key="index"
                        class="rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-lg transition-all hover:-translate-y-1 hover:shadow-xl"
                    >
                        <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-rose-50 text-brand-red">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="tip.icon" />
                            </svg>
                        </div>
                        <h3 class="mb-3 font-heading text-xl font-bold text-gray-900">{{ tip.title }}</h3>
                        <p class="text-gray-600 leading-relaxed">{{ tip.description }}</p>
                    </div>
                </div>

                <!-- Call to action block -->
                <div class="mt-16 overflow-hidden rounded-3xl bg-brand-red px-6 py-12 shadow-xl sm:px-12 sm:py-16">
                    <div class="mx-auto max-w-2xl text-center text-white">
                        <h2 class="mb-4 font-heading text-3xl font-bold">Have a Safe Celebration!</h2>
                        <p class="mb-8 text-lg text-rose-100">
                            Our team at Sri Subamangala Crackers wishes you a prosperous and deeply joyous Diwali. Let's pledge to celebrate safely.
                        </p>
                        <div class="flex flex-wrap justify-center gap-4">
                            <Link 
                                href="/"
                                class="inline-flex items-center rounded-full bg-white px-8 py-3.5 text-base font-semibold text-brand-red shadow-sm transition-all hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-brand-red"
                            >
                                Shop Crackers
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>