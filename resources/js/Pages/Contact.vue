<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
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
const showContactModal = ref(false)

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
    <Head title="Contact Us - Sri Subamangala Crackers Sivakasi">
        <meta name="description" content="Contact Sri Subamangala Crackers for wholesale crackers orders. Located in Sivakasi, Tamil Nadu. Call or WhatsApp for price list and orders. All India delivery." />
    </Head>

    <AppLayout
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
                <span class="text-brand-dark">Contact</span>
            </nav>

            <h1 class="mb-8 font-heading text-3xl font-bold text-[#800020]" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Contact Us</h1>

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
    </AppLayout>
</template>
