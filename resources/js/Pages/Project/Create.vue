<template>
    <app-layout title="Create Project">
        <template #header>
            <h2 class="flex font-semibold text-xl text-gray-800 leading-tight">
                <Link class="text-primary" :href="route('projects.index')">Projects</Link>
                <span class="text-primary mx-2 font-medium">/</span>Create
            </h2>
        </template>

        <div class="pt-4 pb-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-5 overflow-hidden shadow-xl sm:rounded-lg md:w-9/12">
                    <form @submit.prevent="submit">
                        <div class="mt-4">
                            <jet-label for="title" value="Title" />
                            <jet-input id="title" type="text" class="mt-1 w-full" v-model="form.title" required />

                            <jet-input-error class="mt-2" :message="form.errors.title" />
                        </div>
                        <div class="mt-4">
                            <jet-label for="description" value="Description" />
                            <text-area id="description" class="mt-1 w-full" v-model="form.description" />

                            <jet-input-error :message="form.errors.description" class="mt-2" />
                        </div>
                        <div class="flex space-x-4 justify-end mt-4">
                            <jet-secondary-button @click="$inertia.get('/projects')">
                                Cancel
                            </jet-secondary-button>
                            <loading-button :loading="form.processing">
                                Submit
                            </loading-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import LoadingButton from '@/Shared/LoadingButton.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import TextArea from '@/Shared/TextArea.vue'

    export default {
        components: {
            AppLayout,
            Link,
            LoadingButton,
            JetSecondaryButton,
            JetLabel,
            JetInput,
            JetInputError,
            TextArea,
        },

        data() {
            return {
                form: this.$inertia.form({
                    title: null,
                    description: null,
                }),
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('projects.store'))
            }
        },
    }
</script>
