<template>
    <app-layout :title="project.title">

        <template #header>
            <h2 class="flex font-semibold text-xl text-gray-800 leading-tight">
                <Link class="text-primary" :href="route('projects.index')">Projects</Link>
                <span class="text-primary mx-2 font-medium">/</span> {{ project.title }}
            </h2>
        </template>

        <project-nav :project_id="project.id" :members="project_members" />

        <slot />

    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import { Link } from '@inertiajs/inertia-vue3'
    import ProjectNav from '@/Shared/ProjectNav.vue'

    export default {
        components: {
            AppLayout,
            Link,
            ProjectNav,
        },

        props: {
            project: Object,
            project_members: Array,
        },

    }
</script>
