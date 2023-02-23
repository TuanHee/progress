<template>
    <textarea ref="input" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
        :rows="rows" @input="$emit('update:modelValue', $event.target.value)">{{ modelValue }}</textarea>
</template>

<script>
    export default {
        props: {
            modelValue: String,
            rows: {
                default: '4'
            },
        },

        emits: ['update:modelValue'],

        methods: {
            focus() {
                this.$refs.input.focus()
            },

            select() {
                this.$refs.input.select()
            },
        }
    }
</script>
