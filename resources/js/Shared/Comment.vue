<template>
    <div class="flex mb-6">
        <profile-photo
            class="mt-1 mr-2"
            :size="9"
            :src="comment.member.profile_photo_url"
            :alt="comment.member.name" />

        <div v-if="comment.deleted" class="flex-row flex-auto px-2 pt-2">
            <div class="flex space-x-2">
                <span class="text-xs font-semibold text-blue-700">{{ comment.member.name }}</span>
                <span class="text-xs text-gray-500">{{ comment.duration }}</span>
            </div>
            <div class="flex space-x-2">
                <span class="text-xs text-gray-500">Comment Deleted</span>
            </div>
        </div>

        <div v-else class="flex-row flex-auto px-2 pt-2">
            <div class="flex space-x-2">
                <span class="text-xs font-semibold text-blue-700">{{ comment.member.name }}</span>
                <span class="text-xs text-gray-500">{{ comment.duration }}</span>
                <span class="text-xs text-gray-500" v-if="comment.edited">(edited)</span>
            </div>
            <div>
                <p v-show="!editMode" class="mt-1 mb-2 leading-tight" v-html="comment.content.replace(/(?:\r\n|\r|\n)/g, '<br />')" />
                <div v-show="editMode" class="flex flex-auto relative">
                    <text-area ref="commentBox" v-model="form.content" class="resize-none w-full" @focus="resizeTextArea"
                        @input="resizeTextArea" placeholder="Add your comment..." required />
                    <div class="flex space-x-2 absolute bottom-2 right-2">
                        <jet-secondary-button @click="cancel">Cancel</jet-secondary-button>
                        <loading-button :loading="form.processing" @click="save" :disabled="this.form.content == null || this.form.content == ''">Save</loading-button>
                    </div>
                </div>
            </div>
            <div class="text-xs flex space-x-2 text-gray-600" v-if="$page.props.user.id == comment.member.user_id">
                <span v-if="!editMode">
                    <button @click="showCommentBox">Edit</button> - <button @click="showDeleteConfirm = true">Delete</button>
                </span>
                <confirm-dialog :show="showDeleteConfirm" :maxWidth="'xl'"
                    @close="showDeleteConfirm = false" :type="'danger'">

                    <template #title>Delete Comment</template>
                    <template #content>
                        Are you sure you want to delete the comment? This action cannot be undone.
                    </template>

                    <template #footer>
                        <div class="flex space-x-2 justify-end">
                            <jet-secondary-button @click="showDeleteConfirm = false">Cancel</jet-secondary-button>
                            <jet-danger-button @click="deleteComment">Delete</jet-danger-button>
                        </div>
                    </template>
                </confirm-dialog>
            </div>
        </div>

    </div>
</template>

<script>
    import { Link, useForm } from '@inertiajs/inertia-vue3'
    import ProfilePhoto from './ProfilePhoto.vue'
    import TextArea from './TextArea.vue'
    import LoadingButton from './LoadingButton.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import ConfirmDialog from '@/Shared/ConfirmDialog.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'

    export default {
        components: {
            Link,
            ProfilePhoto,
            TextArea,
            LoadingButton,
            JetSecondaryButton,
            ConfirmDialog,
            JetDangerButton,
        },

        props: {
            comment: Object,
        },

        data() {
            return {
                editMode: false,
                form: useForm({
                    'content': this.comment.content
                }),
                showDeleteConfirm: false,
            }
        },

        methods: {
            resizeTextArea(e) {
                const box = e.target
                const line_count = box.value.split(/\r*\n/).length
                box.style.height = (line_count * 24 + 55) + 'px'
            },

            showCommentBox() {
                this.editMode = true
                setTimeout(() => {
                    this.$refs.commentBox.focus()
                    this.$refs.commentBox.select()
                }, 250)
            },

            cancel() {
                this.form.content = this.comment.content
                this.editMode = false
            },

            save() {
                this.form.put(this.route('comments.update', {comment: this.comment.id}), {
                    preserveScroll: true,
                    onSuccess: () => this.editMode = false,
                })
            },

            deleteComment() {
                this.$inertia.delete(route('comments.destroy', {'comment': this.comment.id}), {
                    preserveScroll: true,
                })
            },

        }
    }
</script>
