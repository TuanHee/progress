<template>
    <app-layout title="Projects">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Projects
            </h2>
        </template>

        <div class="pt-4 py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="flex justify-between items-center border-b px-6 pt-6 pb-4">
                        <input type="text" placeholder="Search..." v-model="searchForm.search"
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm lg:w-3/12 md:w-4/12 w-6/12" />
                        <button class="ml-2 text-gray-500 hover:text-black" @click="reset">Reset</button>
                        <jet-button class="ml-auto" @click="$inertia.get(route('projects.create'))">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add New
                        </jet-button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap">
                            <tr class="text-left font-bold">
                                <th class="px-6 pt-6 pb-4">Title</th>
                                <th class="px-6 pt-6 pb-4">Member</th>
                                <th class="px-6 pt-6 pb-4">Status</th>
                            </tr>
                            <tr v-for="project in projects.data" :key="project.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t">
                                    <Link class="px-6 py-2 flex items-center" :href="route('projects.show', project)">{{ project.title }}</Link>
                                </td>
                                <td class="border-t">
                                    <Link class="px-6 py-2 flex items-center" :href="route('projects.show', project)">
                                        <div class="flex space-x-1 divide-x items-center">
                                            <profile-photo :src="project.user.profile_photo_url" :alt="project.user.name" :size="9" />
                                            <div class="flex justify-around pl-1 -space-x-1">
                                                <profile-photo :src="member.profile_photo_url" :alt="member.name" v-for="member in project.joined_members.slice(0,3)" :size="8" :key="member.id" />
                                                <div v-if="project.all_joined_members_count > 3" class="flex rounded-full justify-center text-purple-600 bg-purple-100 items-center h-8 w-8">
                                                    <span>
                                                        ...
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </Link>
                                </td>
                                <td class="border-t">
                                    <Link class="px-6 py-2 flex items-center"
                                        :href="route('projects.show', project)">
                                        <div class="w-full">
                                            <progress-bar :percent="Math.ceil((
                                                project.tasks_count
                                                ? project.tasks_completed_count / project.tasks_count
                                                : 0
                                            ) * 100) + '%'" />
                                        </div>
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="projects.data.length === 0">
                                <td class="border-t px-6 py-4 text-center" colspan="3">No projects found.</td>
                            </tr>
                        </table>
                        <div class="border-t py-3 px-4">
                            <pagination :links="projects.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import { Link } from '@inertiajs/inertia-vue3'
    import JetButton from '@/Jetstream/Button'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import Pagination from '@/Shared/Pagination'
    import ProfilePhoto from "@/Shared/ProfilePhoto.vue"
    import ProgressBar from '@/Shared/ProgressBar.vue'
    import { throttle, pickBy, mapValues } from 'lodash'

    export default {
        components: {
            AppLayout,
            Link,
            JetButton,
            JetSecondaryButton,
            Pagination,
            ProfilePhoto,
            ProgressBar,
        },

        props: {
            filter: Object,
            projects: Object,
        },

        data() {
            return {
                searchForm: {
                    search: this.filter.search,
                }
            }
        },

        watch: {
            searchForm: {
                deep: true,
                handler: throttle(function () {
                    console.log(this.searchForm)
                    this.$inertia.get(
                        route('projects.index'),
                        pickBy(this.searchForm),
                        { preserveState: true },
                    )
                }, 150),
            },
        },

        methods: {
            reset() {
                this.searchForm = mapValues(this.searchForm, () => null)
            }
        }
    }
</script>
