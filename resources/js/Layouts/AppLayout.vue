<script setup>
import Navbar from '@/Components/partials/Navbar.vue'
import Footer from '@/Components/partials/Footer.vue'
import { computed } from 'vue'
import { useStore } from 'vuex'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
    globalDiscount: { type: Number, default: 0 },
    minOrderValue: { type: Number, default: 0 },
    company_address: String,
    mobile_numbers: Array,
    whatsapp_number: String,
})

defineEmits(['contact-click', 'scroll-to-checkout'])

const store = useStore()
const totalItems = computed(() => store.getters.totalItems)
const totalPrice = computed(() => store.getters.totalPrice)
const discountedTotal = computed(() =>
    Math.round(totalPrice.value - (totalPrice.value * props.globalDiscount) / 100)
)

const scrollToCheckout = () => {
    const el = document.getElementById('checkout-section')
    if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
    } else {
        router.visit('/?scroll=checkout', {
            preserveState: false,
        })
    }
}
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <Navbar :show-cart="true" :show-contact-button="true" @toggle-cart="scrollToCheckout" @contact-click="$emit('contact-click')" />
        <main class="flex-1">
            <slot />
        </main>
        <Footer
            :company_address="company_address"
            :mobile_numbers="mobile_numbers"
        />

        <!-- WhatsApp Chat Icon (fixed bottom-left) -->
        <a
            v-if="whatsapp_number"
            :href="`https://wa.me/91${whatsapp_number}`"
            target="_blank"
            class="animate-bounce-in animate-soft-pulse fixed bottom-6 left-6 z-30 flex h-14 w-14 items-center justify-center rounded-full bg-green-500 shadow-lg transition-transform hover:scale-110"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="h-7 w-7">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
        </a>

        <!-- Floating cart button — scrolls to inline checkout -->
        <button
            v-if="totalItems > 0"
            @click="scrollToCheckout"
            class="animate-bounce-in animate-soft-pulse fixed bottom-24 left-6 z-30 flex items-center gap-2 rounded-full bg-brand-red px-4 py-3 text-white shadow-lg transition-transform hover:scale-105"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121 0 2.09-.773 2.34-1.867l1.86-8.154A.75.75 0 0 0 20.44 3.5H6.456M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
            <span class="text-sm font-bold">₹{{ discountedTotal }}</span>
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white text-xs font-bold text-brand-red">
                {{ totalItems }}
            </span>
        </button>
    </div>
</template>