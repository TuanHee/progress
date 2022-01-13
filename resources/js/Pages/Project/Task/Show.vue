<template>
    <project-layout :project="project" :project_members="members">
        <div class="pt-4 pb-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-4">

                    <div class="p-4">
                        <div class="flex justify-between">
                            <Link :href="route('projects.show', { project: project.id })" class="flex p-2 rounded-lg hover:bg-gray-200">
                                <ChevronLeftIcon class="h-4 w-4" />
                            </Link>

                            <div class="flex space-x-2">
                                <button v-if="can.create_attachment" class="flex p-2 rounded-lg hover:bg-gray-200" @click="showAddAttachmentModal = true" title="Add Attachments">
                                    <PaperClipIcon class="h-4 w-4" />
                                </button>

                                <jet-dialog-modal maxWidth="xl" v-if="can.create_attachment" :show="showAddAttachmentModal" @close="closeAddAttachmentModal">
                                    <template #title>
                                        <div class="flex items-center font-bold">
                                            <PaperClipIcon class="h-4 w-4" />
                                            <h3 class="ml-2">Attachments</h3>
                                        </div>
                                    </template>
                                    <template #content>
                                        <form>
                                            <div class="my-4">
                                                <jet-label value="Attach from" />
                                                <div class="flex flex-col space-y-2 mt-1">

                                                    <input id="file" ref="file" type="file" class="hidden" @change.prevent="addAttachment" />

                                                    <button @click.prevent="selectFile"
                                                        class="flex items-center space-x-2 w-full rounded px-3 py-3 hover:bg-gray-200">
                                                        <DesktopComputerIcon class="h-4 w-4" />
                                                        <span>File from PC</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="mt-4">
                                                <jet-label for="url" value="Attach a link" />
                                                <jet-input id="url" type="text" class="mt-1 w-full" v-model="attachmentForm.link" />
                                                <jet-input-error :message="attachmentForm.errors.link" class="mt-2" />
                                            </div>

                                            <div class="flex mt-4 justify-end">
                                                <loading-button :loading="attachmentForm.processing" @click.prevent="addAttachment">Add</loading-button>
                                            </div>
                                        </form>
                                    </template>
                                    <template #footer>
                                        <jet-secondary-button @click="closeAddAttachmentModal">Close</jet-secondary-button>
                                    </template>
                                </jet-dialog-modal>

                                <Link v-if="can.update_task" :href="route('tasks.edit', { task: task.id })" class="flex p-2 rounded-lg hover:bg-gray-200">
                                    <PencilIcon class="h-4 w-4" />
                                </Link>

                                <button v-if="can.delete_task" class="flex p-2 rounded-lg hover:bg-gray-200" @click="showDeleteConfirm = true">
                                    <TrashIcon class="h-4 w-4" />
                                </button>

                                <confirm-dialog v-if="can.delete_task" :show="showDeleteConfirm" :maxWidth="'xl'"
                                    @close="showDeleteConfirm = false" :type="'danger'">

                                    <template #title>Delete Task</template>
                                    <template #content>
                                        Are you sure you want to delete the task? This action cannot be undone.
                                    </template>

                                    <template #footer>
                                        <div class="flex space-x-2 justify-end">
                                            <jet-secondary-button @click="showDeleteConfirm = false">Cancel</jet-secondary-button>
                                            <form @submit.prevent="$inertia.delete(route('tasks.destroy', {'task': task.id}))">
                                                <jet-danger-button :type="'submit'">Delete</jet-danger-button>
                                            </form>
                                        </div>
                                    </template>
                                </confirm-dialog>

                            </div>
                        </div>

                        <div class="px-2 mt-2 flex items-start space-x-3">
                            <div class="flex-row">
                                <h3 class="font-medium text-lg leading-7">{{ task.title }}</h3>
                                <small class="text-xs leading-tight text-gray-500">in <span class="hover:underline">{{ task.list.title }}</span> list</small>
                            </div>
                            <label v-if="can.update_task" class="items-center select-none relative px-2 py-1 text-xs rounded-md flex
                                hover:bg-green-50 hover:text-green-500 cursor-pointer
                                border border-gray-200 hover:border-green-200"
                                :class="{ 'border-green-200 bg-green-50 text-green-500' : task.completed }">
                                <jet-checkbox class="hidden"
                                    v-model:checked="task.completed" />
                                <CheckIcon class="h-4 w-4" />
                                <span class="ml-2">{{ task.completed ? 'Completed' : 'Mark complete' }}</span>
                                <span class="flex items-center ml-2">
                                    <div class="hover:brightness-90" v-for="performer_request_complete in task.performers_request_complete" :key="performer_request_complete.id">
                                        <profile-photo :size="5" :src="performer_request_complete.profile_photo_url" :alt="performer_request_complete.name" />
                                    </div>
                                </span>
                            </label>
                            <label v-else-if="!task.completed" class="items-center select-none relative
                                px-2 py-1 text-xs rounded-md flex border border-gray-200 cursor-pointer"
                                :class="{
                                    'cursor-not-allowed': current_member_id == null || requested_completion,
                                    'hover:bg-green-50 hover:text-green-500 hover:border-green-200': current_member_id != null && !requested_completion
                                }">
                                <jet-checkbox class="hidden" @change="requestComplete" :disabled="current_member_id == null || requested_completion" />
                                <CheckIcon v-if="!requestCompleteForm.processing" class="h-4 w-4" />
                                <svg v-else class="animate-spin -ml-1 mr-3 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="ml-2">{{ requested_completion ? 'Requested' : 'Request complete' }}</span>
                                <span class="flex items-center ml-2">
                                    <div class="hover:brightness-90" v-for="performer_request_complete in task.performers_request_complete" :key="performer_request_complete.id">
                                        <profile-photo :size="5" :src="performer_request_complete.profile_photo_url" :alt="performer_request_complete.name" />
                                    </div>
                                </span>
                            </label>
                            <label v-else-if="task.completed" class="items-center select-none relative px-2 py-1 text-xs rounded-md flex
                                border border-green-200 bg-green-50 text-green-500">
                                <CheckIcon class="h-4 w-4" />
                                <span class="ml-2">Completed</span>
                                <span class="flex items-center ml-2">
                                    <div class="hover:brightness-90" v-for="performer in task.performers_request_complete" :key="performer.id">
                                        <profile-photo :size="5" :src="performer.profile_photo_url" :alt="performer.name" />
                                    </div>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-200">
                        <dl>
                            <div class="bg-gray-50 px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 flex items-center">Assign To</dt>
                                <dd class="sm:col-span-2 mt-1">
                                    <div class="flex items-center space-x-1">
                                        <div class="hover:brightness-90" v-for="performer in task.performers" :key="performer.id">
                                            <profile-photo :src="performer.profile_photo_url" :alt="performer.name" />
                                        </div>
                                    </div>
                                </dd>
                            </div>

                            <div class="bg-white px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Priority</dt>
                                <dd class="sm:col-span-2 mt-1 flex items-center">
                                    <span v-if="task.priority == 'Low'"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-200 text-green-800">Low</span>
                                    <span v-if="task.priority == 'Normal'"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-200 text-yellow-800">Normal</span>
                                    <span v-if="task.priority == 'High'"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-800">High</span>
                                </dd>
                            </div>

                            <div class="bg-gray-50 px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                <dd class="text-sm text-gray-900 sm:col-span-2 mt-1">{{ task.start_at ?? '-' }}</dd>
                            </div>

                            <div class="bg-white px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                                <dd class="text-sm text-gray-900 sm:col-span-2 mt-1"
                                    :class="{
                                        'text-red-500': task.due && !task.completed
                                    }">{{ task.due_at ?? '-' }}</dd>
                            </div>

                            <div class="bg-gray-50 px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="text-sm text-gray-900 sm:col-span-2 mt-1"
                                    v-html="task.description ?task.description.replace(/(?:\r\n|\r|\n)/g, '<br />') : '-'"></dd>
                            </div>

                            <div class="bg-white px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6" v-if="task.attachments.length > 0">
                                <dt class="text-sm font-medium text-gray-500">Attachments</dt>
                                <dd class="text-sm text-gray-900 sm:col-span-2 mt-1">
                                    <ul role="list" class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                        <li v-for="attachment in task.attachments" :key="attachment.id" class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                            <template v-if="attachment.type == 'url'">
                                                <div class="w-0 flex-1 flex items-center">
                                                    <div class="flex flex-1 flex-col">
                                                        <div class="flex">
                                                            <LinkIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                                                            <a class="ml-2 flex-1 w-0 truncate hover:underline font-bold" target="_blank" :href="attachment.link">{{ attachment.link }}</a>
                                                        </div>
                                                        <div class="flex">
                                                            <span class="h-5 w-5"></span>
                                                            <span class="text-xs ml-2">{{ attachment.created_at }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ml-4 flex-shrink-0">
                                                    <button v-if="attachment.can.delete_attachment"
                                                        @click="openDeleteAttachmentConfirm(attachment.id)"
                                                        class="font-medium text-red-600 hover:text-red-500">
                                                        Remove
                                                    </button>
                                                </div>
                                            </template>

                                            <template v-else-if="['jpg', 'png'].includes(attachment.type)">
                                                <div class="w-0 flex-1 flex items-stretch">
                                                    <img class="w-auto h-36 cursor-pointer" @click="zoom(attachment.link)" :src="attachment.link" :alt="attachment.name">
                                                    <div class="w-0 flex-1 flex flex-col items-stretch ml-2">
                                                        <div class="flex flex-1 flex-col">
                                                            <div class="flex">
                                                                <PhotographIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                                                                <span class="ml-1 truncate font-bold">{{ attachment.name }}</span>
                                                            </div>
                                                            <div class="flex">
                                                                <span class="h-5 w-5"></span>
                                                                <span class="text-xs ml-1">{{ attachment.created_at }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="flex space-x-3 justify-end">
                                                            <a :href="attachment.link" class="font-medium text-blue-600 hover:text-blue-500" download>
                                                                Download
                                                            </a>
                                                            <button v-if="attachment.can.delete_attachment"
                                                                @click="openDeleteAttachmentConfirm(attachment.id)"
                                                                class="font-medium text-red-600 hover:text-red-500">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                            <template v-else>
                                                <div class="w-0 flex-1 flex flex-col items-stretch">
                                                    <div class="flex flex-1 flex-col">
                                                        <div class="flex">
                                                            <!-- Heroicon name: solid/paper-clip -->
                                                            <PaperClipIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                                                            <span class="ml-2 flex-1 w-0 truncate font-bold">{{ attachment.name }}</span>
                                                        </div>
                                                        <div class="flex">
                                                            <span class="h-5 w-5"></span>
                                                            <span class="text-xs ml-2">{{ attachment.created_at }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ml-4 flex-shrink-0 space-x-3">
                                                    <a :href="attachment.link" class="font-medium text-blue-600 hover:text-blue-500" download>Download</a>
                                                    <button v-if="attachment.can.delete_attachment"
                                                        @click="openDeleteAttachmentConfirm(attachment.id)"
                                                        class="font-medium text-red-600 hover:text-red-500">
                                                        Remove
                                                    </button>
                                                </div>
                                            </template>

                                        </li>
                                    </ul>

                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-4">
                    <div class="pt-4 px-4">
                        <h3 class="font-semibold text-lg">
                            <span>{{ task.comments.length }}</span> Comments
                        </h3>
                    </div>
                    <div class="px-4 pt-4">
                        <form @submit.prevent="makeComment" class="flex">
                            <profile-photo
                                :src="$page.props.user.profile_photo_url"
                                :alt="$page.props.user.name" :size="9"
                                class="mr-2" />
                            <div class="flex flex-auto relative">
                                <text-area v-model="commentForm.content" class="resize-none w-full" :rows="3" placeholder="Add your comment..." required />
                                <loading-button :loading="commentForm.processing" class="absolute bottom-2 right-2">Comment</loading-button>
                            </div>
                        </form>
                    </div>
                    <div class="px-4 py-5">
                        <comment v-for="comment in task.comments"
                            :key="comment" :comment="comment" />
                        <!-- <template v-for="comment in task.comments" :key="comment">{{ comment }} <br></template> -->
                    </div>
                </div>
            </div>

        </div>

        <!-- show image modal -->
        <jet-dialog-modal maxWidth="2xl" :show="imageToZoom != null" @close="imageToZoom = null">
            <template #content>
                <div class="flex justify-center">
                    <img :src="imageToZoom">
                </div>
            </template>
            <template #footer>
                <jet-secondary-button @click="imageToZoom = null">Close</jet-secondary-button>
            </template>
        </jet-dialog-modal>

        <confirm-dialog :show="showDeleteAttachmentConfirm" :maxWidth="'xl'"
            @close="closeDeleteAttachmentConfirm" :type="'danger'">

            <template #title>Delete Attachment</template>
            <template #content>
                Are you sure you want to delete the attachment? This action cannot be undone.
            </template>

            <template #footer>
                <div class="flex space-x-2 justify-end">
                    <jet-secondary-button @click="closeDeleteAttachmentConfirm">Cancel</jet-secondary-button>
                    <form @submit.prevent="deleteAttachment(delete_attachment_id)">
                        <jet-danger-button :type="'submit'">Delete</jet-danger-button>
                    </form>
                </div>
            </template>
        </confirm-dialog>
        <!-- {{ task.performers }} -->
    </project-layout>
</template>

<script>
    import ProjectLayout from '@/Layouts/ProjectLayout.vue'
    import { Link, useForm } from '@inertiajs/inertia-vue3'
    import {
        CheckIcon,
        ChevronLeftIcon,
        PencilIcon,
        TrashIcon,
        PaperClipIcon,
        DesktopComputerIcon,
        LinkIcon,
        PhotographIcon,
        ZoomInIcon
    } from '@heroicons/vue/outline'
    import JetCheckbox from '@/Jetstream/Checkbox.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import ProfilePhoto from '@/Shared/ProfilePhoto.vue'
    import TextArea from '@/Shared/TextArea.vue'
    import LoadingButton from '@/Shared/LoadingButton.vue'
    import ConfirmDialog from '@/Shared/ConfirmDialog.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import Comment from '@/Shared/Comment'

    export default {
        components: {
            ProjectLayout,
            Link,
            CheckIcon,
            ChevronLeftIcon,
            PencilIcon,
            TrashIcon,
            JetCheckbox,
            JetSecondaryButton,
            ProfilePhoto,
            TextArea,
            LoadingButton,
            ConfirmDialog,
            JetSecondaryButton,
            JetDangerButton,
            JetDialogModal,
            Comment,
            PaperClipIcon,
            LinkIcon,
            ZoomInIcon,
            PhotographIcon,
            DesktopComputerIcon,
            JetLabel,
            JetInput,
            JetInputError,
        },

        props: {
            project: Object,
            task: Object,
            members: Array,
            can: Object,
            task_member_ids: Array,
            current_member_id: Number,
            requested_completion: Boolean,
        },

        data() {
            return {
                delete_attachment_id: null,
                showDeleteConfirm: false,
                showDeleteAttachmentConfirm: false,
                showAddAttachmentModal: false,
                commentForm: useForm({
                    content: null,
                }),
                attachmentForm: useForm({
                    link: null,
                    attachment: null,
                }),
                imageToZoom: null,
                requestCompleteForm: useForm(),
            }
        },

        watch: {
            'task.completed'() {
                axios.post(route('tasks.updateStatus', {'task': this.task}),
                {
                    'completed': this.task.completed
                }).then((response) => {
                    console.log(response)
                }).catch((error) => {
                    console.log(error)
                })
            }
        },

        methods: {
            makeComment() {
                this.commentForm.post(
                    route('tasks.comments.store', { task: this.task.id }), {
                        preserveScroll: true,
                        onSuccess: () => this.commentForm.reset(),
                    }
                )
            },

            selectFile() {
                this.$refs.file.click()
            },

            addAttachment() {
                if (this.$refs.file) {
                    this.attachmentForm.attachment = this.$refs.file.files[0]
                }

                this.attachmentForm.post(route('tasks.attachments.store', { task: this.task.id }), {
                    preserveScroll: true,
                    onSuccess: () => this.closeAddAttachmentModal(),
                })
            },

            closeAddAttachmentModal() {
                this.clearInput()
                this.showAddAttachmentModal = false
            },

            clearInput() {
                if (this.$refs.file?.value) {
                    this.$refs.file.value = null
                }

                this.attachmentForm.link = null
                this.attachmentForm.attachment = null
            },

            zoom(url) {
                this.imageToZoom = url
            },

            openDeleteAttachmentConfirm(attachment_id) {
                this.showDeleteAttachmentConfirm = true
                this.delete_attachment_id = attachment_id
            },

            deleteAttachment(attachment_id) {
                this.$inertia.delete(route('attachments.destroy', {'attachment': attachment_id}))
                this.closeDeleteAttachmentConfirm()
            },

            closeDeleteAttachmentConfirm() {
                this.showDeleteAttachmentConfirm = false
                this.delete_attachment_id = null
            },

            requestComplete() {
                this.requestCompleteForm.post(
                    route('tasks.requestComplete', { task: this.task.id }), {
                        preserveScroll: true,
                    }
                )
            },
        }
    }
</script>
