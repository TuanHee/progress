<template>
    <div class="flex items-center justify-end border-gray-200">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <div class="flex justify-end">
                <div v-for="(link, key) in links" :key="key">
                    <Link :href="link.url" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                        :class="{
                            'bg-indigo-50 border-indigo-500 text-indigo-600 ' : link.active,
                            'rounded-l-md' : key == 0,
                            'rounded-r-md' : key == (links.length - 1)
                        }" v-html="link.label" />
                </div>
            </div>
        </nav>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';

    export default {
        components: {
            Link,
        },
        props: {
            links: Array,
        },
    }
</script>
