<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    open: Boolean,
    globalDiscount: { type: Number, default: 0 },
    minOrderValue: { type: Number, default: 0 },
})

const emit = defineEmits(['close'])

const store = useStore()
const cartItems = computed(() => store.state.cartItems)
const totalPrice = computed(() => store.getters.totalPrice)
const totalItems = computed(() => store.getters.totalItems)

const discountedTotal = computed(() =>
    Math.round(totalPrice.value - (totalPrice.value * props.globalDiscount) / 100)
)

const discountAmount = computed(() =>
    Math.round((props.globalDiscount / 100) * totalPrice.value)
)

const finalPrice = (price) => Math.round(price - (price * props.globalDiscount) / 100)

const canCheckout = computed(() => discountedTotal.value >= props.minOrderValue)

const addItem = (item) => store.commit('addToCart', item)
const removeItem = (item) => store.commit('removeItemFromCart', item)
const deleteItem = (item) => store.commit('deleteFromCart', item)
const clearCart = () => store.commit('clearCart')
</script>

<template>
    <!-- Backdrop -->
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="open" class="fixed inset-0 z-40 bg-black/50" @click="$emit('close')" />
    </Transition>

    <!-- Drawer -->
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-x-0"
        leave-to-class="translate-x-full"
    >
        <div v-if="open" class="fixed bottom-0 right-0 top-0 z-50 flex w-full max-w-md flex-col bg-white shadow-2xl">
            <!-- Header -->
            <div class="flex items-center justify-between border-b px-4 py-3">
                <h2 class="font-heading text-lg font-semibold text-brand-dark">
                    Your Cart ({{ totalItems }})
                </h2>
                <button @click="$emit('close')" class="rounded-lg p-1 text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Items -->
            <div class="flex-1 overflow-y-auto px-4 py-3">
                <div v-if="cartItems.length === 0" class="flex h-full items-center justify-center text-center text-gray-400">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto mb-3 h-12 w-12 opacity-30">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121 0 2.09-.773 2.34-1.867l1.86-8.154A.75.75 0 0 0 20.44 3.5H6.456M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                        <p class="font-medium">Your cart is empty</p>
                        <p class="mt-1 text-sm">Add products to get started</p>
                    </div>
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="item in cartItems"
                        :key="item.id"
                        class="rounded-lg border border-gray-100 p-3"
                    >
                        <div class="flex items-center gap-3">
                            <img
                                v-if="item.image"
                                :src="'/storage/' + item.image"
                                :alt="item.name"
                                class="h-12 w-12 shrink-0 rounded-lg object-cover"
                            />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-brand-dark">{{ item.name }}</p>
                                <div class="flex items-center gap-1.5">
                                    <span v-if="globalDiscount > 0" class="text-xs text-gray-400 line-through">₹{{ item.price }}</span>
                                    <span class="text-xs" :class="globalDiscount > 0 ? 'text-green-600 font-medium' : 'text-gray-400'">₹{{ globalDiscount > 0 ? finalPrice(item.price) : item.price }} each</span>
                                </div>
                            </div>
                            <p class="shrink-0 text-sm font-semibold text-brand-dark">
                                ₹{{ globalDiscount > 0 ? finalPrice(item.price) * item.quantity : item.price * item.quantity }}
                            </p>
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                            <button
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
                                    @click="removeItem(item)"
                                    class="flex h-8 w-8 items-center justify-center rounded-l-lg text-base font-bold text-white active:bg-brand-red-hover"
                                >&minus;</button>
                                <span class="w-7 text-center text-sm font-bold text-white">{{ item.quantity }}</span>
                                <button
                                    @click="addItem(item)"
                                    class="flex h-8 w-8 items-center justify-center rounded-r-lg text-base font-bold text-white active:bg-brand-red-hover"
                                >+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div v-if="cartItems.length > 0" class="border-t px-4 py-4">
                <div class="mb-3 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">MRP Total</span>
                        <span>₹{{ totalPrice }}</span>
                    </div>
                    <div class="flex justify-between text-green-600">
                        <span>Discount ({{ globalDiscount }}%)</span>
                        <span>-₹{{ discountAmount }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-1 text-base font-bold text-brand-dark">
                        <span>Net Total</span>
                        <span>₹{{ discountedTotal }}</span>
                    </div>
                </div>

                <p v-if="!canCheckout" class="mb-2 text-center text-xs text-red-500">
                    Minimum order value: ₹{{ minOrderValue }}
                </p>

                <Link
                    href="/checkout"
                    :class="[
                        'block w-full rounded-lg py-3 text-center text-sm font-semibold text-white transition-colors',
                        canCheckout ? 'bg-brand-red hover:bg-brand-red-hover' : 'pointer-events-none bg-gray-300',
                    ]"
                >
                    Proceed to Checkout
                </Link>

                <button
                    @click="clearCart()"
                    class="mt-2 w-full text-center text-xs text-gray-400 hover:text-red-500"
                >
                    Clear Cart
                </button>
            </div>
        </div>
    </Transition>
</template>
