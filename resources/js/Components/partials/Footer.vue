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
    <footer class="relative bg-gradient-to-b from-brand-maroon to-brand-dark text-gray-200">
        <!-- Amber accent line on top -->
        <div class="h-1 w-full bg-gradient-to-r from-amber-500 via-amber-300 to-amber-500"></div>

        <div class="mx-auto max-w-6xl px-4 py-14">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-3 lg:gap-12">
                <!-- Company Info -->
                <div>
                    <h3 class="mb-4 font-heading text-lg font-bold tracking-wide text-amber-400">
                        Sri Subamangala Crackers
                    </h3>
                    <p class="mb-5 text-sm leading-relaxed text-gray-300">{{ company_address }}</p>
                    <div class="space-y-2.5 text-sm">
                        <a
                            v-if="mobile_number_1"
                            :href="`tel:+91${mobile_number_1}`"
                            class="flex items-center gap-2.5 text-gray-200 transition-colors hover:text-amber-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 text-amber-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                            +91 {{ mobile_number_1 }}
                        </a>
                        <a
                            v-if="mobile_number_2"
                            :href="`tel:+91${mobile_number_2}`"
                            class="flex items-center gap-2.5 text-gray-200 transition-colors hover:text-amber-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 text-amber-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                            +91 {{ mobile_number_2 }}
                        </a>
                        <a
                            v-if="mobile_number_3"
                            :href="`tel:+91${mobile_number_3}`"
                            class="flex items-center gap-2.5 text-gray-200 transition-colors hover:text-amber-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 text-amber-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                            +91 {{ mobile_number_3 }}
                        </a>
                        <a
                            v-if="mobile_number_4"
                            :href="`tel:+91${mobile_number_4}`"
                            class="flex items-center gap-2.5 text-gray-200 transition-colors hover:text-amber-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 text-amber-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                            +91 {{ mobile_number_4 }}
                        </a>
                        <a
                            v-if="mobile_number_5"
                            :href="`tel:+91${mobile_number_5}`"
                            class="flex items-center gap-2.5 text-gray-200 transition-colors hover:text-amber-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 text-amber-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                            +91 {{ mobile_number_5 }}
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="mb-4 font-heading text-lg font-bold tracking-wide text-amber-400">
                        Quick Links
                    </h4>
                    <ul class="space-y-2.5 text-sm">
                        <li>
                            <Link href="/" class="group flex items-center gap-2 text-gray-200 transition-colors hover:text-amber-300">
                                <span class="h-1 w-1 rounded-full bg-amber-400 opacity-0 transition-opacity group-hover:opacity-100"></span>
                                Home
                            </Link>
                        </li>
                        <li>
                            <Link href="/about" class="group flex items-center gap-2 text-gray-200 transition-colors hover:text-amber-300">
                                <span class="h-1 w-1 rounded-full bg-amber-400 opacity-0 transition-opacity group-hover:opacity-100"></span>
                                About Us
                            </Link>
                        </li>
                        <li>
                            <Link href="/faq" class="group flex items-center gap-2 text-gray-200 transition-colors hover:text-amber-300">
                                <span class="h-1 w-1 rounded-full bg-amber-400 opacity-0 transition-opacity group-hover:opacity-100"></span>
                                FAQ
                            </Link>
                        </li>
                        <li>
                            <Link href="/safety-tips" class="group flex items-center gap-2 text-gray-200 transition-colors hover:text-amber-300">
                                <span class="h-1 w-1 rounded-full bg-amber-400 opacity-0 transition-opacity group-hover:opacity-100"></span>
                                Safety Tips
                            </Link>
                        </li>
                        <li>
                            <Link href="/contact" class="group flex items-center gap-2 text-gray-200 transition-colors hover:text-amber-300">
                                <span class="h-1 w-1 rounded-full bg-amber-400 opacity-0 transition-opacity group-hover:opacity-100"></span>
                                Contact
                            </Link>
                        </li>
                        <li>
                            <Link href="/privacy-policy" class="group flex items-center gap-2 text-gray-200 transition-colors hover:text-amber-300">
                                <span class="h-1 w-1 rounded-full bg-amber-400 opacity-0 transition-opacity group-hover:opacity-100"></span>
                                Privacy Policy
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Map -->
                <div>
                    <h4 class="mb-4 font-heading text-lg font-bold tracking-wide text-amber-400">
                        Find Us
                    </h4>
                    <div class="overflow-hidden rounded-xl ring-2 ring-amber-400/30">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3936.568390741373!2d77.81075299999999!3d9.371407!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zOcKwMjInMTcuMSJOIDc3wrA0OCczOC43IkU!5e0!3m2!1sen!2sin!4v1749896362951!5m2!1sen!2sin"
                            class="h-48 w-full"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="border-t border-amber-400/20 bg-black/30 py-5">
            <p class="text-center text-xs text-gray-400">
                &copy; {{ new Date().getFullYear() }}
                <span class="text-gray-300">Sri Subamangala Crackers</span>. All rights reserved.
            </p>
        </div>
    </footer>
</template>