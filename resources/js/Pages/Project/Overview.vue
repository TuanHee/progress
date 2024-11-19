<template>
    <project-layout :project="project" :project_members="members">

        <div class="pt-4 pb-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-3 lg:gap-5">
                    <div class="md:col-span-2 mb-4">
                        <div class="card min-h-full">
                            <div class="pb-2 border-b">
                                <h3 class="text-lg font-bold">Project Information</h3>
                            </div>
                            <template v-if="can.update_project">
                                <form @submit.prevent="projectForm.put(this.route('projects.update', project.id))">
                                    <div class="mt-4">
                                        <jet-label for="title" value="Title" />
                                        <jet-input id="title" type="text" class="mt-1 w-full" v-model="projectForm.title" required />
                                        <jet-input-error class="mt-2" :message="errors.title" />
                                    </div>
                                    <div class="mt-4">
                                        <jet-label for="description" value="Description" />
                                        <text-area id="description" type="text" class="mt-1 w-full" v-model="projectForm.description" />
                                    </div>
                                    <div class="mt-4 flex space-x-4 justify-end">
                                        <loading-button :loading="projectForm.processing">Submit</loading-button>
                                    </div>
                                </form>
                            </template>
                            <template v-else>
                                <div>
                                    <h3 class="mt-4 font-bold">{{ project.title }}</h3>
                                    <div class="mt-4">{{ project.description ?? '-' }}</div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="card min-h-full">
                            <div class="font-bold">Project Author</div>
                            <project-member :member="project.user" />
                            <div class="mt-4">
                                <div class="font-bold">Project Members</div>
                                <div class="flex flex-col space-y-1">
                                    <template v-if="can.create_project_member">
                                        <div class="flex items-center space-x-2 py-2 px-2 rounded-md cursor-pointer hover:bg-gray-200" @click="showAddMemberModal">
                                            <div class="rounded-full border border-dashed border-gray-500 w-8 h-8 flex justify-center items-center text-gray-500">
                                                <PlusSmIcon class="h-6 w-6" />
                                            </div>
                                            <div>Add Member</div>
                                        </div>
                                        <jet-dialog-modal maxWidth="xl" :show="addMember" @close="closeAddMemberModal">
                                            <template #title>
                                                <h3 class="font-bold">Invite teammates</h3>
                                            </template>
                                            <template #content>
                                                <form @submit.prevent="sendInviteMail">
                                                    <div class="relative">
                                                        <jet-label for="email" value="Invite Member" />
                                                        <jet-input id="email" ref="email" type="email" class="mt-1 w-full"
                                                            @keypress="showAutoCompletePopUp"
                                                            v-model="inviteMemberForm.email" placeholder="Email Address"
                                                            autocomplete="off" required />
                                                        <div class="absolute bg-white w-full rounded-b-md shadow-md flex flex-col" v-if="emailAutoComplete.length">
                                                            <div class="flex flex-row px-4 py-2 space-x-3 items-center cursor-pointer hover:bg-gray-200" v-for="{ name, email, profile_photo_url } in emailAutoComplete" :key="email"
                                                                @click="chooseUser(email)">
                                                                <profile-photo :src="profile_photo_url" :alt="name" />
                                                                <div class="flex grow flex-col">
                                                                    <span class="text-sm">{{ name }}</span>
                                                                    <span class="text-gray-500 text-xs">{{ email }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4">
                                                        <text-area id="message" type="text" class="mt-1 w-full" :rows="4"
                                                            v-model="inviteMemberForm.message" placeholder="Additional Message (Optional)" />
                                                    </div>
                                                    <div class="my-4 flex justify-end">
                                                        <loading-button :loading="inviteMemberForm.processing">Send Invite</loading-button>
                                                    </div>
                                                </form>
                                                <hr>
                                                <div class="mt-4 flex items-center space-x-2">
                                                    <h3 class="font-bold">Invite via link</h3>
                                                    <Switch
                                                        @click="toogleInviteLinkStatus"
                                                        :class="project.invite_link_status ? 'bg-green-200' : 'bg-gray-300'"
                                                        class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                                                        <span class="sr-only">Use setting</span>
                                                        <span
                                                            aria-hidden="true"
                                                            :class="project.invite_link_status ? 'translate-x-5' : 'translate-x-0'"
                                                            class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow-lg transform ring-0 transition ease-in-out duration-200"
                                                        />
                                                    </Switch>
                                                </div>
                                                <transition leave-active-class="duration-200">
                                                    <div v-show="project.invite_link_status">
                                                        <transition enter-active-class="ease-out duration-300"
                                                            enter-from-class="opacity-0"
                                                            enter-to-class="opacity-100"
                                                            leave-active-class="ease-in duration-200"
                                                            leave-from-class="opacity-100"
                                                            leave-to-class="opacity-0">
                                                            <div class="mt-4">
                                                                <jet-input id="invite_link" type="text" class="mt-1 w-full" v-model="project.invite_link" placeholder="Invitte Link" readonly />
                                                            </div>
                                                        </transition>
                                                        <transition enter-active-class="ease-out duration-300"
                                                            enter-from-class="opacity-0"
                                                            enter-to-class="opacity-100"
                                                            leave-active-class="ease-in duration-200"
                                                            leave-from-class="opacity-100"
                                                            leave-to-class="opacity-0">
                                                            <div v-show="project.invite_link_status" class="mt-4 flex justify-end">
                                                                <jet-button @click="copyInviteLink" :disabled="disabledCopyButton">{{ copyButtonText }}</jet-button>
                                                            </div>
                                                        </transition>
                                                    </div>
                                                </transition>
                                            </template>
                                            <template #footer>
                                                <jet-secondary-button @click="closeAddMemberModal">Close</jet-secondary-button>
                                            </template>
                                        </jet-dialog-modal>
                                    </template>
                                    <template v-for="member in members" :key="member.id">
                                        <project-member :member="member" :project_auth="project.user.id" />
                                    </template>
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
    import LoadingButton from '@/Shared/LoadingButton.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import TextArea from '@/Shared/TextArea.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import ProfilePhoto from '@/Shared/ProfilePhoto.vue'
    import ProjectMember from '@/Shared/ProjectMember.vue'
    import { Switch } from '@headlessui/vue'
    import { PlusSmIcon } from '@heroicons/vue/outline'

    export default {
        components: {
            ProjectLayout,
            LoadingButton,
            JetButton,
            JetLabel,
            JetInput,
            JetInputError,
            JetSecondaryButton,
            TextArea,
            JetDialogModal,
            ProfilePhoto,
            ProjectMember,
            Switch,
            PlusSmIcon,
        },
        props: {
            project: Object,
            can: Object,
            errors: Object,
            members: Array,
        },
        data() {
            return {
                addMember: false,
                projectForm: useForm({
                    title: this.project.title,
                    description: this.project.description,
                }),
                inviteMemberForm: useForm({
                    email: null,
                    message: null,
                }),
                emailAutoComplete: [],
                disabledCopyButton: false,
                copyButtonText: 'copy link',
            }
        },

        methods: {
            showAddMemberModal() {
                this.addMember = true

                setTimeout(() => this.$refs.email.focus(), 250)
            },

            closeAddMemberModal() {
                this.addMember = false
                this.inviteMemberForm.email = null
                this.inviteMemberForm.message = null
                this.emailAutoComplete = []
            },

            async showAutoCompletePopUp() {
                try {
                    const { data } = await axios.post(
                        route('projects.registedEmailAddress', {'project': this.project}),
                        {
                            keyword: this.inviteMemberForm.email,
                        }
                    )
                    this.emailAutoComplete = data
                    await _.delay(() => this.emailAutoComplete = [], 5000, 'later');
                } catch (error) {
                    console.log(error);
                }
            },

            chooseUser(email) {
                this.inviteMemberForm.email = email
                this.emailAutoComplete = [];
            },

            sendInviteMail() {
                this.inviteMemberForm.post(route('projects.sendInviteMail', {'project': this.project}),{
                    preserveScroll: true,
                    onSuccess: () => {
                        this.closeAddMemberModal()
                    },
                    onFinish: () => {
                        this.inviteMemberForm.email = null
                        this.inviteMemberForm.message = null
                    },
                })
            },

            async toogleInviteLinkStatus() {
                try {
                    const { data } = await axios.post(
                        route('projects.updateInviteLinkStatus', {'project': this.project}),
                        {
                            invite_link_status: !this.project.invite_link_status,
                        }
                    )
                    this.project.invite_link_status = data.invite_link_enable
                    this.project.invite_link = data.link
                } catch (error) {
                    console.log(error);
                }
            },

            copyInviteLink() {
                var url = document.getElementById("invite_link")
                url.select()
                url.setSelectionRange(0, 99999)
                document.execCommand("copy")
                this.disabledCopyButton = true
                this.copyButtonText = 'copied!'
                setTimeout(() => {
                    this.disabledCopyButton = false
                    this.copyButtonText = 'copy link'
                }, 3000)
            },

        },
    }
</script>
