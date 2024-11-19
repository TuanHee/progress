
import { title } from 'process';

<template>
    <Head :title="title" />

    <jet-banner/>

    <div class="min-h-screen bg-gray-100">

        <nav class="bg-white shadow border-gray-100">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex-shrink-0 flex items-center">
                        <Link class="flex items-center space-x-3" :href="route('home')">
                            <application-mark class="block h-9 w-auto" />
                            <span class="text-2xl font-bold text-primary">Progress</span>
                        </Link>
                    </div>

                    <div v-if="$page.props.canLogin" class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <Link v-if="$page.props.user" :href="route('dashboard')"
                            class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition">
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link :href="route('login')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition">
                                Log in
                            </Link>

                            <Link v-if="$page.props.canRegister" :href="route('register')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition">
                                Register
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <slot></slot>
        </main>

        <div class="bg-white py-4 fixed bottom-0 min-w-full">
            <div class="items-center max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-center sm:items-center sm:justify-between">
                    <div class="text-center text-sm text-gray-500 sm:text-left">
                        <div class="flex">&copy; 2023</div>
                    </div>

                    <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">
                        Laravel v{{ $page.props.laravelVersion }} (PHP v{{ $page.props.phpVersion }})
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import { Head, Link } from '@inertiajs/inertia-vue3'
    import ApplicationMark from '@/Shared/ApplicationMark.vue'
    import JetBanner from '@/Jetstream/Banner.vue'

    export default {
        components: {
            Head,
            Link,
            ApplicationMark,
            JetBanner,
        },

        props: {
            title: String,
        }
    }
</script>
