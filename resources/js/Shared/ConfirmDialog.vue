<template>
    <modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <!-- icon -->
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                    :class="{
                        'bg-blue-100': type == 'info',
                        'bg-red-100': type == 'danger',
                    }">
                    <InformationCircleIcon v-if="type == 'info'" class="h-6 w-6 text-blue-600" />
                    <ExclamationIcon v-if="type == 'danger'" class="h-6 w-6 text-red-600" />
                </div>

                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        <slot name="title"></slot>
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            <slot name="content"></slot>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-100 text-right">
            <slot name="footer">
            </slot>
        </div>
    </modal>
</template>

<script>
    import Modal from '@/Jetstream/Modal.vue'
    import { ExclamationIcon, InformationCircleIcon } from '@heroicons/vue/outline'

    export default {
        emits: ['close'],

        components: {
            Modal,
            ExclamationIcon,
            InformationCircleIcon,
        },

        props: {
            show: {
                default: false
            },
            maxWidth: {
                default: '2xl'
            },
            closeable: {
                default: true
            },
            type: {
                default: 'info'
            }
        },

        methods: {
            close() {
                this.$emit('close')
            },
        }
    }
</script>
