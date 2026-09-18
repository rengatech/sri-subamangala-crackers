<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    company_address: String,
    mobile_numbers: Array,
})

const showContactModal = ref(false)
const openFaq = ref(null)
const toggleFaq = (index) => {
    openFaq.value = openFaq.value === index ? null : index
}

const faqs = [
    {
        category: 'Ordering',
        items: [
            {
                question: 'How do I place an order?',
                answer: 'Simply browse our products on the home page, add items to your cart using the + button, fill in your details in the order form, and submit. You will receive a confirmation via WhatsApp.',
            },
            {
                question: 'What is the minimum order value?',
                answer: 'The minimum order value is displayed on the order form. Orders below this amount cannot be placed.',
            },
            {
                question: 'Can I modify or cancel my order after placing it?',
                answer: 'Yes, you can contact us via WhatsApp or phone immediately after placing the order. Once packing has started, modifications may not be possible.',
            },
            {
                question: 'How will I receive my order confirmation?',
                answer: 'You will receive an order confirmation with an estimate PDF via WhatsApp to the number you provide during checkout.',
            },
        ],
    },
    {
        category: 'Delivery & Shipping',
        items: [
            {
                question: 'Do you deliver across India?',
                answer: 'Yes, we deliver crackers across India through trusted transport partners. Delivery charges may vary based on location and order size.',
            },
            {
                question: 'How long does delivery take?',
                answer: 'Delivery typically takes 3-7 business days depending on your location. You will receive an LR (Lorry Receipt) copy via WhatsApp once your order is dispatched.',
            },
            {
                question: 'How can I track my order?',
                answer: 'Once your order is dispatched, we will share the LR (Lorry Receipt) screenshot via WhatsApp. You can use this to track your shipment with the transport company.',
            },
        ],
    },
    {
        category: 'Payment',
        items: [
            {
                question: 'What payment methods do you accept?',
                answer: 'We accept bank transfers (NEFT/RTGS/IMPS), UPI payments, and Google Pay. Bank details are provided on our website.',
            },
            {
                question: 'Is advance payment required?',
                answer: 'Yes, full advance payment is required before dispatch. This ensures smooth processing and timely delivery of your order.',
            },
        ],
    },
    {
        category: 'Products & Safety',
        items: [
            {
                question: 'What is a green cracker?',
                answer: 'Green crackers are eco-friendly crackers that cause less air and noise pollution compared to traditional firecrackers. They are certified by CSIR-NEERI.',
            },
            {
                question: 'How to identify Green Crackers?',
                answer: 'Look for these labels: SWAS (Safe Water Releaser) - releases water vapour instead of pollutants. STAR (Safe Thermite Cracker) - no sulphur, lower sound. SAFAL (Safe Minimal Aluminium) - uses magnesium instead of aluminium.',
            },
            {
                question: 'Who certifies Green Crackers?',
                answer: 'CSIR-National Environmental Engineering Research Institute (CSIR-NEERI) certifies green crackers in India.',
            },
            {
                question: 'Are your products safe?',
                answer: 'Yes, all our products are manufactured by licensed factories in Sivakasi following strict safety standards. We recommend following all safety guidelines while using crackers.',
            },
            {
                question: 'Can I buy fireworks anytime?',
                answer: 'You can buy fireworks if you are over 18 from registered sellers. Typically available from 15th October to 10th November, 26th to 31st December, and 3 days before Diwali.',
            },
        ],
    },
    {
        category: 'Price & Offers',
        items: [
            {
                question: 'How long is the price list valid?',
                answer: 'Our price list is valid for 3 days from the date of download/generation. Prices may change after the validity period.',
            },
            {
                question: 'Do you offer discounts?',
                answer: 'Yes! We offer attractive discounts on all products. The discounted price is shown alongside the original price on our website.',
            },
            {
                question: 'Are the prices inclusive of GST?',
                answer: 'Yes, all prices displayed on our website are inclusive of applicable taxes.',
            },
        ],
    },
]
</script>

<template>
    <Head title="FAQ - Frequently Asked Questions | Sri Subamangala Crackers">
        <meta name="description" content="Frequently asked questions about buying crackers online from Sivakasi. Ordering, delivery, payment, safety tips, green crackers, and pricing - all answered." />
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
                    <button @click="showContactModal = false" class="mt-4 text-sm text-gray-400 hover:text-gray-600">Close</button>
                </div>
            </div>
        </Transition>

        <div class="bg-gradient-to-b from-red-50 to-white py-12">
            <div class="mx-auto max-w-4xl px-4">
                <h1 class="mb-2 text-center font-heading text-3xl font-bold text-brand-dark sm:text-4xl">Frequently Asked Questions</h1>
                <p class="mb-10 text-center text-gray-500">Everything you need to know about ordering crackers from Sri Subamangala Crackers</p>

                <div class="space-y-8">
                    <div v-for="(section, sIdx) in faqs" :key="sIdx">
                        <h2 class="mb-4 font-heading text-xl font-bold text-brand-red">{{ section.category }}</h2>
                        <div class="space-y-3">
                            <div
                                v-for="(faq, fIdx) in section.items"
                                :key="fIdx"
                                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md"
                            >
                                <button
                                    @click="toggleFaq(`${sIdx}-${fIdx}`)"
                                    class="flex w-full items-center justify-between px-5 py-4 text-left"
                                >
                                    <span class="pr-4 text-sm font-semibold text-brand-dark sm:text-base">{{ faq.question }}</span>
                                    <svg
                                        :class="['h-5 w-5 shrink-0 text-brand-red transition-transform duration-200', openFaq === `${sIdx}-${fIdx}` ? 'rotate-180' : '']"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <Transition
                                    enter-active-class="transition duration-200 ease-out"
                                    enter-from-class="opacity-0 -translate-y-2"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-active-class="transition duration-150 ease-in"
                                    leave-from-class="opacity-100"
                                    leave-to-class="opacity-0"
                                >
                                    <div v-if="openFaq === `${sIdx}-${fIdx}`" class="border-t border-gray-100 px-5 py-4">
                                        <p class="text-sm leading-relaxed text-gray-600">{{ faq.answer }}</p>
                                    </div>
                                </Transition>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 rounded-xl bg-brand-red/5 p-6 text-center">
                    <h3 class="mb-2 font-heading text-lg font-bold text-brand-dark">Still have questions?</h3>
                    <p class="mb-4 text-sm text-gray-500">We're here to help. Contact us anytime!</p>
                    <button @click="showContactModal = true" class="rounded-lg bg-brand-red px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-red-hover">
                        Contact Us
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
