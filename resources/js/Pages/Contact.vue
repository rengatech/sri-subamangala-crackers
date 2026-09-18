<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    mobile_numbers: Array,
    company_address: String,
    whatsapp_number: String,
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
                                    <p v-for="(number, idx) in mobile_numbers" :key="idx"><a :href="`tel:+91${number}`" class="hover:text-brand-red">+91 {{ number }}</a></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp card -->
                    <div v-if="whatsapp_number" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-start gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-heading text-sm font-semibold text-brand-dark">WhatsApp Us</h3>
                                <div class="mt-1 text-sm text-brand-gray">
                                    <a
                                        :href="`https://wa.me/91${whatsapp_number}`"
                                        target="_blank"
                                        class="hover:text-brand-red"
                                    >
                                        +91 {{ whatsapp_number }}
                                    </a>
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