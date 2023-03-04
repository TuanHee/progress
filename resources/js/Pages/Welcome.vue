<template>
    <web-layout title="Home">
        <div class="pt-14">
            <div class="max-w-7xl mx-auto mt-6 sm:px-6 lg:px-8">
                <div class="bg-white px-4 py-5 rounded shadow-sm">
                    <div class="flex space-x-5">
                        <div class="md:block lg:w-1/2 items-stretch p-3">
                            <h2 class="text-5xl font-semibold">Progress</h2>
                            <div class="my-4 flex-col -space-y-1">
                                <hr class="w-1/12 border-purple-900">
                                <hr class="w-1/6 border-primary">
                                <hr class="w-1/3 border-green-400">
                            </div>
                            <p class="text-lg">A Task Management System, which provide a web-based system and mobile application. This system focuses on project managers handing large teams and complex projects.</p>
                        </div>
                        <div class="hidden lg:block lg:w-1/2 relative">
                            <img src="/storage/progress.png" alt="A task Management System"
                                class="absolute -translate-y-1/2 top-1/2 shadow-lg rounded-lg">
                            <img src="/storage/mobile_app.png" alt="Mobile Application"
                                class="absolute z-10 -bottom-1/4 -right-6 rounded-lg w-2/12 shadow-md">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-16">
            <div class="max-w-7xl mx-auto mt-6 sm:px-6 lg:px-8">
                <div class="bg-white px-4 py-5 rounded shadow-sm">
                    <div class="flex space-x-5">
                        <div class="lg:w-1/2 relative">
                            <img src="/storage/invite_function.png" alt="Permission Function"
                                class="absolute w-1/2 -translate-y-1/2 -translate-x-1/2 top-1/2 left-1/3 -skew-y-12 rounded-lg shadow-md">
                            <img src="/storage/permission_function.png" alt="Permission Function"
                                class="absolute w-1/2 -translate-y-1/2 -translate-x-1/2 top-1/2 left-2/3 -skew-y-12 rounded-lg shadow-md">
                        </div>
                        <div class="lg:w-1/2">
                            <h2 class="text-3xl font-semibold">Project Member Function</h2>
                            <div class="my-4 flex-col -space-y-1">
                                <hr class="w-1/12 border-purple-900">
                                <hr class="w-1/6 border-primary">
                                <hr class="w-1/3 border-green-400">
                            </div>
                            <p>Project member function allow project author and admin to invite someone to join the project and manage their permission in the project.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </web-layout>
</template>

<!-- <style scoped>
    .bg-gray-100 {
        background-color: #f7fafc;
        background-color: rgba(247, 250, 252, var(--tw-bg-opacity));
    }

    .border-gray-200 {
        border-color: #edf2f7;
        border-color: rgba(237, 242, 247, var(--tw-border-opacity));
    }

    .text-gray-400 {
        color: #cbd5e0;
        color: rgba(203, 213, 224, var(--tw-text-opacity));
    }

    .text-gray-500 {
        color: #a0aec0;
        color: rgba(160, 174, 192, var(--tw-text-opacity));
    }

    .text-gray-600 {
        color: #718096;
        color: rgba(113, 128, 150, var(--tw-text-opacity));
    }

    .text-gray-700 {
        color: #4a5568;
        color: rgba(74, 85, 104, var(--tw-text-opacity));
    }

    .text-gray-900 {
        color: #1a202c;
        color: rgba(26, 32, 44, var(--tw-text-opacity));
    }

    @media (prefers-color-scheme: dark) {
        .dark\:bg-gray-800 {
            background-color: #2d3748;
            background-color: rgba(45, 55, 72, var(--tw-bg-opacity));
        }

        .dark\:bg-gray-900 {
            background-color: #1a202c;
            background-color: rgba(26, 32, 44, var(--tw-bg-opacity));
        }

        .dark\:border-gray-700 {
            border-color: #4a5568;
            border-color: rgba(74, 85, 104, var(--tw-border-opacity));
        }

        .dark\:text-white {
            color: #fff;
            color: rgba(255, 255, 255, var(--tw-text-opacity));
        }

        .dark\:text-gray-400 {
            color: #cbd5e0;
            color: rgba(203, 213, 224, var(--tw-text-opacity));
        }
    }
</style> -->

<script>
    import { Head, Link } from '@inertiajs/inertia-vue3';
    import WebLayout from '@/Layouts/WebLayout.vue'

    export default {
        components: {
            Head,
            WebLayout,
            Link,
        },

        props: {
            canLogin: Boolean,
            canRegister: Boolean,
            laravelVersion: String,
            phpVersion: String,
        }
    }
</script>
