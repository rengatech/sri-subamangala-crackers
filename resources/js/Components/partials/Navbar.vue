<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useStore } from 'vuex'

const props = defineProps({
    showCart: { type: Boolean, default: false },
    showContactButton: { type: Boolean, default: false },
})

const emit = defineEmits(['toggle-cart', 'contact-click'])

const today = new Date()
const todayFormatted = today.toLocaleDateString('en-IN', { day: '2-digit', month: '2-digit', year: 'numeric' })
const validTill = new Date(today.getTime() + 3 * 24 * 60 * 60 * 1000).toLocaleDateString('en-IN', { day: '2-digit', month: '2-digit', year: 'numeric' })

const store = useStore()
const mobileMenuOpen = ref(false)
const totalItems = computed(() => store.getters.totalItems)
const scrolled = ref(false)
const cartBounce = ref(false)

watch(totalItems, (newVal, oldVal) => {
    if (newVal !== oldVal) {
        cartBounce.value = true
        setTimeout(() => { cartBounce.value = false }, 400)
    }
})

const onScroll = () => {
    scrolled.value = window.scrollY > 10
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<template>
    <header :class="['sticky top-0 z-50 bg-white transition-all duration-300', scrolled ? 'shadow-xl' : 'shadow-sm']">
        <div class="mx-auto max-w-6xl px-4">
            <div class="flex h-20 items-center justify-between">
                <!-- Logo -->
                <Link href="/" class="flex shrink-0 items-center gap-2">
                    <img src="/assets/img/sri-mangala-crackers/logo.jpeg" alt="Sri Subamangala Crackers" class="h-14 w-auto sm:h-16" />
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden items-center gap-8 md:flex">
                    <Link
                        v-for="link in [
                            { name: 'Home', href: '/', route: 'home' },
                            { name: 'About', href: '/about', route: 'about' },
                            { name: 'FAQ', href: '/faq', route: 'faq' },
                            { name: 'Safety Tips', href: '/safety-tips', route: 'safety-tips' },
                            { name: 'Contact', href: '/contact', route: 'contact' },
                        ]"
                        :key="link.route"
                        :href="link.href"
                        :class="[
                            'text-base font-bold tracking-wide transition-colors hover:text-brand-red',
                            route().current(link.route) || (link.route === 'home' && route().current('/'))
                                ? 'text-brand-red border-b-2 border-brand-red pb-1'
                                : 'text-brand-dark',
                        ]"
                    >
                        {{ link.name }}
                    </Link>
                </nav>

                <!-- Right side -->
                <div class="flex items-center gap-2 sm:gap-4">
                    <!-- Contact Now button -->
                    <button
                        v-if="showContactButton"
                        @click="$emit('contact-click')"
                        class="flex items-center gap-1.5 rounded-lg border-2 border-brand-red px-2.5 py-1.5 text-xs font-bold text-brand-red transition-all hover:bg-brand-red hover:text-white sm:px-4 sm:text-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                        </svg>
                        <span class="text-[10px] sm:text-sm">Contact</span>
                    </button>

                    <!-- Price List button -->
                    <div class="group relative hidden sm:inline-block">
                        <a
                            :href="route('pricelist')"
                            class="rounded-lg bg-amber-400 px-4 py-2 text-sm font-bold text-brand-dark shadow-md transition-all hover:scale-105 hover:bg-amber-300"
                        >
                            Price List
                        </a>
                        <div class="pointer-events-none absolute left-1/2 top-full z-50 mt-2 -translate-x-1/2 whitespace-nowrap rounded-lg bg-gray-900 px-3 py-1.5 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                            Date: {{ todayFormatted }} | Valid till: {{ validTill }}
                            <div class="absolute -top-1 left-1/2 -translate-x-1/2 border-4 border-transparent border-b-gray-900"></div>
                        </div>
                    </div>

                    <!-- Cart icon -->
                    <button
                        v-if="showCart"
                        @click="$emit('toggle-cart')"
                        :class="['relative rounded-lg p-2 text-brand-dark transition-colors hover:bg-gray-100', cartBounce ? 'animate-cart-wiggle' : '']"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121 0 2.09-.773 2.34-1.867l1.86-8.154A.75.75 0 0 0 20.44 3.5H6.456M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                        <span
                            v-if="totalItems > 0"
                            :class="['absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-brand-red text-xs font-bold text-white transition-transform', cartBounce ? 'scale-125' : 'scale-100']"
                        >
                            {{ totalItems }}
                        </span>
                    </button>

                    <!-- Mobile hamburger -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="rounded-lg p-2 text-brand-dark hover:bg-gray-100 md:hidden"
                    >
                        <svg v-if="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu overlay -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="mobileMenuOpen" class="fixed inset-0 z-40 bg-black/60 md:hidden" @click="mobileMenuOpen = false" />
        </Transition>

        <!-- Mobile menu panel -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="-translate-y-full opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="-translate-y-full opacity-0"
        >
            <div v-if="mobileMenuOpen" class="absolute left-0 right-0 top-20 z-50 bg-white shadow-xl md:hidden border-t border-gray-100">
                <div class="space-y-2 px-4 pb-6 pt-4">
                    <Link
                        v-for="link in [
                            { name: 'Home', href: '/', route: 'home' },
                            { name: 'About', href: '/about', route: 'about' },
                            { name: 'FAQ', href: '/faq', route: 'faq' },
                            { name: 'Safety Tips', href: '/safety-tips', route: 'safety-tips' },
                            { name: 'Contact', href: '/contact', route: 'contact' },
                        ]"
                        :key="link.route"
                        :href="link.href"
                        @click="mobileMenuOpen = false"
                        :class="[
                            'block rounded-lg px-4 py-3 text-base font-bold transition-colors',
                            route().current(link.route) || (link.route === 'home' && route().current('/'))
                                ? 'bg-red-50 text-brand-red'
                                : 'text-brand-dark hover:bg-gray-50',
                        ]"
                    >
                        {{ link.name }}
                    </Link>
                    <div class="pt-4">
                        <a
                            :href="route('pricelist')"
                            class="block rounded-lg bg-amber-400 px-4 py-3 text-center text-base font-bold text-brand-dark hover:bg-amber-300 shadow-md"
                        >
                            Download Price List
                            <span class="block text-xs font-medium opacity-80 mt-1">Valid till: {{ validTill }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </Transition>
    </header>
</template>