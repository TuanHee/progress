<template>
    <Link :href="'/'">
        <svg class="w-16 h-16" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill="#86EFAC" d="M3 7a 4 4 0 0 1 4 -4h34a 4 4 0 0 1 4 4v4 a 4 4 0 0 1 -4 4 H7a 4 4 0 0 1 -4 -4 v-2 z" />
            <path fill="#4F46E5" d="M3 22a4 4 0 014-4h22a4 4 0 014 4v4a4 4 0 01-4 4H7a4 4 0 01-4-4v-3z" />
            <path fill="#701A75" d="M3 37a4 4 0 014-4h8a4 4 0 014 4v4a4 4 0 01-4 4H7a4 4 0 01-4-4v-3z" />
        </svg>
    </Link>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';

    export default {
        components: {
            Link,
        },
    }
</script>
