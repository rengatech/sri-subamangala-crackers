<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed, nextTick, onMounted, onUnmounted } from 'vue'
import { useStore } from 'vuex'
import { pick } from 'lodash'

const props = defineProps({
    status: String,
    categories: Object,
    global_discount: Number,
    starting_year: Number,
    min_order_value: Number,
    mobile_numbers: Array,
    marquee_content: Object,
    bank_details: Object,
    company_address: String,
})

const store = useStore()
const showAnnouncement = ref(true)
const showContactModal = ref(false)
const imageModal = ref(null)

// Category accordion
const openCategories = ref(
    props.categories ? props.categories.map(c => c.id) : []
)

const collapseAll = () => { openCategories.value = [] }
const expandAll = () => { openCategories.value = props.categories ? props.categories.map(c => c.id) : [] }
const allExpanded = computed(() => props.categories && openCategories.value.length === props.categories.length)
const toggleCategory = (id) => {
    const index = openCategories.value.indexOf(id)
    if (index === -1) openCategories.value.push(id)
    else openCategories.value.splice(index, 1)
}
const isCategoryOpen = (id) => openCategories.value.includes(id)

// Cart
const totalPrice = computed(() => store.getters.totalPrice)
const totalItems = computed(() => store.getters.totalItems)
const cartItems = computed(() => store.state.cartItems)
const itemCount = (id) => store.getters.countByItem(id)

const addItem = (product) => store.commit('addToCart', product)
const removeItem = (product) => store.commit('removeItemFromCart', product)
const deleteItem = (item) => store.commit('deleteFromCart', item)
const clearCart = () => store.commit('clearCart')

const discountAmount = (price) => Math.round((price * props.global_discount) / 100)
const finalPrice = (price) => price - discountAmount(price)

const discountTotalAmount = computed(() =>
    Math.round((props.global_discount / 100) * totalPrice.value)
)
const discountedTotal = computed(() =>
    Math.round(totalPrice.value - (totalPrice.value * props.global_discount) / 100)
)
const canSubmit = computed(() => discountedTotal.value >= props.min_order_value)

// Checkout form
const orderItems = computed(() =>
    store.getters.getOrderItems.map(item => pick(item, ['id', 'quantity']))
)

const sameAsMobile = ref(true)
const submitting = ref(false)

const form = useForm({
    name: '',
    mobile_number: '',
    whatsapp_number: '',
    address: '',
    city_town: '',
    order_items: null,
})

const isMobileValid = computed(() => /^\d{10}$/.test(form.mobile_number))
const isWhatsAppValid = computed(() => /^\d{10}$/.test(form.whatsapp_number))

const handleSameAsMobile = () => {
    if (sameAsMobile.value) form.whatsapp_number = form.mobile_number
}

const submitOrder = () => {
    if (submitting.value) return
    submitting.value = true
    form.order_items = orderItems
    form.post(route('orders.store'), {
        onSuccess: () => store.commit('clearCart'),
        onError: () => { submitting.value = false },
    })
}

const scrollToCheckout = () => {
    document.getElementById('checkout-section')?.scrollIntoView({ behavior: 'smooth' })
}

// Smart scroll button — toggles between top/bottom based on position
const isNearBottom = ref(false)
const onScroll = () => {
    const scrollPos = window.scrollY + window.innerHeight
    isNearBottom.value = scrollPos >= document.body.scrollHeight - 200
}
const smartScroll = () => {
    if (isNearBottom.value) {
        window.scrollTo({ top: 0, behavior: 'smooth' })
    } else {
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })
    }
}
onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true })
    // Auto-scroll to checkout if navigated from another page
    const params = new URLSearchParams(window.location.search)
    if (params.get('scroll') === 'checkout') {
        nextTick(() => {
            document.getElementById('checkout-section')?.scrollIntoView({ behavior: 'smooth' })
        })
        // Clean up URL
        window.history.replaceState({}, '', window.location.pathname)
    }
})
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<template>
    <Head title="Buy Crackers Online Sivakasi | Wholesale Fireworks">
        <meta name="description" content="Buy crackers online from Sri Subamangala Crackers, Sivakasi. Wholesale & retail fireworks at best prices. Sparklers, aerial shots, gift boxes. All India delivery." />
    </Head>

    <AppLayout
        :global-discount="global_discount"
        :min-order-value="min_order_value"
        :company_address="company_address"
        :mobile_numbers="mobile_numbers"
        @contact-click="showContactModal = true"
        @scroll-to-checkout="scrollToCheckout"
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

        <!-- Banner Section -->
        <div class="w-full">
            <img
                src="/assets/img/sri-mangala-crackers/home-banner.jpg"
                alt="Sri Subamangala Crackers Banner"
                class="w-full h-auto"
            />
        </div>

        <!-- Marquee / Announcement -->
        <div v-if="marquee_content" class="overflow-hidden bg-brand-dark py-1.5">
            <p class="animate-marquee whitespace-nowrap text-sm font-medium text-white sm:text-base">
                {{ marquee_content }}
            </p>
        </div>

        <!-- Product Image Disclaimer -->
        <div class="mx-auto max-w-6xl px-4 pt-4">
            <div class="flex flex-col items-center justify-center gap-1.5 rounded-lg border-2 border-brand-red/40 bg-red-50 px-4 py-3 text-center sm:flex-row sm:gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 shrink-0 text-brand-red">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <p class="text-sm font-bold text-brand-red sm:text-base">
                    Note: The Product Image is only for your reference — packing and brand may change.
                </p>
            </div>
        </div>

        <!-- Category Accordion -->
        <div class="mx-auto max-w-6xl px-4 py-6 space-y-4">
            <!-- Floating buttons (left side, stacked above WhatsApp & cart) -->
            <div class="fixed bottom-6 right-6 z-30 flex flex-col items-end gap-3">
                <button
                    @click="smartScroll"
                    class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-dark text-white shadow-lg transition-transform hover:scale-110"
                    :title="isNearBottom ? 'Scroll to top' : 'Scroll to bottom'"
                >
                    <!-- Up arrow when near bottom -->
                    <svg v-if="isNearBottom" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                    </svg>
                    <!-- Down arrow when near top/middle -->
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <button
                    @click="allExpanded ? collapseAll() : expandAll()"
                    class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-dark text-white shadow-lg transition-transform hover:scale-110"
                    :title="allExpanded ? 'Collapse All' : 'Expand All'"
                >
                    <svg v-if="allExpanded" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l7.5-7.5 7.5 7.5m-15 6l7.5-7.5 7.5 7.5" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 5.25l-7.5 7.5-7.5-7.5m15 6l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </div>
            <div
                v-for="(category, catIndex) in categories"
                :key="category.id"
                class="animate-fade-up rounded-xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden transition-shadow duration-300 hover:shadow-md"
                :style="{ animationDelay: `${catIndex * 0.08}s` }"
            >
                <!-- Accordion Header -->
                <button
                    @click="toggleCategory(category.id)"
                    class="flex w-full items-center justify-between bg-brand-red px-4 py-3 sm:px-6 text-left transition-colors hover:bg-brand-red-hover"
                >
                    <h2 class="font-heading text-base font-bold text-white sm:text-lg">
                        {{ category.category }}
                    </h2>
                    <div class="flex items-center gap-2">
                        <span class="rounded-full bg-white px-2.5 py-0.5 text-xs font-bold text-brand-red">
                            {{ category.products.length }} items
                        </span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            :class="['h-5 w-5 text-white/70 transition-transform duration-200', isCategoryOpen(category.id) ? 'rotate-180' : '']"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Accordion Content — Product List -->
                <div v-show="isCategoryOpen(category.id)" class="divide-y divide-gray-200 px-3 sm:px-4">
                    <div
                        v-for="(product, index) in category.products"
                        :key="product.id"
                        class="animate-fade-up flex items-center py-3 sm:py-4"
                        :style="{ animationDelay: `${index * 0.04}s` }"
                    >
                        <!-- Left: Product Info (tabular) -->
                        <div class="flex-1 p-3 sm:p-4">
                            <!-- Mobile: stacked layout -->
                            <div class="sm:hidden">
                                <p class="text-sm font-semibold text-brand-dark">{{ product.name }}</p>
                                <p v-if="product.tamil_name" class="text-xs text-gray-400">{{ product.tamil_name }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span v-if="global_discount > 0" class="text-xs text-gray-400 line-through">₹{{ product.price }}</span>
                                    <span :class="['text-sm font-bold', global_discount > 0 ? 'text-green-600' : 'text-brand-dark']">₹{{ global_discount > 0 ? finalPrice(product.price) : product.price }}</span>
                                </div>
                            </div>
                            <!-- Desktop: even 4-column grid -->
                            <div class="hidden sm:grid sm:items-center sm:gap-4" :style="global_discount > 0 ? 'grid-template-columns: 1fr 1fr auto auto' : 'grid-template-columns: 1fr 1fr auto'">
                                <p class="text-base font-semibold text-brand-dark truncate">{{ product.name }}</p>
                                <p class="text-sm text-gray-500 truncate">{{ product.tamil_name || '—' }}</p>
                                <span :class="['text-base whitespace-nowrap', global_discount > 0 ? 'text-gray-400 line-through' : 'font-bold text-brand-dark']">₹{{ product.price }}</span>
                                <span v-if="global_discount > 0" class="text-base font-bold text-green-600 whitespace-nowrap">₹{{ finalPrice(product.price) }}</span>
                            </div>
                        </div>
                        <!-- Right: Image with ADD overlay -->
                        <div class="img-hover-zoom relative w-28 shrink-0 sm:w-32">
                            <img
                                v-if="product.image"
                                :src="'/storage/' + product.image"
                                :alt="product.name"
                                class="h-full w-full cursor-pointer rounded-lg object-cover"
                                @click="imageModal = '/storage/' + product.image"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center bg-gray-100 text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v14.25a1.5 1.5 0 0 0 1.5 1.5Z" /></svg>
                            </div>
                            <!-- ADD button overlay -->
                            <div class="absolute bottom-1.5 left-1/2 -translate-x-1/2 w-[6.75rem]">
                                <Transition
                                    enter-active-class="transition duration-200 ease-out"
                                    enter-from-class="opacity-0 scale-75"
                                    enter-to-class="opacity-100 scale-100"
                                    leave-active-class="transition duration-150 ease-in"
                                    leave-from-class="opacity-100 scale-100"
                                    leave-to-class="opacity-0 scale-75"
                                    mode="out-in"
                                >
                                    <button
                                        v-if="itemCount(product.id) === 0"
                                        key="add"
                                        @click="addItem(product)"
                                        class="w-full rounded-lg bg-brand-red py-1.5 text-xs font-bold text-white shadow-md transition-colors hover:bg-brand-red-hover active:scale-95"
                                    >ADD</button>
                                    <div v-else key="qty" class="flex w-full items-center rounded-lg bg-brand-red shadow-md">
                                        <button @click="removeItem(product)" class="flex h-8 w-8 items-center justify-center rounded-l-lg text-base font-bold text-white active:bg-brand-red-hover">&minus;</button>
                                        <span class="flex-1 text-center text-sm font-bold text-white">{{ itemCount(product.id) }}</span>
                                        <button @click="addItem(product)" class="flex h-8 w-8 items-center justify-center rounded-r-lg text-base font-bold text-white active:bg-brand-red-hover">+</button>
                                    </div>
                                </Transition>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== INLINE CHECKOUT SECTION ===== -->
        <div id="checkout-section" class="bg-gray-50 py-8">
            <div class="mx-auto max-w-6xl px-4">
                <h2 class="mb-6 text-center font-heading text-2xl font-bold text-brand-dark border-b-4 border-brand-red pb-3">Order Confirmation</h2>

                <!-- Empty cart state -->
                <div v-if="cartItems.length === 0" class="py-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto mb-4 h-16 w-16 text-gray-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121 0 2.09-.773 2.34-1.867l1.86-8.154A.75.75 0 0 0 20.44 3.5H6.456M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                    <p class="text-lg font-medium text-gray-500">No items are added to cart</p>
                    <p class="mt-1 text-sm text-gray-400">Please add items from the product list above</p>
                </div>

                <form v-else @submit.prevent="submitOrder" class="grid gap-6 lg:grid-cols-5">
                    <!-- Order Summary -->
                    <div class="lg:col-span-3">
                        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100 sm:p-6">
                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="font-heading text-lg font-semibold text-brand-dark">Order Summary ({{ totalItems }} items)</h3>
                                <button
                                    type="button"
                                    @click="clearCart"
                                    class="flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 transition-colors hover:bg-red-100"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    Clear Cart
                                </button>
                            </div>

                            <div class="divide-y">
                                <div v-for="item in cartItems" :key="item.id" class="py-3">
                                    <div class="flex items-center gap-3">
                                        <img
                                            v-if="item.image"
                                            :src="'/storage/' + item.image"
                                            :alt="item.name"
                                            class="h-9 w-9 shrink-0 rounded-md object-cover"
                                        />
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-brand-dark">{{ item.name }}</p>
                                            <div class="flex items-center gap-1.5">
                                                <span v-if="global_discount > 0" class="text-xs text-gray-400 line-through">₹{{ item.price }}</span>
                                                <span class="text-xs" :class="global_discount > 0 ? 'text-green-600 font-medium' : 'text-gray-400'">₹{{ global_discount > 0 ? finalPrice(item.price) : item.price }} each</span>
                                            </div>
                                        </div>
                                        <p class="shrink-0 text-sm font-semibold">₹{{ global_discount > 0 ? finalPrice(item.price) * item.quantity : item.price * item.quantity }}</p>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <button
                                            type="button"
                                            @click="deleteItem(item)"
                                            class="rounded-lg p-1.5 text-red-400 transition-colors hover:bg-red-50 hover:text-red-600"
                                            title="Remove item"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                        <div class="flex items-center rounded-lg bg-brand-red">
                                            <button type="button" @click="removeItem(item)" class="flex h-8 w-8 items-center justify-center rounded-l-lg text-base font-bold text-white active:bg-brand-red-hover">&minus;</button>
                                            <span class="w-7 text-center text-sm font-bold text-white">{{ item.quantity }}</span>
                                            <button type="button" @click="addItem(item)" class="flex h-8 w-8 items-center justify-center rounded-r-lg text-base font-bold text-white active:bg-brand-red-hover">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Totals -->
                            <div class="mt-4 space-y-2 border-t pt-4 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">MRP Total</span>
                                    <span>₹{{ totalPrice }}</span>
                                </div>
                                <div class="flex justify-between text-green-600">
                                    <span>Discount ({{ global_discount }}%)</span>
                                    <span>-₹{{ discountTotalAmount }}</span>
                                </div>
                                <div class="flex justify-between border-t pt-2 text-lg font-bold text-brand-dark">
                                    <span>Net Total</span>
                                    <span>₹{{ discountedTotal }}</span>
                                </div>
                            </div>

                            <p v-if="!canSubmit" class="animate-blink-color mt-4 rounded-lg bg-red-50 px-4 py-4 text-center text-base font-bold">
                                <span>Minimum order value: ₹{{ min_order_value }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Customer Details -->
                    <div class="lg:col-span-2">
                        <div class="rounded-xl border-2 border-brand-red bg-white p-6 shadow-sm">
                            <h3 class="mb-4 font-heading text-lg font-semibold text-brand-dark">Customer Details</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="mb-1.5 block text-sm font-semibold text-brand-dark">Name *</label>
                                    <input v-model="form.name" type="text" required class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-base focus:border-brand-red focus:ring-brand-red" placeholder="Your full name" />
                                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-semibold text-brand-dark">Mobile Number *</label>
                                    <input v-model="form.mobile_number" type="tel" required class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-base focus:border-brand-red focus:ring-brand-red" placeholder="10-digit mobile number" @input="sameAsMobile && (form.whatsapp_number = form.mobile_number)" />
                                    <p v-if="form.mobile_number && !isMobileValid" class="mt-1 text-xs text-red-500">Mobile number should be 10 digits</p>
                                </div>

                                <div>
                                    <div class="mb-1 flex items-center justify-between">
                                        <label class="text-sm font-semibold text-brand-dark">WhatsApp Number *</label>
                                        <label class="flex items-center gap-1.5 text-xs text-gray-400">
                                            <input v-model="sameAsMobile" type="checkbox" class="rounded border-gray-300 text-brand-red focus:ring-brand-red" @change="handleSameAsMobile" />
                                            Same as mobile
                                        </label>
                                    </div>
                                    <input v-model="form.whatsapp_number" type="tel" required :disabled="sameAsMobile" class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-base focus:border-brand-red focus:ring-brand-red disabled:bg-gray-50" placeholder="10-digit WhatsApp number" />
                                    <p v-if="form.whatsapp_number && !isWhatsAppValid" class="mt-1 text-xs text-red-500">WhatsApp number should be 10 digits</p>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-semibold text-brand-dark">City / Delivery Area *</label>
                                    <input v-model="form.city_town" type="text" required class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-base focus:border-brand-red focus:ring-brand-red" placeholder="City or town" />
                                    <p v-if="form.errors.city_town" class="mt-1 text-xs text-red-500">{{ form.errors.city_town }}</p>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-semibold text-brand-dark">Delivery Address *</label>
                                    <textarea v-model="form.address" required rows="3" class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-base focus:border-brand-red focus:ring-brand-red" placeholder="Full delivery address"></textarea>
                                    <p v-if="form.errors.address" class="mt-1 text-xs text-red-500">{{ form.errors.address }}</p>
                                </div>
                            </div>

                            <button
                                type="submit"
                                :disabled="!canSubmit || !isMobileValid || !isWhatsAppValid || submitting"
                                :class="[
                                    'mt-6 w-full rounded-lg py-3 text-sm font-semibold text-white transition-colors',
                                    canSubmit && isMobileValid && isWhatsAppValid && !submitting
                                        ? 'bg-brand-red hover:bg-brand-red-hover'
                                        : 'bg-gray-300 cursor-not-allowed',
                                ]"
                            >
                                <span v-if="submitting" class="flex items-center justify-center gap-2">
                                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25" />
                                        <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75" />
                                    </svg>
                                    Placing Order...
                                </span>
                                <span v-else>Place Order — ₹{{ discountedTotal }}</span>
                            </button>
                            <p v-if="!canSubmit" class="animate-blink-color mt-3 rounded-lg bg-red-50 px-4 py-4 text-center text-base font-bold">
                                <span>Minimum order value: ₹{{ min_order_value }}</span>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bank Details Section -->
        <div v-if="bank_details && bank_details.length > 0" class="bg-white py-10">
            <div class="mx-auto max-w-6xl px-4">
                <h2 class="mb-6 text-center font-heading text-2xl font-bold text-brand-dark">
                    Bank Details
                </h2>
                <div class="mx-auto grid max-w-4xl gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="(bank, bankIndex) in bank_details"
                        :key="bank.id"
                        class="animate-fade-up rounded-xl bg-brand-maroon p-5 text-white transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg"
                        :style="{ animationDelay: `${bankIndex * 0.1}s` }"
                    >
                        <div class="space-y-2 text-sm">
                            <p><span class="text-white/70">Name:</span> <span class="font-semibold text-yellow-300">{{ bank.name }}</span></p>
                            <p><span class="text-white/70">Bank:</span> <span class="font-semibold text-yellow-300">{{ bank.bank_name }}</span></p>
                            <p><span class="text-white/70">A/C No:</span> <span class="font-semibold text-yellow-300">{{ bank.account_number }}</span></p>
                            <p><span class="text-white/70">IFSC:</span> <span class="font-semibold text-yellow-300">{{ bank.ifsc_code }}</span></p>
                            <p><span class="text-white/70">Branch:</span> <span class="font-semibold text-yellow-300">{{ bank.branch }}</span></p>
                            <p><span class="text-white/70">Google Pay:</span> <span class="font-semibold text-yellow-300">{{ bank.g_pay }}</span></p>
                            <img
                                v-if="bank.image"
                                :src="'/storage/' + bank.image"
                                alt="Bank Screenshot"
                                class="mt-2 max-w-[200px] rounded-lg border-2 border-yellow-300"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Popup Modal -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
        >
            <div v-if="imageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4" @click="imageModal = null">
                <img :src="imageModal" alt="Product" class="max-h-[80vh] max-w-full rounded-xl object-contain shadow-2xl" />
                <button @click="imageModal = null" class="absolute right-4 top-4 rounded-full bg-white/20 p-2 text-white backdrop-blur hover:bg-white/40">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </Transition>

    </AppLayout>
</template>