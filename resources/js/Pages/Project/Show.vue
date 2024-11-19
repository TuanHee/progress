<template>
    <project-layout :project="project" :project_members="members">

        <div class="pt-4 pb-12">
            <template v-if="can.create_task_list">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-4">
                    <jet-secondary-button @click="showAddTaskListModel">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        add list
                    </jet-secondary-button>
                    <jet-dialog-modal :show="addTaskList" @close="closeAddTaskListModel">
                        <template #title>
                            <h3 class="font-bold">Add Task List</h3>
                        </template>
                        <template #content>
                            <div>
                                <jet-label for="task_list_title" value="Task List Title" />
                                <jet-input id="task_list_title" ref="task_list_title"
                                    type="text" class="mt-1 w-full" v-model="addTaskListForm.title"
                                    @keyup.enter="submitAddTaskList"
                                    placeholder="Enter Task List Title" required />

                                <jet-input-error :message="addTaskListForm.error" class="mt-2" />
                            </div>
                        </template>
                        <template #footer>
                            <jet-secondary-button @click="closeAddTaskListModel">Close</jet-secondary-button>
                            <loading-button class="ml-2"
                                @click="submitAddTaskList"
                                :loading="addTaskListForm.processing">Add</loading-button>
                        </template>
                    </jet-dialog-modal>
                </div>
            </template>

        <template v-for="taskList in taskLists" :key="taskList.id">
            <task-list :can="can.create_task" :list="taskList" />
        </template>
        </div>
    </project-layout>
</template>

<script>
    import ProjectLayout from '@/Layouts/ProjectLayout'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import LoadingButton from '@/Shared/LoadingButton'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import TaskList from '@/Shared/TaskList'

    export default {
        components: {
            ProjectLayout,
            JetInput,
            JetInputError,
            JetLabel,
            LoadingButton,
            JetSecondaryButton,
            JetDialogModal,
            TaskList,
        },
        props: {
            project: Object,
            taskLists: Array,
            members: Array,
            can: Object,
        },
        data() {
            return {
                addTaskList: false,
                addTaskListForm: {
                    title: '',
                    error: '',
                },
            }
        },

        methods: {
            showAddTaskListModel() {
                this.addTaskList = true

                setTimeout(() => this.$refs.task_list_title.focus(), 250)
            },
            closeAddTaskListModel() {
                this.addTaskList = false
                this.addTaskListForm.title = ''
                this.addTaskListForm.error = ''
            },
            submitAddTaskList() {
                this.addTaskListForm.processing = true

                axios.post(route('projects.taskLists.store', {'project': this.project}), {
                    title: this.addTaskListForm.title
                }).then((response) => {
                    this.taskLists.push(response.data)
                    this.addTaskListForm.processing = false
                    this.closeAddTaskListModel()
                }).catch((error) => {
                    this.addTaskListForm.processing = false
                    this.addTaskListForm.error = error.response.data.errors.title[0]
                    this.$refs.task_list_title.focus()
                })
            }
        }
    }
</script>
