<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    company_address: String,
    mobile_numbers: Array,
})

const showContactModal = ref(false)
</script>

<template>
    <Head title="Privacy Policy - Sri Subamangala Crackers">
        <meta name="description" content="Privacy Policy of Sri Subamangala Crackers. Learn how we collect, use, and protect your personal information when you order crackers from our website." />
    </Head>

    <AppLayout
        :company_address="company_address"
        :mobile_numbers="mobile_numbers"
        @contact-click="showContactModal = true"
    >
        <!-- Contact Modal -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showContactModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showContactModal = false">
                <div class="mx-4 w-full max-w-sm rounded-xl bg-white p-6 text-center shadow-xl">
                    <h3 class="mb-4 font-heading text-lg font-bold text-brand-red">Contact Numbers</h3>
                    <div class="space-y-3">
                        <a v-for="(number, idx) in mobile_numbers" :key="idx" :href="`tel:+91${number}`" class="block rounded-lg bg-gray-50 px-4 py-2.5 text-sm font-semibold text-brand-dark hover:bg-red-50 hover:text-brand-red">+91 {{ number }}</a>
                    </div>
                    <button @click="showContactModal = false" class="mt-4 rounded-lg bg-brand-dark px-6 py-2 text-sm font-semibold text-white hover:bg-gray-800">Close</button>
                </div>
            </div>
        </Transition>

        <div class="mx-auto max-w-3xl px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm text-gray-400">
                <a href="/" class="hover:text-brand-red">Home</a>
                <span class="mx-2">/</span>
                <span class="text-brand-dark">Privacy Policy</span>
            </nav>

            <h1 class="mb-2 font-heading text-3xl font-bold text-brand-dark">Privacy Policy</h1>
            <p class="mb-8 text-sm text-gray-400">Last updated: March 2026</p>

            <div class="prose prose-sm max-w-none text-brand-gray prose-headings:font-heading prose-headings:text-brand-dark prose-h3:text-lg prose-h3:font-semibold prose-a:text-brand-red">
                <h3>1. Introduction</h3>
                <p>
                    Sri Subamangala Crackers ("we", "our", "us") operates the website madhucrackers.com.
                    This Privacy Policy explains how we collect, use, and protect your personal information
                    when you visit our website or place orders with us.
                </p>

                <h3>2. Information We Collect</h3>
                <p>We collect the following information when you place an order or contact us:</p>
                <ul>
                    <li>Name and contact details (phone number, WhatsApp number)</li>
                    <li>Delivery address (street address, city, pincode)</li>
                    <li>Order details and payment information</li>
                    <li>Messages sent via WhatsApp for order-related communication</li>
                </ul>

                <h3>3. How We Use Your Information</h3>
                <p>Your information is used to:</p>
                <ul>
                    <li>Process and deliver your orders</li>
                    <li>Communicate order updates via WhatsApp or phone</li>
                    <li>Send promotional offers and price lists (with your consent)</li>
                    <li>Improve our services and customer experience</li>
                </ul>

                <h3>4. WhatsApp Communication</h3>
                <p>
                    We use the WhatsApp Business API to communicate with customers regarding orders,
                    delivery updates, and promotional offers. Messages are stored securely on our servers
                    for order tracking purposes. You may opt out of promotional messages at any time by
                    contacting us.
                </p>

                <h3>5. Data Security</h3>
                <p>
                    We implement appropriate security measures to protect your personal information
                    from unauthorized access, alteration, or destruction. Payment information is
                    processed securely and we do not store sensitive payment details on our servers.
                </p>

                <h3>6. Data Sharing</h3>
                <p>
                    We do not sell or share your personal information with third parties except as
                    necessary to fulfill your orders (e.g., delivery partners) or as required by law.
                </p>

                <h3>7. Your Rights</h3>
                <p>You have the right to:</p>
                <ul>
                    <li>Access the personal data we hold about you</li>
                    <li>Request correction of inaccurate data</li>
                    <li>Request deletion of your data</li>
                    <li>Opt out of promotional communications</li>
                </ul>

                <h3>8. Contact Us</h3>
                <p>
                    If you have any questions about this Privacy Policy, please contact us at:<br>
                    <strong>Sri Subamangala Crackers</strong><br>
                    Phone: {{ mobile_numbers?.[0] }}<br>
                    Address: {{ company_address }}
                </p>
            </div>
        </div>
    </AppLayout>
</template>
