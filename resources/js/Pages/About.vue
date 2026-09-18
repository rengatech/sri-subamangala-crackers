<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const showContactModal = ref(false)

const props = defineProps({
    about_page: Object,
    categories: Array,
    min_order_value: [String, Number],
    company_address: String,
    mobile_numbers: Array,
    whatsapp_number: String,
})

const hasAboutData = computed(() => Array.isArray(props.about_page) && props.about_page.length > 0)

const aboutText = computed(() => {
    if (hasAboutData.value && props.about_page[0]?.about) {
        return props.about_page[0].about
    }
    return 'Sri Subamangala Crackers is a trusted wholesale and retail crackers dealer based in Sivakasi, Tamil Nadu. We offer genuine prices and the best quality crackers, and we care about the safety of our customers, their families, and the environment.'
})

const featureFallbacks = {
    genuine_price: 'Fair, transparent pricing on every order, with no hidden charges.',
    best_quality: 'Premium quality crackers sourced directly from trusted manufacturers.',
    safe_to_use: 'Certified green crackers that are safer for you and the environment.',
    trusted: 'Thousands of happy customers across Tamil Nadu trust us every Diwali.',
}

const featureText = (key) => {
    if (hasAboutData.value && props.about_page[0]?.[key]) {
        return props.about_page[0][key]
    }
    return featureFallbacks[key]
}

// Static showcase images for the "Our Products" section (not tied to DB products).
// Put your 8 image files in /public/assets/img/sri-mangala-crackers/showcase/
// and update the file names below to match.
const showcaseImages = [
    { src: '/assets/img/sri-mangala-crackers/showcase/1.jpg', alt: 'Product 1' },
    { src: '/assets/img/sri-mangala-crackers/showcase/2.jpg', alt: 'Product 2' },
    { src: '/assets/img/sri-mangala-crackers/showcase/3.jpg', alt: 'Product 3' },
    { src: '/assets/img/sri-mangala-crackers/showcase/4.jpg', alt: 'Product 4' },
    { src: '/assets/img/sri-mangala-crackers/showcase/5.jpg', alt: 'Product 5' },
    { src: '/assets/img/sri-mangala-crackers/showcase/6.jpg', alt: 'Product 6' },
    { src: '/assets/img/sri-mangala-crackers/showcase/7.jpg', alt: 'Product 7' },
    { src: '/assets/img/sri-mangala-crackers/showcase/8.jpg', alt: 'Product 8' },
]

const openFaq = ref(null)
const toggleFaq = (index) => {
    openFaq.value = openFaq.value === index ? null : index
}

const faqs = [
    {
        question: 'What is green cracker?',
        answer: 'Green crackers are dubbed as \'eco-friendly\' crackers and are known to cause less air and noise pollution as compared to traditional firecrackers.',
    },
    {
        question: 'How to identify "Green Crackers"?',
        answer: 'SWAS - Safe Water Releaser: These crackers do not use sulphur or potassium nitrate, and thus release water vapour instead of certain key pollutants. STAR - Safe Thermite Cracker: Does not contain sulphur and potassium nitrate, has lower sound intensity. SAFAL - Safe Minimal Aluminium: Replaces aluminium content with magnesium and produces reduced levels of pollutants.',
    },
    {
        question: 'Who certify "Green Crackers"?',
        answer: 'CSIR-NATIONAL ENVIRONMENTAL ENGINEERING RESEARCH INSTITUTE (CSIR-NEERI).',
    },
    {
        question: 'Can I buy fireworks?',
        answer: 'You can\'t buy fireworks if you\'re under 18. If you\'re over 18 then you can buy fireworks from registered sellers during: 15th October - 10th November, 26th - 31st December, and 3 days before Diwali. For other dates, buy from a licensed shop.',
    },
    {
        question: 'How are the colours in fireworks made?',
        answer: 'The colours in fireworks are made from specific chemical compounds. For example, Strontium (Sr) or Lithium (Li) can make red when burnt. To make violet, you\'d need Potassium (K) or Rubidium (Rb).',
    },
]

const features = [
    { title: 'Genuine Price', icon: 'receipt', key: 'genuine_price' },
    { title: 'Best Quality', icon: 'cube', key: 'best_quality' },
    { title: 'Safe To Use', icon: 'shield-check', key: 'safe_to_use' },
    { title: 'Trusted', icon: 'shield', key: 'trusted' },
]
</script>

<template>
    <Head title="About Us - Sri Subamangala Crackers Sivakasi">
        <meta name="description" content="About Sri Subamangala Crackers - Trusted wholesale and retail crackers dealer in Sivakasi, Tamil Nadu. Genuine prices, best quality, safe green crackers." />
    </Head>

    <AppLayout
        :company_address="company_address"
        :mobile_numbers="mobile_numbers"
        :whatsapp_number="whatsapp_number"
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

        <!-- Page Banner -->
        <div class="w-full">
            <img
                src="/assets/img/sri-mangala-crackers/about-us-banner.jpg"
                alt="About Us"
                class="w-full h-auto"
            />
        </div>

        <div class="mx-auto max-w-6xl px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm text-gray-400">
                <a href="/" class="hover:text-brand-red"></a>
                <span class="mx-2"></span>
                <span class="text-brand-dark"></span>
            </nav>

            <!-- About Section -->
            <div class="mb-12">
                <div class="grid gap-12 lg:grid-cols-2 items-center">

                    <!-- Left Image -->
                    <div class="flex justify-center">
                        <img src="/assets/img/sri-mangala-crackers/about-side.jpg" alt="Sri Subamangala Crackers" class="w-full max-w-lg object-contain drop-shadow-sm rounded-4xl" />
                    </div>

                    <!-- Right Text Box -->
                    <div>
                        <div class="mb-6 flex items-center gap-4">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-amber-500 font-heading text-lg font-bold text-white shadow-sm">
                                SC
                            </div>
                            <div>
                                <h2 class="font-heading text-2xl font-bold text-brand-dark">Sri Subamangala Crackers</h2>
                                <h3 class="font-heading text-lg font-bold text-amber-500">Online Crackers Shop</h3>
                            </div>
                        </div>

                        <p class="mb-8 text-sm leading-relaxed text-brand-gray md:text-base">{{ aboutText }}</p>

                        <!-- Minimum Price Alert Box -->
                        <div class="border border-gray-200 bg-white px-6 py-8 text-center shadow-sm">
                            <p class="mb-3 text-xs font-bold tracking-[0.15em] text-gray-600 sm:text-sm">THE ORDERS BEGINS WITH A MINIMUM PRICE OF</p>
                            <p class="font-heading text-3xl font-black text-brand-dark sm:text-4xl">Rs.{{ min_order_value }}/-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="mb-8 mt-16">
                <h2 class="mb-6 text-center font-heading text-2xl font-bold text-brand-dark">
                    Frequently Asked Questions
                </h2>
                <div class="mx-auto max-w-3xl space-y-3">
                    <div
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden"
                    >
                        <button
                            @click="toggleFaq(index)"
                            class="flex w-full items-center justify-between px-5 py-4 text-left"
                        >
                            <span class="text-sm font-medium text-brand-dark">{{ faq.question }}</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                :class="['h-4 w-4 text-gray-400 transition-transform duration-200', openFaq === index ? 'rotate-180' : '']"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <Transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="max-h-0 opacity-0"
                            enter-to-class="max-h-96 opacity-100"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="max-h-96 opacity-100"
                            leave-to-class="max-h-0 opacity-0"
                        >
                            <div v-if="openFaq === index" class="overflow-hidden">
                                <p class="border-t px-5 py-4 text-sm leading-relaxed text-brand-gray">
                                    {{ faq.answer }}
                                </p>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>