<template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex px-4 bg-white overflow-hidden shadow sm:rounded-lg h-12">
            <jet-nav-link :href="route('projects.overview', project_id)" :active="route().current('projects.overview', project_id)">
                Overview
            </jet-nav-link>
            <jet-nav-link :href="route('projects.show', project_id)" class="mx-4" :active="route().current('projects.show', project_id)">
                Tasks
            </jet-nav-link>
            <div class="my-auto">
                <span class="border-r"></span>
            </div>
            <div class="flex space-x-2">
                <div class="flex space-x-1 justify-around items-center ml-2">
                    <profile-photo v-for="member in members" :src="member.profile_photo_url" :alt="member.name" :key="member.id" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import JetNavLink from '@/Jetstream/NavLink.vue'
    import ProfilePhoto from '@/Shared/ProfilePhoto.vue'

    export default {
        components: {
            JetNavLink,
            ProfilePhoto,
        },
        props: {
            project_id: Number,
            members: Object,
        },
    }
</script>
