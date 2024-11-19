<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="pt-4 py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex">
                    <h3 class="mb-2 font-semibold">Projects Recent</h3>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="h-20 text-center col-span-2 lg:col-span-4" v-if="in_projects.length == 0">Not Project yet</div>
                    <project-card v-for="project in in_projects" :key="project" :project="project" />
                </div>
            </div>
        </div>

        <div class="pt-4 py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h3 class="mb-2 font-semibold">Assigned to me</h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                        <h3 class="font-semibold px-4 pt-3 pb-2 border-b">Tasks</h3>
                        <table class="w-full">
                            <tr class="hover:bg-gray-100" v-for="task in in_tasks" :key="task.id">
                                <td class="flex justify-between border-t">
                                    <div class="flex flex-auto items-center">
                                        <!-- <input type="checkbox" name="" id="" class="mx-3 my-2 items-center border-1 rounded-full"> -->
                                        <Link class="flex flex-auto items-center px-3 py-2" :href="route('tasks.show', { task : task.id })">
                                            {{ task.title +" (Due at "+ task.due_at +")" }}
                                            <span v-if="task.due" class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full text-red-800 bg-red-200">Due</span>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="in_tasks.length == 0">
                                <td class="text-center px-2 leading-7 text-gray-500">There are no tasks assigned to you right now</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import ProjectCard from '@/Shared/ProjectCard.vue'

    export default {
        components: {
            Link,
            AppLayout,
            ProjectCard,
        },
        props: {
            in_tasks: Array,
            in_projects: Array,
        }
    }
</script>
