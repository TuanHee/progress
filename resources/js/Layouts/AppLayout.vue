<template>
    <div>
        <Head :title="title" />

        <jet-banner />

        <div class="min-h-screen bg-gray-100">
            <application-nav-bar />

            <!-- Page Heading -->
            <header v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header"></slot>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <flash-messages />
                <slot></slot>
            </main>
        </div>
    </div>
</template>

<script>
    import JetBanner from '@/Jetstream/Banner.vue'
    import ApplicationNavBar from '@/Shared/ApplicationNavBar'
    import FlashMessages from '@/Shared/FlashMessages.vue'
    import { Head } from '@inertiajs/inertia-vue3'

    export default {
        props: {
            title: String,
        },

        components: {
            Head,
            JetBanner,
            ApplicationNavBar,
            FlashMessages,
        },

    }
</script>
