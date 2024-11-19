<template>
    <template v-if="['App\\Notifications\\TaskAssigned', 'App\\Notifications\\TaskDueSoon', 'App\\Notifications\\CompleteRequest'].includes(notification.type)">
        <jet-dropdown-link :href="notification.link">
            <div class="flex space-x-2">
                <profile-photo :src="notification.sender.profile_photo_url" :alt="notification.sender.name" />
                <div>
                    <div class="whitespace-normal">
                        <strong class="pb-1 text-gray-700 font-bold">{{ notification.sender.name }}</strong> <span v-html="notification.message"></span>
                    </div>
                    <span class="text-xs text-gray-600">{{ notification.created_at }}</span>
                </div>
            </div>
        </jet-dropdown-link>
    </template>
    <template v-else-if="notification.type == 'App\\Notifications\\ProjectInvite'">
        <div class="block w-full px-4 py-2 text-sm leading-5 text-gray-700 text-left hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition">
            <div class="flex space-x-2">
                <profile-photo :src="notification.sender.profile_photo_url" :alt="notification.sender.name" />
                <div>
                    <div class="whitespace-normal">
                        <strong class="pb-1 text-gray-700 font-bold">{{ notification.sender.name }}</strong> <span v-html="notification.message"></span>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <Link :href="notification.join_link" class="text-green-600 hover:underline rounded-sm">Join</Link>
                        <Link :href="notification.deny_link" method="delete" as="button"
                            type="button" class="text-red-600 px-2 hover:underline">Deny</Link>
                    </div>
                    <span class="text-xs text-gray-600">{{ notification.created_at }}</span>
                </div>
            </div>
        </div>
    </template>
</template>

<script>
    import JetDropdownLink from '@/Jetstream/DropdownLink.vue'
    import ProfilePhoto from './ProfilePhoto.vue'
    import { Link } from '@inertiajs/inertia-vue3'

    export default {
        components: {
            JetDropdownLink,
            ProfilePhoto,
            Link,
        },

        props: {
            notification: Object,
        },

    }
</script>
