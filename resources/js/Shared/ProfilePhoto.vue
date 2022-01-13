<template>
    <img :class="sizeClass" class="rounded-full object-cover" :src="src" :alt="alt" />
</template>

<script>
    export default {
        props: {
            src: String,
            alt: String,
            size: {
                default: '7'
            },
        },

        computed: {
            sizeClass() {
                return {
                    '5': 'h-5 w-5',
                    '7': 'h-7 w-7',
                    '8': 'h-8 w-8',
                    '9': 'h-9 w-9',
                    '10': 'h-10 w-10',
                }[this.size]
            }
        }
    }
</script>
