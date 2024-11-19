<template>
    <Link :href="route('projects.show', { project : project.id })" class="card min-h-full">
        <progress-bar :percent="progress" />
        <div class="pb-2 flex justify-between">
            <div class="flex flex-col space-y-1">
                <h5 class="font-semibold">{{ project.title }}</h5>
                <div class="flex text-gray-500 items-center"
                    :class="{
                        'text-green-500': project.tasks_completed_count == project.tasks_count && project.tasks_count != 0
                    }">
                    <CheckCircleIcon class="w-5 h-5" />
                    <span class="ml-1">{{ (project.tasks_count != 0) ? project.tasks_completed_count + "/" + project.tasks_count : "-" }}</span>
                </div>
                <div class="flex -space-x-1">
                    <profile-photo :src="member.profile_photo_url" :alt="member.name" v-for="member in project.joined_members.slice(0,3)" :size="7" :key="member.id" />
                    <div v-if="project.all_joined_members_count > 3" class="flex rounded-full justify-center text-purple-600 bg-purple-100 items-center h-8 w-8">
                        <span>
                            ...
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right text-xs text-gray-600">{{ project.created_at }}</div>
    </Link>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3'
    import ProfilePhoto from '@/Shared/ProfilePhoto.vue'
    import ProgressBar from '@/Shared/ProgressBar.vue'
    import { CheckCircleIcon } from '@heroicons/vue/outline'

    export default {
        components: {
            Link,
            ProfilePhoto,
            ProgressBar,
            CheckCircleIcon,
        },

        props: {
            project: Object,
        },

        data() {
            return {
                progress: this.project.tasks_count
                    ? Math.ceil(this.project.tasks_completed_count / this.project.tasks_count * 100) +'%'
                    : 0 + '%',
            }
        },
    }
</script>
