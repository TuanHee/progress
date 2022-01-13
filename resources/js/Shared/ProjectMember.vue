<template>
    <template v-if="!can">
        <div class="flex items-center space-x-2 py-2 px-2 rounded-md cursor-pointer hover:bg-gray-200">
            <profile-photo :src="member.profile_photo_url" :alt="member.name" :size="8" />
            <div>{{ name }}</div>
            <span v-if="member.id == project_auth && project_auth != null" class="text-xs text-white bg-green-500 px-2 py-1 rounded-full">Auth</span>
            <span v-else-if="member.is_admin" class="text-xs text-white bg-green-500 px-2 py-1 rounded-full">Admin</span>
            <span v-if="member.user_id == $page.props.user.id" class="text-xs text-white bg-blue-500 px-2 py-1 rounded-full">You</span>
            <span v-if="member.user_id != project_auth && member.validated_at == null && project_auth != null" class="text-xs text-white bg-yellow-500 px-2 py-1 rounded-full">Invited</span>
        </div>
    </template>
    <template v-else>
        <div class="flex items-center space-x-2 py-2 px-2 rounded-md cursor-pointer hover:bg-gray-200" @click="showEditMemberModal">
            <profile-photo :src="member.profile_photo_url" :alt="member.name" :size="8" />
            <div>{{ name }}</div>
            <span v-if="member.user_id == project_auth && project_auth != null" class="text-xs text-white bg-green-500 px-2 py-1 rounded-full">Auth</span>
            <span v-else-if="member.is_admin" class="text-xs text-white bg-green-500 px-2 py-1 rounded-full">Admin</span>
            <span v-if="member.user_id == $page.props.user.id" class="text-xs text-white bg-blue-500 px-2 py-1 rounded-full">You</span>
            <span v-if="member.validated_at == null" class="text-xs text-white bg-yellow-500 px-2 py-1 rounded-full">Invited</span>
        </div>
        <jet-dialog-modal maxWidth="lg" :show="editMember" @close="closeEditMemberModal">
            <template #title>
                <h3 class="font-bold">Project Member</h3>
            </template>
            <template #content>
                <div class="flex justify-between px-4 pb-2">
                    <div class="text-gray-500">Name</div>
                    <div class="font-bold">{{ name }}</div>
                </div>
                <div class="flex justify-between px-4 pb-2">
                    <div class="text-gray-500">Email</div>
                    <div class="font-bold select-all">{{ member.email ?? '-' }}</div>
                </div>
                <div class="flex justify-between px-4 pb-2">
                    <div class="text-gray-500">Invited Date</div>
                    <div class="font-bold">{{ member.created_at }}</div>
                </div>
                <div class="flex justify-between px-4 pb-2">
                    <div class="text-gray-500">Joined Date</div>
                    <div class="font-bold">{{ member.validated_at ?? '-' }}</div>
                </div>
                <hr class="my-2"/>
                <h3 class="text-lg font-bold mb-3">Permissions</h3>
                <button @click="updatePermission(true)" class="block w-full rounded-md px-4 py-2 text-sm leading-5 text-left hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition" :disabled="member.is_admin">
                    <h4 class="text-gray-900 flex items-center space-x-1">
                        <CheckIcon v-if="member.is_admin" class="h-4 w-4" />
                        <div>Admin (Can Edit)</div>
                    </h4>
                    <div class="text-gray-500 text-xs">The member can add, edit and delete anything in this project.</div>
                </button>
                <button @click="updatePermission(false)" name="member" class="block w-full rounded-md px-4 py-2 text-sm leading-5 text-gray-900 text-left hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition" :disabled="!member.is_admin">
                    <h4 class="text-gray-900 flex items-center space-x-1">
                        <CheckIcon v-if="!member.is_admin" class="h-4 w-4" />
                        <div>Member (Can Comment)</div>
                    </h4>
                    <div class="text-gray-500 text-xs">The member can comment, but can't edit anything in this project.</div>
                </button>
                <hr class="my-2"/>
                <button type="submit" @click="showRemoveConfirm = true"
                    class="block w-full rounded-md px-4 py-2 text-sm leading-5 text-red-500 text-left hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition">Remove from Project</button>

                <confirm-dialog :show="showRemoveConfirm" :maxWidth="'xl'"
                    @close="showRemoveConfirm = false" :type="'danger'">

                    <template #title>Remove Project Member</template>
                    <template #content>
                        Are you sure? The member will remove from this project.
                    </template>
                    <template #footer>
                        <div class="flex space-x-2 justify-end">
                            <jet-secondary-button @click="showRemoveConfirm = false">Cancel</jet-secondary-button>
                            <form @submit.prevent="remove">
                                <jet-danger-button :type="'submit'">Remove</jet-danger-button>
                            </form>
                        </div>
                    </template>
                </confirm-dialog>
            </template>
            <template #footer>
                <jet-secondary-button @click="closeEditMemberModal">Close</jet-secondary-button>
            </template>
        </jet-dialog-modal>
    </template>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3'
    import ProfilePhoto from '@/Shared/ProfilePhoto.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import { CheckIcon } from '@heroicons/vue/outline'
    import ConfirmDialog from '@/Shared/ConfirmDialog.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'

    export default {
        components: {
            Link,
            ProfilePhoto,
            JetDialogModal,
            JetSecondaryButton,
            CheckIcon,
            ConfirmDialog,
            JetDangerButton,
        },

        props: {
            member: Object,
            project_auth: Number,
        },

        data() {
            return {
                name: this.member.name,
                profile_photo_url: this.member.profile_photo_url,
                can: this.member.can ? this.member.can.edit_member : false,
                // modal
                editMember: false,
                showRemoveConfirm: false,
            }
        },

        methods: {
            showEditMemberModal() {
                this.editMember = true
            },

            closeEditMemberModal() {
                this.editMember = false
            },

            updatePermission(setAdmin) {
                axios.post(route('members.updatePermission', {'member': this.member}), {
                    setAdmin: setAdmin,
                }).then((response) => {
                    this.member.is_admin = response.data.is_admin
                }).catch((error) => {
                    console.log(error)
                })
            },

            remove() {
                this.$inertia.delete(route(
                    'members.remove',
                    {'project': this.member.project_id, 'member': this.member.id}
                ))
            },
        }
    }
</script>
