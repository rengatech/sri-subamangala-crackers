# Design Overhaul Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Rebuild all user-facing pages (Home, Checkout, About, Contact, Privacy Policy) with Tailwind CSS, dropping Bootstrap and jQuery, while preserving the existing color palette and all functionality.

**Architecture:** Replace Bootstrap + jQuery + template CSS with Tailwind CSS utility classes. Unify two navbar/layout variants into one. Extract checkout into a separate page. Keep Vuex store and Inertia.js routing intact. All Vue components rewritten with Tailwind — no `style.css` dependency.

**Tech Stack:** Vue 3, Inertia.js, Tailwind CSS 3, Vuex 4, Heroicons (via inline SVG), Vite

---

## Task 1: Tailwind CSS Configuration & Cleanup

**Files:**
- Modify: `tailwind.config.js`
- Modify: `resources/css/app.css`
- Modify: `resources/views/app.blade.php`
- Modify: `package.json`

**Step 1: Update `tailwind.config.js` with custom colors and scan paths**

```js
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.vue',
        './resources/**/*.js',
        './vendor/filament/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                brand: {
                    red: '#e03a3c',
                    'red-hover': '#c8292b',
                    maroon: '#991b1b',
                    dark: '#111111',
                    gray: '#444444',
                    light: '#f8f9fa',
                },
                danger: require('tailwindcss/colors').rose,
                primary: require('tailwindcss/colors').red,
                success: require('tailwindcss/colors').red,
                warning: require('tailwindcss/colors').yellow,
            },
            fontFamily: {
                sans: ['Open Sans', ...defaultTheme.fontFamily.sans],
                heading: ['Raleway', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
```

**Step 2: Update `resources/css/app.css`**

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Raleway:wght@300;400;500;600;700&display=swap');
```

Remove the `@import './invoice3.css';` line (invoice CSS stays separate — it's only for PDF rendering, not user-facing pages).

**Step 3: Strip all vendor CSS/JS from `resources/views/app.blade.php`**

Replace the entire file with:

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sri Subamangala Crackers - Best crackers @ affordable price</title>
    <link href="/assets/img/MADHU.svg" rel="icon">
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-brand-light text-brand-gray">
    @inertia
</body>
</html>
```

This removes: Bootstrap CSS/JS, AOS, Swiper, Boxicons, Remix icons, FontAwesome, Magnific Popup, jQuery, main.js, purecounter, isotope, php-email-form validate. All gone.

**Step 4: Verify build works**

Run: `npm run build`
Expected: Vite compiles successfully. The app will look broken (no Bootstrap classes) — that's expected.

**Step 5: Commit**

```bash
git add tailwind.config.js resources/css/app.css resources/views/app.blade.php
git commit -m "chore: configure Tailwind CSS, remove Bootstrap and jQuery vendor deps"
```

---

## Task 2: Unified Navbar Component

**Files:**
- Rewrite: `resources/js/Components/partials/Navbar.vue` (this becomes the single navbar)
- Delete: `resources/js/Components/partials/Navbar1.vue` (no longer needed)

**Step 1: Rewrite `Navbar.vue`**

```vue
<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useStore } from 'vuex'

const props = defineProps({
    showCart: { type: Boolean, default: false },
})

const emit = defineEmits(['toggle-cart'])

const store = useStore()
const mobileMenuOpen = ref(false)
const totalItems = computed(() => store.getters.totalItems)
</script>

<template>
    <header class="sticky top-0 z-50 bg-white shadow-sm transition-shadow duration-300">
        <div class="mx-auto max-w-6xl px-4">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-2">
                    <img src="/assets/img/MADHU.svg" alt="Sri Subamangala Crackers" class="h-10 w-auto" />
                    <img src="/assets/img/sri-mangala-crackers/logo.jpeg" alt="" class="hidden h-10 w-auto sm:block" />
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden items-center gap-8 md:flex">
                    <Link
                        v-for="link in [
                            { name: 'Home', href: '/', route: 'home' },
                            { name: 'About', href: '/about', route: 'about' },
                            { name: 'Contact', href: '/contact', route: 'contact' },
                        ]"
                        :key="link.route"
                        :href="link.href"
                        :class="[
                            'text-sm font-medium transition-colors hover:text-brand-red',
                            route().current(link.route) || (link.route === 'home' && route().current('/'))
                                ? 'text-brand-red'
                                : 'text-brand-dark',
                        ]"
                    >
                        {{ link.name }}
                    </Link>
                </nav>

                <!-- Right side -->
                <div class="flex items-center gap-3">
                    <!-- Price List button -->
                    <a
                        :href="route('pricelist')"
                        class="hidden rounded-lg border-2 border-brand-red px-4 py-1.5 text-sm font-semibold text-brand-red transition-colors hover:bg-brand-red hover:text-white sm:inline-block"
                    >
                        Price List
                    </a>

                    <!-- Cart icon (only on shopping pages) -->
                    <button
                        v-if="showCart"
                        @click="$emit('toggle-cart')"
                        class="relative rounded-lg p-2 text-brand-dark transition-colors hover:bg-gray-100"
                    >
                        <!-- Heroicon: shopping-cart -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121 0 2.09-.773 2.34-1.867l1.86-8.154A.75.75 0 0 0 20.44 3.5H6.456M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                        <span
                            v-if="totalItems > 0"
                            class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-brand-red text-xs font-bold text-white"
                        >
                            {{ totalItems }}
                        </span>
                    </button>

                    <!-- Mobile hamburger -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="rounded-lg p-2 text-brand-dark md:hidden"
                    >
                        <svg v-if="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
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
            <div v-if="mobileMenuOpen" class="fixed inset-0 z-40 bg-black/50 md:hidden" @click="mobileMenuOpen = false" />
        </Transition>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="-translate-y-full opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="-translate-y-full opacity-0"
        >
            <div v-if="mobileMenuOpen" class="absolute left-0 right-0 top-16 z-50 bg-white shadow-lg md:hidden">
                <div class="space-y-1 px-4 pb-4 pt-2">
                    <Link
                        v-for="link in [
                            { name: 'Home', href: '/', route: 'home' },
                            { name: 'About', href: '/about', route: 'about' },
                            { name: 'Contact', href: '/contact', route: 'contact' },
                        ]"
                        :key="link.route"
                        :href="link.href"
                        @click="mobileMenuOpen = false"
                        :class="[
                            'block rounded-lg px-3 py-2 text-base font-medium transition-colors',
                            route().current(link.route) || (link.route === 'home' && route().current('/'))
                                ? 'bg-red-50 text-brand-red'
                                : 'text-brand-dark hover:bg-gray-50',
                        ]"
                    >
                        {{ link.name }}
                    </Link>
                    <a
                        :href="route('pricelist')"
                        class="block rounded-lg bg-brand-red px-3 py-2 text-center text-base font-semibold text-white"
                    >
                        Download Price List
                    </a>
                </div>
            </div>
        </Transition>
    </header>
</template>
```

**Step 2: Delete `Navbar1.vue`**

Delete the file `resources/js/Components/partials/Navbar1.vue`.

**Step 3: Commit**

```bash
git add resources/js/Components/partials/Navbar.vue
git rm resources/js/Components/partials/Navbar1.vue
git commit -m "feat: unified Navbar component with Tailwind CSS"
```

---

## Task 3: Unified Footer Component

**Files:**
- Rewrite: `resources/js/Components/partials/Footer.vue`

**Step 1: Rewrite `Footer.vue`**

```vue
<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    company_address: String,
    mobile_number_1: Number,
    mobile_number_2: Number,
    mobile_number_3: Number,
    mobile_number_4: Number,
    mobile_number_5: Number,
})
</script>

<template>
    <footer class="bg-brand-dark text-gray-300">
        <div class="mx-auto max-w-6xl px-4 py-12">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Company Info -->
                <div>
                    <h3 class="mb-4 font-heading text-lg font-semibold text-white">
                        Sri Subamangala Crackers
                    </h3>
                    <p class="mb-4 text-sm leading-relaxed">{{ company_address }}</p>
                    <div class="space-y-1 text-sm">
                        <p v-if="mobile_number_1"><a :href="`tel:+91${mobile_number_1}`" class="hover:text-white transition-colors">+91 {{ mobile_number_1 }}</a></p>
                        <p v-if="mobile_number_2"><a :href="`tel:+91${mobile_number_2}`" class="hover:text-white transition-colors">+91 {{ mobile_number_2 }}</a></p>
                        <p v-if="mobile_number_3"><a :href="`tel:+91${mobile_number_3}`" class="hover:text-white transition-colors">+91 {{ mobile_number_3 }}</a></p>
                        <p v-if="mobile_number_4"><a :href="`tel:+91${mobile_number_4}`" class="hover:text-white transition-colors">+91 {{ mobile_number_4 }}</a></p>
                        <p v-if="mobile_number_5"><a :href="`tel:+91${mobile_number_5}`" class="hover:text-white transition-colors">+91 {{ mobile_number_5 }}</a></p>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="mb-4 font-heading text-lg font-semibold text-white">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><Link href="/" class="hover:text-white transition-colors">Home</Link></li>
                        <li><Link href="/about" class="hover:text-white transition-colors">About Us</Link></li>
                        <li><Link href="/contact" class="hover:text-white transition-colors">Contact</Link></li>
                        <li><Link href="/privacy-policy" class="hover:text-white transition-colors">Privacy Policy</Link></li>
                    </ul>
                </div>

                <!-- Map -->
                <div>
                    <h4 class="mb-4 font-heading text-lg font-semibold text-white">Find Us</h4>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3936.568390741373!2d77.81075299999999!3d9.371407!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zOcKwMjInMTcuMSJOIDc3wrA0OCczOC43IkU!5e0!3m2!1sen!2sin!4v1749896362951!5m2!1sen!2sin"
                        class="h-48 w-full rounded-lg"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="border-t border-gray-700 py-4">
            <p class="text-center text-xs text-gray-500">
                &copy; {{ new Date().getFullYear() }} Sri Subamangala Crackers. All rights reserved.
            </p>
        </div>
    </footer>
</template>
```

**Step 2: Commit**

```bash
git add resources/js/Components/partials/Footer.vue
git commit -m "feat: rewrite Footer with Tailwind CSS"
```

---

## Task 4: Unified Layouts

**Files:**
- Rewrite: `resources/js/Layouts/HomeLayout.vue`
- Rewrite: `resources/js/Layouts/OtherLayout.vue`

**Step 1: Rewrite `HomeLayout.vue`**

```vue
<script setup>
import Navbar from '@/Components/partials/Navbar.vue'
import Footer from '@/Components/partials/Footer.vue'

defineProps({
    company_address: String,
    mobile_number_1: Number,
    mobile_number_2: Number,
    mobile_number_3: Number,
    mobile_number_4: Number,
    mobile_number_5: Number,
})

const emit = defineEmits(['toggle-cart'])
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <Navbar :show-cart="true" @toggle-cart="$emit('toggle-cart')" />
        <main class="flex-1">
            <slot />
        </main>
        <Footer
            :company_address="company_address"
            :mobile_number_1="mobile_number_1"
            :mobile_number_2="mobile_number_2"
            :mobile_number_3="mobile_number_3"
            :mobile_number_4="mobile_number_4"
            :mobile_number_5="mobile_number_5"
        />
    </div>
</template>
```

**Step 2: Rewrite `OtherLayout.vue`**

```vue
<script setup>
import Navbar from '@/Components/partials/Navbar.vue'
import Footer from '@/Components/partials/Footer.vue'

defineProps({
    company_address: String,
    mobile_number_1: Number,
    mobile_number_2: Number,
    mobile_number_3: Number,
    mobile_number_4: Number,
    mobile_number_5: Number,
})
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <Navbar />
        <main class="flex-1">
            <slot />
        </main>
        <Footer
            :company_address="company_address"
            :mobile_number_1="mobile_number_1"
            :mobile_number_2="mobile_number_2"
            :mobile_number_3="mobile_number_3"
            :mobile_number_4="mobile_number_4"
            :mobile_number_5="mobile_number_5"
        />
    </div>
</template>
```

**Step 3: Commit**

```bash
git add resources/js/Layouts/HomeLayout.vue resources/js/Layouts/OtherLayout.vue
git commit -m "feat: rewrite layouts with Tailwind, unified navbar"
```

---

## Task 5: Cart Drawer Component

**Files:**
- Create: `resources/js/Components/CartDrawer.vue`

**Step 1: Create `CartDrawer.vue`**

```vue
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

const canCheckout = computed(() => discountedTotal.value >= props.minOrderValue)

const addItem = (item) => store.commit('addToCart', item)
const removeItem = (item) => store.commit('removeItemFromCart', item)
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
                        class="flex items-center gap-3 rounded-lg border border-gray-100 p-3"
                    >
                        <img
                            v-if="item.image"
                            :src="'/storage/' + item.image"
                            :alt="item.name"
                            class="h-14 w-14 rounded-lg object-cover"
                        />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-brand-dark">{{ item.name }}</p>
                            <p class="text-xs text-gray-400">
                                ₹{{ item.price }} x {{ item.quantity }}
                            </p>
                        </div>
                        <div class="flex items-center gap-1">
                            <button
                                @click="removeItem(item)"
                                class="flex h-7 w-7 items-center justify-center rounded-md border text-sm font-bold text-gray-500 hover:bg-gray-50"
                            >-</button>
                            <span class="w-8 text-center text-sm font-semibold">{{ item.quantity }}</span>
                            <button
                                @click="addItem(item)"
                                class="flex h-7 w-7 items-center justify-center rounded-md border text-sm font-bold text-gray-500 hover:bg-gray-50"
                            >+</button>
                        </div>
                        <p class="w-16 text-right text-sm font-semibold text-brand-dark">
                            ₹{{ item.price * item.quantity }}
                        </p>
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
```

**Step 2: Commit**

```bash
git add resources/js/Components/CartDrawer.vue
git commit -m "feat: add CartDrawer slide-out component"
```

---

## Task 6: Product Card Component

**Files:**
- Create: `resources/js/Components/ProductCard.vue`

**Step 1: Create `ProductCard.vue`**

```vue
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
```

**Step 2: Commit**

```bash
git add resources/js/Components/ProductCard.vue
git commit -m "feat: add ProductCard component with Tailwind"
```

---

## Task 7: Home Page Rewrite

**Files:**
- Rewrite: `resources/js/Pages/Home.vue`

**Step 1: Rewrite `Home.vue`**

```vue
<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'
import CartDrawer from '@/Components/CartDrawer.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useStore } from 'vuex'
import { pick } from 'lodash'

const props = defineProps({
    status: String,
    products: Object,
    categories: Object,
    orders: Object,
    global_discount: Number,
    starting_year: Number,
    min_order_value: Number,
    mobile_number_1: Number,
    mobile_number_2: Number,
    mobile_number_3: Number,
    mobile_number_4: Number,
    mobile_number_5: Number,
    marquee_content: Object,
    bank_details: Object,
    company_address: String,
})

const store = useStore()
const cartOpen = ref(false)
const activeCategory = ref(null)
const showAnnouncement = ref(true)

// Set first category as active on load
if (props.categories && props.categories.length > 0) {
    activeCategory.value = props.categories[0].id
}

const filteredProducts = computed(() => {
    if (!activeCategory.value || !props.categories) return []
    const cat = props.categories.find(c => c.id === activeCategory.value)
    return cat ? cat.products : []
})

const totalPrice = computed(() => store.getters.totalPrice)
const totalItems = computed(() => store.getters.totalItems)

const discountedTotal = computed(() =>
    Math.round(totalPrice.value - (totalPrice.value * props.global_discount) / 100)
)
</script>

<template>
    <Head title="Home" />

    <HomeLayout
        :company_address="company_address"
        :mobile_number_1="mobile_number_1"
        :mobile_number_2="mobile_number_2"
        :mobile_number_3="mobile_number_3"
        :mobile_number_4="mobile_number_4"
        :mobile_number_5="mobile_number_5"
        @toggle-cart="cartOpen = !cartOpen"
    >
        <!-- Announcement Bar -->
        <div
            v-if="showAnnouncement && marquee_content"
            class="bg-brand-maroon text-white"
        >
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-2">
                <p class="flex-1 text-center text-sm font-medium">
                    {{ marquee_content }}
                    <span v-if="global_discount" class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs font-bold">
                        {{ global_discount }}% OFF
                    </span>
                </p>
                <button @click="showAnnouncement = false" class="ml-2 text-white/70 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Category Tabs (sticky below navbar) -->
        <div class="sticky top-16 z-30 border-b bg-white shadow-sm">
            <div class="mx-auto max-w-6xl px-4">
                <div class="flex gap-1 overflow-x-auto py-2 scrollbar-hide">
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="activeCategory = category.id"
                        :class="[
                            'shrink-0 rounded-full px-4 py-2 text-sm font-medium transition-colors whitespace-nowrap',
                            activeCategory === category.id
                                ? 'bg-brand-red text-white'
                                : 'bg-gray-100 text-brand-gray hover:bg-gray-200',
                        ]"
                    >
                        {{ category.category }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="mx-auto max-w-6xl px-4 py-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <ProductCard
                    v-for="product in filteredProducts"
                    :key="product.id"
                    :product="product"
                    :global-discount="global_discount"
                />
            </div>

            <div v-if="filteredProducts.length === 0" class="py-20 text-center text-gray-400">
                <p class="text-lg font-medium">No products in this category</p>
            </div>
        </div>

        <!-- Bank Details Section -->
        <div v-if="bank_details && bank_details.length > 0" class="bg-white py-10">
            <div class="mx-auto max-w-6xl px-4">
                <h2 class="mb-6 text-center font-heading text-2xl font-bold text-brand-dark">
                    Bank Details
                </h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="bank in bank_details"
                        :key="bank.id"
                        class="rounded-xl bg-brand-maroon p-5 text-white"
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

        <!-- Floating cart button (mobile) -->
        <button
            v-if="totalItems > 0"
            @click="cartOpen = true"
            class="fixed bottom-6 right-6 z-30 flex items-center gap-2 rounded-full bg-brand-red px-5 py-3 text-white shadow-lg transition-transform hover:scale-105 md:hidden"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121 0 2.09-.773 2.34-1.867l1.86-8.154A.75.75 0 0 0 20.44 3.5H6.456M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
            <span class="text-sm font-bold">₹{{ discountedTotal }}</span>
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white text-xs font-bold text-brand-red">
                {{ totalItems }}
            </span>
        </button>

        <!-- Cart Drawer -->
        <CartDrawer
            :open="cartOpen"
            :global-discount="global_discount"
            :min-order-value="min_order_value"
            @close="cartOpen = false"
        />
    </HomeLayout>
</template>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
```

**Step 2: Commit**

```bash
git add resources/js/Pages/Home.vue
git commit -m "feat: rewrite Home page with Tailwind, category tabs, cart drawer"
```

---

## Task 8: Checkout Page

**Files:**
- Create: `resources/js/Pages/Checkout.vue`
- Modify: `routes/web.php` (add checkout route)
- Modify: `app/Http/Controllers/OrderController.php` (if needed for checkout data)

**Step 1: Create `Checkout.vue`**

```vue
<script setup>
import OtherLayout from '@/Layouts/OtherLayout.vue'
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

const orderItems = computed(() => {
    return store.getters.getOrderItems.map(item => pick(item, ['id', 'quantity']))
})

const sameAsMobile = ref(false)

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

const submitOrder = () => {
    form.order_items = orderItems
    form.post(route('orders.store'), {
        onSuccess: () => store.commit('clearCart'),
    })
}
</script>

<template>
    <Head title="Checkout" />

    <OtherLayout
        :company_address="company_address"
        :mobile_number_1="mobile_number_1"
        :mobile_number_2="mobile_number_2"
        :mobile_number_3="mobile_number_3"
        :mobile_number_4="mobile_number_4"
        :mobile_number_5="mobile_number_5"
    >
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
                <!-- Order Summary (left, 3 cols) -->
                <div class="lg:col-span-3">
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                        <h2 class="mb-4 font-heading text-lg font-semibold text-brand-dark">Order Summary</h2>

                        <div class="divide-y">
                            <div
                                v-for="item in cartItems"
                                :key="item.id"
                                class="flex items-center gap-4 py-3"
                            >
                                <img
                                    v-if="item.image"
                                    :src="'/storage/' + item.image"
                                    :alt="item.name"
                                    class="h-14 w-14 rounded-lg object-cover"
                                />
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-brand-dark">{{ item.name }}</p>
                                    <p class="text-xs text-gray-400">₹{{ item.price }} each</p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button
                                        type="button"
                                        @click="removeItem(item)"
                                        class="flex h-7 w-7 items-center justify-center rounded border text-sm text-gray-500 hover:bg-gray-50"
                                    >-</button>
                                    <span class="w-8 text-center text-sm font-semibold">{{ item.quantity }}</span>
                                    <button
                                        type="button"
                                        @click="addItem(item)"
                                        class="flex h-7 w-7 items-center justify-center rounded border text-sm text-gray-500 hover:bg-gray-50"
                                    >+</button>
                                </div>
                                <p class="w-20 text-right text-sm font-semibold">₹{{ item.price * item.quantity }}</p>
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

                <!-- Customer Details (right, 2 cols) -->
                <div class="lg:col-span-2">
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
                            :disabled="!canSubmit || !isMobileValid || !isWhatsAppValid || form.processing"
                            :class="[
                                'mt-6 w-full rounded-lg py-3 text-sm font-semibold text-white transition-colors',
                                canSubmit && isMobileValid && isWhatsAppValid && !form.processing
                                    ? 'bg-brand-red hover:bg-brand-red-hover'
                                    : 'bg-gray-300 cursor-not-allowed',
                            ]"
                        >
                            <span v-if="form.processing">Placing Order...</span>
                            <span v-else>Place Order — ₹{{ discountedTotal }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </OtherLayout>
</template>
```

**Step 2: Add checkout route to `routes/web.php`**

Add this route after the `home` route (around line 58):

```php
Route::get('/checkout', function (GeneralSettings $settings) {
    return Inertia::render('Checkout', [
        'global_discount' => $settings->global_discount,
        'min_order_value' => $settings->min_order_value,
        'mobile_number_1' => $settings->mobile_number_1,
        'mobile_number_2' => $settings->mobile_number_2,
        'mobile_number_3' => $settings->mobile_number_3,
        'mobile_number_4' => $settings->mobile_number_4,
        'mobile_number_5' => $settings->mobile_number_5,
        'company_address' => $settings->company_address,
        'bank_details' => BankAccount::all(),
    ]);
})->name('checkout');
```

**Step 3: Commit**

```bash
git add resources/js/Pages/Checkout.vue routes/web.php
git commit -m "feat: add Checkout page with Tailwind"
```

---

## Task 9: About Page Rewrite

**Files:**
- Rewrite: `resources/js/Pages/About.vue`

**Step 1: Rewrite `About.vue`**

```vue
<script setup>
import OtherLayout from '@/Layouts/OtherLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    about_page: Object,
    company_address: String,
    mobile_number_1: Number,
    mobile_number_2: Number,
    mobile_number_3: Number,
    mobile_number_4: Number,
    mobile_number_5: Number,
})

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
    <Head title="About Us" />

    <OtherLayout
        :company_address="company_address"
        :mobile_number_1="mobile_number_1"
        :mobile_number_2="mobile_number_2"
        :mobile_number_3="mobile_number_3"
        :mobile_number_4="mobile_number_4"
        :mobile_number_5="mobile_number_5"
    >
        <div class="mx-auto max-w-6xl px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm text-gray-400">
                <a href="/" class="hover:text-brand-red">Home</a>
                <span class="mx-2">/</span>
                <span class="text-brand-dark">About Us</span>
            </nav>

            <!-- About Section -->
            <div v-for="about in about_page" :key="about.id" class="mb-12">
                <div class="grid gap-8 lg:grid-cols-2">
                    <!-- Text -->
                    <div>
                        <h1 class="mb-4 font-heading text-3xl font-bold text-brand-dark">About Us</h1>
                        <p class="leading-relaxed text-brand-gray">{{ about.about }}</p>
                    </div>

                    <!-- Image -->
                    <div class="overflow-hidden rounded-xl">
                        <img src="/assets/img/page-3.jpeg" alt="Sri Subamangala Crackers" class="h-full w-full object-cover" />
                    </div>
                </div>

                <!-- Feature Cards -->
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="feature in features"
                        :key="feature.key"
                        class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 transition-all hover:shadow-md hover:-translate-y-0.5"
                    >
                        <!-- Icon -->
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-brand-red">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path v-if="feature.icon === 'receipt'" stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185Z" />
                                <path v-if="feature.icon === 'cube'" stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                <path v-if="feature.icon === 'shield-check'" stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                <path v-if="feature.icon === 'shield'" stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <h3 class="mb-1 font-heading text-base font-semibold text-brand-dark">{{ feature.title }}</h3>
                        <p class="text-sm text-brand-gray">{{ about[feature.key] }}</p>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="mb-8">
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
    </OtherLayout>
</template>
```

**Step 2: Commit**

```bash
git add resources/js/Pages/About.vue
git commit -m "feat: rewrite About page with Tailwind"
```

---

## Task 10: Contact Page Rewrite

**Files:**
- Rewrite: `resources/js/Pages/Contact.vue`

**Step 1: Rewrite `Contact.vue`**

```vue
<script setup>
import OtherLayout from '@/Layouts/OtherLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    mobile_number_1: Number,
    mobile_number_2: Number,
    mobile_number_3: Number,
    mobile_number_4: Number,
    mobile_number_5: Number,
    company_address: String,
})

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
})

const submitted = ref(false)

const submitForm = () => {
    form.post(route('contact-form'), {
        onSuccess: () => {
            submitted.value = true
            form.reset()
        },
    })
}
</script>

<template>
    <Head title="Contact Us" />

    <OtherLayout
        :company_address="company_address"
        :mobile_number_1="mobile_number_1"
        :mobile_number_2="mobile_number_2"
        :mobile_number_3="mobile_number_3"
        :mobile_number_4="mobile_number_4"
        :mobile_number_5="mobile_number_5"
    >
        <div class="mx-auto max-w-6xl px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm text-gray-400">
                <a href="/" class="hover:text-brand-red">Home</a>
                <span class="mx-2">/</span>
                <span class="text-brand-dark">Contact</span>
            </nav>

            <h1 class="mb-8 font-heading text-3xl font-bold text-brand-dark">Contact Us</h1>

            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Contact Info -->
                <div class="space-y-4">
                    <!-- Address card -->
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-start gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-brand-red">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-heading text-sm font-semibold text-brand-dark">Our Address</h3>
                                <p class="mt-1 text-sm text-brand-gray">{{ company_address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Phone card -->
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-start gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-brand-red">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-heading text-sm font-semibold text-brand-dark">Call Us</h3>
                                <div class="mt-1 space-y-0.5 text-sm text-brand-gray">
                                    <p v-if="mobile_number_1"><a :href="`tel:+91${mobile_number_1}`" class="hover:text-brand-red">+91 {{ mobile_number_1 }}</a></p>
                                    <p v-if="mobile_number_2"><a :href="`tel:+91${mobile_number_2}`" class="hover:text-brand-red">+91 {{ mobile_number_2 }}</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div v-if="submitted" class="py-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto mb-3 h-12 w-12 text-green-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <p class="text-lg font-medium text-brand-dark">Message sent!</p>
                        <p class="mt-1 text-sm text-gray-400">We'll get back to you soon.</p>
                        <button @click="submitted = false" class="mt-4 text-sm text-brand-red hover:underline">
                            Send another message
                        </button>
                    </div>

                    <form v-else @submit.prevent="submitForm" class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-brand-gray">Name</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red"
                                    placeholder="Your name"
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-brand-gray">Email</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red"
                                    placeholder="Your email"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-brand-gray">Subject</label>
                            <input
                                v-model="form.subject"
                                type="text"
                                required
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red"
                                placeholder="Subject"
                            />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-brand-gray">Message</label>
                            <textarea
                                v-model="form.message"
                                rows="5"
                                required
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red"
                                placeholder="Your message..."
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded-lg bg-brand-red py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-red-hover disabled:bg-gray-300"
                        >
                            {{ form.processing ? 'Sending...' : 'Send Message' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Google Maps -->
            <div class="mt-8">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3936.5523131108343!2d77.7834678!3d9.3728248!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3b06c92e0ce589db%3A0xedbb891d1f71d3a4!2sMadhu%20Crackers!5e0!3m2!1sen!2sin!4v1692119224081!5m2!1sen!2sin"
                    class="h-80 w-full rounded-xl"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </div>
    </OtherLayout>
</template>
```

**Step 2: Commit**

```bash
git add resources/js/Pages/Contact.vue
git commit -m "feat: rewrite Contact page with Tailwind"
```

---

## Task 11: Privacy Policy Page Rewrite

**Files:**
- Rewrite: `resources/js/Pages/PrivacyPolicy.vue`

**Step 1: Rewrite `PrivacyPolicy.vue`**

```vue
<script setup>
import OtherLayout from '@/Layouts/OtherLayout.vue'
import { Head } from '@inertiajs/vue3'

defineProps({
    company_address: String,
    mobile_number_1: Number,
    mobile_number_2: Number,
    mobile_number_3: Number,
    mobile_number_4: Number,
    mobile_number_5: Number,
})
</script>

<template>
    <Head title="Privacy Policy" />

    <OtherLayout
        :company_address="company_address"
        :mobile_number_1="mobile_number_1"
        :mobile_number_2="mobile_number_2"
        :mobile_number_3="mobile_number_3"
        :mobile_number_4="mobile_number_4"
        :mobile_number_5="mobile_number_5"
    >
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
                    Phone: {{ mobile_number_1 }}<br>
                    Address: {{ company_address }}
                </p>
            </div>
        </div>
    </OtherLayout>
</template>
```

**Step 2: Commit**

```bash
git add resources/js/Pages/PrivacyPolicy.vue
git commit -m "feat: rewrite Privacy Policy page with Tailwind"
```

---

## Task 12: Route Cleanup & Remove Dead Pages

**Files:**
- Modify: `routes/web.php` (remove Collection, Portfolio, Orderlist1 routes)
- Delete: `resources/js/Pages/Collection.vue`
- Delete: `resources/js/Pages/Portfolio.vue`
- Delete: `resources/js/Pages/Orderlist1.vue` (if exists)
- Delete: `resources/js/Pages/Home2.vue` (unused duplicate)

**Step 1: Remove routes from `routes/web.php`**

Delete these route blocks:

```php
// DELETE these:
Route::get('/collection', function () { ... })->name('collection');
Route::get('/portfolio', function () { ... })->name('portfolio');
Route::get('/orderlist1', function () { ... })->name('orderlist1');
```

Also remove the first duplicate `/` route (lines 32-34):
```php
// DELETE this (duplicate of 'home' route):
Route::get('/', function () {
    return Inertia::render('Home');
})->name('/');
```

**Step 2: Delete unused Vue pages**

```bash
git rm resources/js/Pages/Collection.vue
git rm resources/js/Pages/Portfolio.vue
git rm resources/js/Pages/Orderlist1.vue 2>/dev/null || true
git rm resources/js/Pages/Home2.vue 2>/dev/null || true
```

**Step 3: Commit**

```bash
git add routes/web.php
git commit -m "chore: remove Collection, Portfolio, dead pages and duplicate routes"
```

---

## Task 13: Update `app.js` Store Import

**Files:**
- Modify: `resources/js/app.js` (verify store import path is correct)

**Step 1: Check and fix store import**

The current import is `import {store} from './store'` which points to `resources/js/store.js`. This is correct and needs no change.

**Step 2: Remove `lodash` from Home.vue if no longer needed**

The new Home.vue still uses `lodash/pick` in Checkout.vue but NOT in Home.vue. The Home.vue import of `pick` from lodash can be removed since the cart drawer handles display only.

This is already handled in the Task 7 rewrite — Home.vue no longer imports lodash.

**Step 3: Commit (only if changes were made)**

No commit needed if no changes.

---

## Task 14: Final Build & Smoke Test

**Step 1: Install any missing deps (if needed)**

Run: `npm install`
Expected: No new deps needed — Tailwind, forms plugin, typography plugin are already in package.json.

**Step 2: Build for production**

Run: `npm run build`
Expected: Vite compiles successfully with no errors.

**Step 3: Manual smoke test checklist**

Run: `php artisan serve`

Test each page:
- [ ] `/` — Home loads, category tabs work, products display, add-to-cart works, cart drawer opens, floating mobile cart button shows
- [ ] `/checkout` — Shows cart items, form validates, order submits
- [ ] `/about` — About content shows, feature cards display, FAQ accordion works
- [ ] `/contact` — Contact info shows, form submits, Google Maps loads
- [ ] `/privacy-policy` — Content renders with proper typography
- [ ] Mobile: Hamburger menu works, cart floating button works
- [ ] Desktop: Navbar shows all links, cart icon has badge
- [ ] Price list download still works

**Step 4: Final commit**

```bash
git add -A
git commit -m "feat: complete user-facing design overhaul with Tailwind CSS"
```

---

## Summary of Files

### New Files (3)
- `resources/js/Components/CartDrawer.vue`
- `resources/js/Components/ProductCard.vue`
- `resources/js/Pages/Checkout.vue`

### Rewritten Files (9)
- `tailwind.config.js`
- `resources/css/app.css`
- `resources/views/app.blade.php`
- `resources/js/Components/partials/Navbar.vue`
- `resources/js/Components/partials/Footer.vue`
- `resources/js/Layouts/HomeLayout.vue`
- `resources/js/Layouts/OtherLayout.vue`
- `resources/js/Pages/Home.vue`
- `resources/js/Pages/About.vue`
- `resources/js/Pages/Contact.vue`
- `resources/js/Pages/PrivacyPolicy.vue`

### Deleted Files (5)
- `resources/js/Components/partials/Navbar1.vue`
- `resources/js/Pages/Collection.vue`
- `resources/js/Pages/Portfolio.vue`
- `resources/js/Pages/Orderlist1.vue`
- `resources/js/Pages/Home2.vue`

### Modified Files (1)
- `routes/web.php` (add checkout route, remove dead routes)
