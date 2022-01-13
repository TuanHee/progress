<template>
    <div class="pb-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow sm:rounded-lg p-2">
                <Disclosure v-slot="{ open }" :defaultOpen="true"><!-- px-2 py-2 -->
                    <div class="flex justify-between items-center rounded-lg">
                        <DisclosureButton
                        class="flex flex-grow space-x-2 text-left items-center pl-2 py-2" >
                            <ChevronUpIcon
                                :class="open ? 'transform rotate-180' : ''"
                                class="w-5 h-5 text-gray-400" />
                            <span class="font-bold">{{ list.title }}</span>
                        </DisclosureButton>
                        <div v-show="can" class="flex justify-end space-x-1 px-2">
                            <button @click="showEditTaskListModal" class="flex bg-gray-100 px-2 py-2 rounded-lg hover:bg-gray-200 focus:shadow-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <jet-dialog-modal :show="editTaskList" @close="closeEditTaskListModal">
                                <template #title>
                                    <h3 class="font-bold">Edit Task List</h3>
                                </template>
                                <template #content>
                                    <jet-label for="task_list_title" value="Task List Title" />
                                    <jet-input id="task_list_title" ref="task_list_title"
                                        @keyup.enter="updateTaskList"
                                        type="text" class="mt-1 w-full" v-model="editTaskListForm.title"
                                        placeholder="Task List Title" required />

                                    <jet-input-error :message="editTaskListForm.error" class="mt-2" />
                                </template>
                                <template #footer>
                                    <jet-secondary-button @click="closeEditTaskListModal">Close</jet-secondary-button>
                                    <jet-button @click="updateTaskList" class="ml-2"
                                        :class="{ 'opacity-25': editTaskListForm.processing }"
                                        :disabled="editTaskListForm.processing">Update</jet-button>
                                </template>
                            </jet-dialog-modal>
                            <button class="flex bg-gray-100 px-2 py-2 rounded-lg hover:bg-gray-200 focus:shadow-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <transition
                        enter-active-class="transition duration-100 ease-out"
                        enter-from-class="transform scale-95 opacity-0"
                        enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-75 ease-out"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0">
                        <DisclosurePanel class="mt-2 text-gray-600 bg-gray-50 rounded-b-lg shadow-inner">
                            <div class="flex justify-around w-full text-xs leading-7 text-left items-center border-b uppercase bg-gray-100 text-gray-400 font-semibold">
                                <div class="pl-4 pr-2"></div>
                                <div class="flex-grow border-r truncate text-center">Task</div>
                                <div class="inline-flex border-r px-2 self-stretch">
                                    <div class="flex -space-x-1 items-center w-24 justify-center">Assigner</div>
                                </div>
                                <div class="w-24 text-center border-r">Priority</div>
                                <div class="w-24 text-center border-r">Assigned At</div>
                                <div class="w-24 text-center border-r">Start At</div>
                                <div class="w-24 text-center">Due At</div>
                            </div>
                            <template v-if="list.tasks.length > 0">
                                <task :task="task" :can="can" v-for="(task, index) in list.tasks" :key="task.id"
                                    :borderBottom="(index != list.tasks.length - 1) || can" />
                            </template>
                            <template v-else-if="!can">
                                <div class="pl-10 block w-full rounded-md px-4 py-2 text-sm leading-5 text-left">Not Task Yet</div>
                            </template>
                            <template v-if="can">
                                <Link :href="route('taskLists.tasks.create', list.id)"
                                    class="pl-10 flex w-full rounded-b-md py-2 text-sm leading-5 text-left hover:bg-gray-200 focus:outline-none focus:bg-gray-100 transition">
                                    <PlusSmIcon class="w-5 h-5"/>
                                    Add Task
                                </Link>
                            </template>
                        </DisclosurePanel>
                    </transition>
                </Disclosure>
            </div>
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3'
    import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import Task from '@/Shared/Task.vue'
    import { ChevronUpIcon, PlusSmIcon } from '@heroicons/vue/solid'

    export default {
        components: {
            Link,
            Disclosure,
            DisclosureButton,
            DisclosurePanel,
            JetDialogModal,
            JetLabel,
            JetInput,
            JetInputError,
            JetButton,
            JetSecondaryButton,
            Task,
            ChevronUpIcon,
            PlusSmIcon,
        },

        props: {
            list: Object,
            can: Boolean,
        },

        data() {
            return {
                editTaskList: false,
                addTask: false,
                editTaskListForm: {
                    title: this.list.title,
                    error: '',
                }
            }
        },

        methods: {
            showEditTaskListModal() {
                this.editTaskList = true

                setTimeout(() => this.$refs.task_list_title.focus(), 250)
            },

            updateTaskList() {
                this.editTaskListForm.processing = true

                axios.put(route('taskLists.update', {'taskList': this.list}), {
                    title: this.editTaskListForm.title
                }).then((response) => {
                    this.list.title = response.data.taskList.title
                    this.editTaskListForm.processing = false
                    this.closeEditTaskListModal()
                }).catch(error => {
                    this.editTaskListForm.processing = false
                    this.editTaskListForm.error = error.response.data.errors.title[0]
                    this.$refs.task_list_title.focus()
                })
            },

            closeEditTaskListModal() {
                this.editTaskList = false
                this.editTaskListForm.title = this.list.title
                this.editTaskListForm.error = ''
            },
        }
    }
</script>
