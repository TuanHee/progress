<template>
    <div class="flex justify-around w-full text-sm leading-10 text-left items-center hover:bg-gray-200"
        :class="{
            'text-gray-400': task.completed,
            'border-b': borderBottom
        }">
        <div class="pl-4 pr-2">
            <jet-checkbox class="rounded-full" v-model:checked="task.completed" :disabled="!can" />
        </div>
        <div class="flex-grow w-3/12 lg:w-auto border-r cursor-pointer truncate flex justify-between">
            <Link class="flex justify-between items-center truncate" :href="route('tasks.show', { task : task.id })">
                <span class="truncate">{{ task.title }}</span>
                <div class="flex items-center px-2" >
                    <div class="flex" v-if="task.comments_count > 0">
                        <span class="text-gray-400">
                            <ChatAltIcon class="h-4 w-4 flex items-center" />
                        </span>
                        <span class="text-xs px-1 text-gray-400">{{ task.comments_count }}</span>
                    </div>
                </div>
            </Link>
        </div>
        <div class="inline-flex border-r px-2 self-stretch">
            <div class="flex -space-x-1 items-center w-24 justify-center">
                <profile-photo :src="performer.profile_photo_url" :alt="performer.name" v-for="performer in task.performers" :key="performer.id"/>
            </div>
        </div>
        <div class="w-24 text-center border-r">
            <span v-if="task.priority == 'Low'"
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-200 text-green-800">Low</span>
            <span v-if="task.priority == 'Normal'"
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-200 text-yellow-800">Normal</span>
            <span v-if="task.priority == 'High'"
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-800">High</span>
        </div>
        <div class="w-24 text-center border-r">
            <span class="text-xs">{{ task.assigned_at }}</span>
        </div>
        <div class="w-24 text-center border-r">
            <span class="text-xs">{{ task.start_at }}</span>
        </div>
        <div class="w-24 text-center">
            <span class="text-xs" :class="{
                'text-red-500': task.due && !task.completed,
            }">{{ task.due_at }}</span>
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3'
    import JetCheckbox from '@/Jetstream/Checkbox.vue'
    import ProfilePhoto from '@/Shared/ProfilePhoto.vue'
    import { ChatAltIcon } from '@heroicons/vue/outline'
    import axios from 'axios'

    export default {
        components: {
            Link,
            JetCheckbox,
            ProfilePhoto,
            ChatAltIcon,
        },

        props: {
            task: Object,
            can: Boolean,
            borderBottom: Boolean,
        },

        watch: {
            'task.completed'() {
                console.log(this.task.completed)
                axios.post(route('tasks.updateStatus', {'task': this.task}),
                {
                    'completed': this.task.completed
                }).then((response) => {
                    console.log(response)
                }).catch((error) => {
                    console.log(error)
                })
            }
        }
    }
</script>
