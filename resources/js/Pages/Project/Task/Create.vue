<template>
    <project-layout :project="project" :project_members="members">
        <div class="pt-4 pb-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="md:grid md:grid-cols-3 md:gap-5">
                    <div class="md:col-span-2 mb-4">
                        <div class="card">
                            <div class="pb-2 border-b">
                                <h3 class="text-lg font-bold">Add Task</h3>
                            </div>
                            <form @submit.prevent="form.post(this.route('taskLists.tasks.store', { taskList: list.id }))" class="grid grid-cols-3 gap-x-6 gap-y-3 pt-4">
                                <div class="col-span-3">
                                    <jet-label for="task_title" value="Task Title" />
                                    <jet-input id="task_title" ref="task_title"
                                        type="text" class="mt-1 w-full"
                                        v-model="form.title"
                                        placeholder="Enter Task Title" required />
                                    <jet-input-error class="mt-2" :message="errors.title" />
                                </div>
                                <div class="col-span-3">
                                    <jet-label for="description" value="Description" />
                                    <text-area id="description"
                                        v-model="form.description"
                                        type="text" class="mt-1 w-full"
                                        placeholder="Description" />
                                </div>
                                <div class="col-span-3 sm:col-span-3 lg:col-span-1">
                                    <jet-label for="priority" value="Priority" />
                                    <select id="priority"
                                        v-model="form.priority"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 focus:ring-indigo-200 focus:border-indigo-300 sm:text-sm">
                                        <option v-for="priority in priorities" :key="priority" :value="priority">{{ priority }}</option>
                                    </select>
                                </div>
                                <div class="col-span-3 sm:col-span-3 lg:col-span-1">
                                    <jet-label for="start_date" value="Start Date" />
                                    <input type="date" id="start_date"
                                        v-model="form.start_date"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 focus:ring-indigo-200 focus:border-indigo-300 sm:text-sm">
                                    <jet-input-error class="mt-2" :message="errors.start_date" />
                                </div>
                                <div class="col-span-3 sm:col-span-3 lg:col-span-1">
                                    <jet-label for="due_date" value="Due Date" />
                                    <input type="date" id="due_date"
                                        v-model="form.due_date"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 focus:ring-indigo-200 focus:border-indigo-300 sm:text-sm">
                                    <jet-input-error class="mt-2" :message="errors.due_date" />
                                </div>
                                <div class="col-span-3 sm:col-span-3 lg:col-span-1">
                                    <jet-label for="task_members" value="Assign To" />
                                    <div class="flex space-x-1 mt-2">
                                        <div class="flex -space-x-2 justify-around items-center mr-2" v-show="selectedMember.length">
                                            <profile-photo v-for="member in selectedMember" :key="member.id" :size="8"
                                                :src="member.profile_photo_url" :alt="member.name" />
                                        </div>
                                        <button type="button" @click="openMembersPicker"
                                            class="h-8 w-8 border border-dashed border-gray-500 rounded-full flex justify-center items-center text-gray-500 hover:bg-gray-200">
                                            <PlusSmIcon class="h-6 w-6" />
                                        </button>
                                        <TransitionRoot as="template" :show="showMembersPicker">
                                            <Dialog as="div" class="fixed z-10 inset-0 overflow-y-auto" @close="closeMembersPicker">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                                                <DialogOverlay class="fixed inset-0 bg-gray-500 bg-opacity-50 transition-opacity" />
                                                </TransitionChild>

                                                <!-- This element is to trick the browser into centering the modal contents. -->
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                            <div class="mt-3 text-center sm:mt-0 sm:text-left">
                                                                <div class="flex justify-between items-center pb-2 border-b">
                                                                    <DialogTitle as="h3" class="text-lg leading-6 font-medium text-gray-900">
                                                                        Select Members
                                                                    </DialogTitle>
                                                                    <button @click="closeMembersPicker" class="p-2 rounded-full hover:bg-gray-200">
                                                                        <XIcon class="h-5 w-5" />
                                                                    </button>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <label type="button"
                                                                        class="w-full block rounded-md cursor-pointer hover:bg-gray-200 py-2 px-2"
                                                                        v-for="member in members" :key="member.id">
                                                                        <div class="flex justify-between items-center">
                                                                            <div class="flex items-center space-x-2">
                                                                                <profile-photo :src="member.profile_photo_url" :alt="member.name" :size="8" />
                                                                                <div>{{ member.name }}</div>
                                                                            </div>
                                                                            <input type="checkbox" class="hidden" :value="member.id" v-model="form.members">
                                                                            <CheckIcon class="h-4 w-4 text-gray-500 mr-1" v-show="isMemberSelected(member.id)" />
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </TransitionChild>
                                            </div>
                                            </Dialog>
                                        </TransitionRoot>
                                    </div>
                                </div>
                                <div class="col-span-3 flex justify-end">
                                    <loading-button :loading="form.processing">Save</loading-button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="md:col-span-1 mb-4">
                        <div class="card">
                            <div class="pb-2 border-b">
                                <div class="text-xs text-gray-400">Task List</div>
                                <h3 class="font-bold">{{ list.title }}</h3>
                            </div>
                            <div class="flex flex-col">
                                <div class="mt-4 p-2 bg-gray-50 rounded ring ring-indigo-200">
                                    <div>
                                        <div v-if="form.title" class="font-bold">{{ form.title }}</div>
                                        <div v-else class="text-gray-400">Title</div>
                                        <div v-if="form.description" class="text-sm" v-html="(form.description).replace(/(?:\r\n|\r|\n)/g, '<br />')"></div>
                                        <div class="flex justify-between">
                                            <template v-if="form.priority == priorities[0]">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-200 text-green-800">Low</span>
                                            </template>
                                            <template v-else-if="form.priority == priorities[1]">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-200 text-yellow-800">Normal</span>
                                            </template>
                                            <template v-else>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-800">High</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </project-layout>
</template>

<script>
    import { useForm } from '@inertiajs/inertia-vue3'
    import ProjectLayout from '@/Layouts/ProjectLayout.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import TextArea from '@/Shared/TextArea.vue'
    import LoadingButton from '@/Shared/LoadingButton.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import {
        Dialog,
        DialogOverlay,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
    } from '@headlessui/vue'
    import {
        PlusSmIcon,
        CheckIcon,
        XIcon,
    } from '@heroicons/vue/outline'
    import ProfilePhoto from '@/Shared/ProfilePhoto.vue'

    export default {
        components: {
            ProjectLayout,
            JetLabel,
            JetInput,
            TextArea,
            LoadingButton,
            JetInputError,
            Dialog,
            DialogOverlay,
            DialogTitle,
            TransitionChild,
            TransitionRoot,
            PlusSmIcon,
            CheckIcon,
            XIcon,
            ProfilePhoto,
        },

        props: {
            project: Object,
            list: Object,
            members: Array,
            priorities: Array,
            errors: Object,
        },

        data() {
            return {
                showMembersPicker: false,
                form: useForm({
                    title: '',
                    description: '',
                    priority: this.priorities[1],
                    start_date: '',
                    due_date: '',
                    members: [],
                })
            }
        },

        methods: {
            openMembersPicker() {
                this.showMembersPicker = true
            },
            closeMembersPicker() {
                this.showMembersPicker = false
            },
            isMemberSelected(member_id) {
                if (this.form.members.includes(member_id)) {
                    return true
                }
                return false
            }
        },

        computed: {
            selectedMember() {
                return this.members.filter(member => this.form.members.includes(member.id))
            }
        }
    }
</script>
