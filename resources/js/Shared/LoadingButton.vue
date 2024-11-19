<template>
    <button :disabled="loading"
        class="inline-flex items-center px-4 py-2 bg-blue-700 border border-transparent rounded-md font-semibold text-xs
            text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-800 focus:outline-none
            focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
        <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        <slot />
    </button>
</template>

<script>
    export default {
        props: {
            loading: Boolean,
        },
    }
</script>
