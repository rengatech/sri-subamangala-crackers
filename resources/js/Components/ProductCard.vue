<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'

const props = defineProps({
    product: Object,
    globalDiscount: { type: Number, default: 0 },
})

const store = useStore()
const quantity = computed(() => store.getters.countByItem(props.product.id))

const discountAmount = computed(() =>
    Math.round((props.product.price * props.globalDiscount) / 100)
)

const finalPrice = computed(() => props.product.price - discountAmount.value)

const addItem = () => store.commit('addToCart', props.product)
const removeItem = () => store.commit('removeItemFromCart', props.product)
</script>

<template>
    <div class="group rounded-xl bg-white shadow-sm ring-1 ring-gray-100 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
        <!-- Product Image -->
        <div class="relative overflow-hidden rounded-t-xl bg-gray-50">
            <img
                v-if="product.image"
                :src="'/storage/' + product.image"
                :alt="product.name"
                class="h-40 w-full object-contain p-2 transition-transform duration-300 group-hover:scale-110"
            />
            <div v-else class="flex h-40 items-center justify-center text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-12 w-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v14.25a1.5 1.5 0 0 0 1.5 1.5Z" />
                </svg>
            </div>

            <!-- Discount badge -->
            <span
                v-if="globalDiscount > 0"
                class="absolute left-2 top-2 rounded-full bg-brand-red px-2 py-0.5 text-xs font-bold text-white"
            >
                -{{ globalDiscount }}%
            </span>
        </div>

        <!-- Product Info -->
        <div class="p-3">
            <h3 class="text-sm font-medium text-brand-dark leading-tight">{{ product.name }}</h3>
            <p v-if="product.tamil_name" class="mt-0.5 text-xs text-gray-400">{{ product.tamil_name }}</p>

            <!-- Pricing -->
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-base font-bold text-green-600">₹{{ finalPrice }}</span>
                <span v-if="globalDiscount > 0" class="text-xs text-gray-400 line-through">₹{{ product.price }}</span>
            </div>

            <!-- Add to cart -->
            <div class="mt-3">
                <button
                    v-if="quantity === 0"
                    @click="addItem"
                    class="w-full rounded-lg bg-brand-red py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-red-hover"
                >
                    Add to Cart
                </button>

                <div v-else class="flex items-center justify-center gap-3">
                    <button
                        @click="removeItem"
                        class="flex h-8 w-8 items-center justify-center rounded-lg border-2 border-brand-red text-sm font-bold text-brand-red transition-colors hover:bg-red-50"
                    >-</button>
                    <span class="w-8 text-center text-base font-bold text-brand-dark">{{ quantity }}</span>
                    <button
                        @click="addItem"
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-red text-sm font-bold text-white transition-colors hover:bg-brand-red-hover"
                    >+</button>
                </div>
            </div>
        </div>
    </div>
</template>
