<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { useStore } from 'vuex'
import { pick } from 'lodash'

const props = defineProps({
    global_discount: Number,
    min_order_value: Number,
    mobile_number_1: Number,
    mobile_number_2: Number,
    mobile_number_3: Number,
    mobile_number_4: Number,
    mobile_number_5: Number,
    company_address: String,
    bank_details: Object,
})

const store = useStore()
const cartItems = computed(() => store.state.cartItems)
const totalPrice = computed(() => store.getters.totalPrice)
const totalItems = computed(() => store.getters.totalItems)

const discountAmount = computed(() =>
    Math.round((props.global_discount / 100) * totalPrice.value)
)
const discountedTotal = computed(() =>
    Math.round(totalPrice.value - (totalPrice.value * props.global_discount) / 100)
)
const canSubmit = computed(() => discountedTotal.value >= props.min_order_value)

const finalPrice = (price) => Math.round(price - (price * props.global_discount) / 100)

const orderItems = computed(() => {
    return store.getters.getOrderItems.map(item => pick(item, ['id', 'quantity']))
})

const sameAsMobile = ref(true)

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
    if (sameAsMobile.value) {
        form.whatsapp_number = form.mobile_number
    }
}

const addItem = (item) => store.commit('addToCart', item)
const removeItem = (item) => store.commit('removeItemFromCart', item)
const deleteItem = (item) => store.commit('deleteFromCart', item)

const showContactModal = ref(false)
const submitting = ref(false)

const submitOrder = () => {
    if (submitting.value) return
    submitting.value = true
    form.order_items = orderItems
    form.post(route('orders.store'), {
        onSuccess: () => store.commit('clearCart'),
        onError: () => { submitting.value = false },
    })
}
</script>

<template>
    <Head title="Checkout" />

    <AppLayout
        :global-discount="global_discount"
        :min-order-value="min_order_value"
        :company_address="company_address"
        :mobile_number_1="mobile_number_1"
        :mobile_number_2="mobile_number_2"
        :mobile_number_3="mobile_number_3"
        :mobile_number_4="mobile_number_4"
        :mobile_number_5="mobile_number_5"
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
                        <a v-if="mobile_number_1" :href="`tel:+91${mobile_number_1}`" class="block rounded-lg bg-gray-50 px-4 py-2.5 text-sm font-semibold text-brand-dark hover:bg-red-50 hover:text-brand-red">+91 {{ mobile_number_1 }}</a>
                        <a v-if="mobile_number_2" :href="`tel:+91${mobile_number_2}`" class="block rounded-lg bg-gray-50 px-4 py-2.5 text-sm font-semibold text-brand-dark hover:bg-red-50 hover:text-brand-red">+91 {{ mobile_number_2 }}</a>
                        <a v-if="mobile_number_3" :href="`tel:+91${mobile_number_3}`" class="block rounded-lg bg-gray-50 px-4 py-2.5 text-sm font-semibold text-brand-dark hover:bg-red-50 hover:text-brand-red">+91 {{ mobile_number_3 }}</a>
                        <a v-if="mobile_number_4" :href="`tel:+91${mobile_number_4}`" class="block rounded-lg bg-gray-50 px-4 py-2.5 text-sm font-semibold text-brand-dark hover:bg-red-50 hover:text-brand-red">+91 {{ mobile_number_4 }}</a>
                        <a v-if="mobile_number_5" :href="`tel:+91${mobile_number_5}`" class="block rounded-lg bg-gray-50 px-4 py-2.5 text-sm font-semibold text-brand-dark hover:bg-red-50 hover:text-brand-red">+91 {{ mobile_number_5 }}</a>
                    </div>
                    <button @click="showContactModal = false" class="mt-4 rounded-lg bg-brand-dark px-6 py-2 text-sm font-semibold text-white hover:bg-gray-800">Close</button>
                </div>
            </div>
        </Transition>

        <div class="mx-auto max-w-6xl px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm text-gray-400">
                <a href="/" class="hover:text-brand-red">Home</a>
                <span class="mx-2">/</span>
                <span class="text-brand-dark">Checkout</span>
            </nav>

            <h1 class="mb-8 font-heading text-2xl font-bold text-brand-dark">Checkout</h1>

            <!-- Empty cart -->
            <div v-if="cartItems.length === 0" class="py-20 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto mb-4 h-16 w-16 text-gray-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121 0 2.09-.773 2.34-1.867l1.86-8.154A.75.75 0 0 0 20.44 3.5H6.456M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                <p class="text-lg font-medium text-gray-500">Your cart is empty</p>
                <a href="/" class="mt-4 inline-block rounded-lg bg-brand-red px-6 py-2 text-sm font-semibold text-white hover:bg-brand-red-hover">
                    Continue Shopping
                </a>
            </div>

            <!-- Checkout form -->
            <form v-else @submit.prevent="submitOrder" class="grid gap-8 lg:grid-cols-5">
                <!-- Customer Details (shows first on mobile via order-first) -->
                <div class="order-first lg:order-last lg:col-span-2">
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                        <h2 class="mb-4 font-heading text-lg font-semibold text-brand-dark">Customer Details</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-brand-gray">Name *</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red"
                                    placeholder="Your full name"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-brand-gray">Mobile Number *</label>
                                <input
                                    v-model="form.mobile_number"
                                    type="tel"
                                    required
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red"
                                    placeholder="10-digit mobile number"
                                    @input="sameAsMobile && (form.whatsapp_number = form.mobile_number)"
                                />
                                <p v-if="form.mobile_number && !isMobileValid" class="mt-1 text-xs text-red-500">
                                    Mobile number should be 10 digits
                                </p>
                            </div>

                            <div>
                                <div class="mb-1 flex items-center justify-between">
                                    <label class="text-sm font-medium text-brand-gray">WhatsApp Number *</label>
                                    <label class="flex items-center gap-1.5 text-xs text-gray-400">
                                        <input
                                            v-model="sameAsMobile"
                                            type="checkbox"
                                            class="rounded border-gray-300 text-brand-red focus:ring-brand-red"
                                            @change="handleSameAsMobile"
                                        />
                                        Same as mobile
                                    </label>
                                </div>
                                <input
                                    v-model="form.whatsapp_number"
                                    type="tel"
                                    required
                                    :disabled="sameAsMobile"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red disabled:bg-gray-50"
                                    placeholder="10-digit WhatsApp number"
                                />
                                <p v-if="form.whatsapp_number && !isWhatsAppValid" class="mt-1 text-xs text-red-500">
                                    WhatsApp number should be 10 digits
                                </p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-brand-gray">City / Delivery Area *</label>
                                <input
                                    v-model="form.city_town"
                                    type="text"
                                    required
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red"
                                    placeholder="City or town"
                                />
                                <p v-if="form.errors.city_town" class="mt-1 text-xs text-red-500">{{ form.errors.city_town }}</p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-brand-gray">Delivery Address *</label>
                                <textarea
                                    v-model="form.address"
                                    required
                                    rows="3"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red"
                                    placeholder="Full delivery address"
                                ></textarea>
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
                    </div>
                </div>

                <!-- Order Summary (shows second on mobile, first on desktop) -->
                <div class="order-last lg:order-first lg:col-span-3">
                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100 sm:p-6">
                        <h2 class="mb-4 font-heading text-lg font-semibold text-brand-dark">Order Summary</h2>

                        <div class="divide-y">
                            <div
                                v-for="item in cartItems"
                                :key="item.id"
                                class="py-3"
                            >
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="item.image"
                                        :src="'/storage/' + item.image"
                                        :alt="item.name"
                                        class="h-12 w-12 shrink-0 rounded-lg object-cover"
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
                                        <button
                                            type="button"
                                            @click="removeItem(item)"
                                            class="flex h-8 w-8 items-center justify-center rounded-l-lg text-base font-bold text-white active:bg-brand-red-hover"
                                        >&minus;</button>
                                        <span class="w-7 text-center text-sm font-bold text-white">{{ item.quantity }}</span>
                                        <button
                                            type="button"
                                            @click="addItem(item)"
                                            class="flex h-8 w-8 items-center justify-center rounded-r-lg text-base font-bold text-white active:bg-brand-red-hover"
                                        >+</button>
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
                                <span>-₹{{ discountAmount }}</span>
                            </div>
                            <div class="flex justify-between border-t pt-2 text-lg font-bold text-brand-dark">
                                <span>Net Total</span>
                                <span>₹{{ discountedTotal }}</span>
                            </div>
                        </div>

                        <p v-if="!canSubmit" class="mt-3 text-center text-xs text-red-500">
                            Minimum order value: ₹{{ min_order_value }}
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
