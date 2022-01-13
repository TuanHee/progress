<template>
    <nav class="bg-white shadow border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <Link :href="route('dashboard')">
                            <application-mark class="block h-9 w-auto" />
                        </Link>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <jet-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </jet-nav-link>
                        <jet-nav-link :href="route('projects.index')" :active="route().current('projects.index')">
                            Projects
                        </jet-nav-link>
                    </div>
                </div>

                <div class="flex">
                    <div class="flex items-center mr-4 sm:mr-0">
                        <div class="relative">
                            <jet-dropdown align="right" width="96">
                                <template #trigger>
                                    <button class="flex text-gray-500 rounded-full relative focus:outline-none focus:text-gray-700 transition">
                                        <BellIcon class="h-5 w-5" />
                                        <span v-if="haveNotice" class="text-xs absolute bg-blue-600 text-white px-1 rounded-full opacity-80 -bottom-2 -right-3">x{{ $page.props.notifications.length < 9 ? $page.props.notifications.length : '9+' }}</span>
                                        <span v-if="haveNotice" class="absolute  inline-flex rounded-full h-2 w-2 bg-blue-500 top-0 right-0">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                        </span>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="block px-4 py-2 text-sm text-gray-600 text-center">
                                        Notifications
                                    </div>
                                    <div :class="{
                                            'overflow-y-scroll max-h-96': $page.props.notifications.length > 5
                                        }">

                                        <notification v-for="notification in $page.props.notifications" :key="notification.id"
                                            :notification="notification" />

                                        <div v-if="!haveNotice"
                                            class="block w-full px-4 py-2 text-sm leading-5 text-gray-700 text-left hover:bg-gray-100 hover:cursor-pointer focus:outline-none focus:bg-gray-100 transition">
                                            No have notification.
                                        </div>

                                    </div>

                                    <div class="border-t border-gray-100"></div>

                                    <!-- Authentication -->
                                    <div class="flex justify-end px-2 pt-2 pb-1">
                                        <button @click="markAllNotificationAsRead" class="text-gray-700 text-sm hover:underline focus:text-gray-900"
                                            :disabled="!haveNotice">Mark all as read</button>
                                    </div>
                                </template>
                            </jet-dropdown>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Settings Dropdown -->
                        <div class="ml-3 relative">
                            <jet-dropdown align="right" width="48">
                                <template #trigger>
                                    <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                        <profile-photo :size="8" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                                    </button>

                                    <span v-else class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                            {{ $page.props.user.name }}

                                            <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <!-- Account Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Manage Account
                                    </div>

                                    <jet-dropdown-link :href="route('profile.show')">
                                        Profile
                                    </jet-dropdown-link>

                                    <jet-dropdown-link :href="route('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                                        API Tokens
                                    </jet-dropdown-link>

                                    <div class="border-t border-gray-100"></div>

                                    <!-- Authentication -->
                                    <form @submit.prevent="logout">
                                        <jet-dropdown-link as="button">
                                            Log Out
                                        </jet-dropdown-link>
                                    </form>
                                </template>
                            </jet-dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <jet-responsive-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                    Dashboard
                </jet-responsive-nav-link>

                <jet-responsive-nav-link :href="route('projects.index')" :active="route().current('projects.index')">
                    Projects
                </jet-responsive-nav-link>

            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex-shrink-0 mr-3" >
                        <profile-photo :size="10" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                    </div>

                    <div>
                        <div class="font-medium text-base text-gray-800">{{ $page.props.user.name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ $page.props.user.email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <jet-responsive-nav-link :href="route('profile.show')" :active="route().current('profile.show')">
                        Profile
                    </jet-responsive-nav-link>

                    <jet-responsive-nav-link :href="route('api-tokens.index')" :active="route().current('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                        API Tokens
                    </jet-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" @submit.prevent="logout">
                        <jet-responsive-nav-link as="button">
                            Log Out
                        </jet-responsive-nav-link>
                    </form>

                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3'
    import ApplicationMark from '@/Shared/ApplicationMark.vue'
    import JetDropdown from '@/Jetstream/Dropdown.vue'
    import JetDropdownLink from '@/Jetstream/DropdownLink.vue'
    import JetNavLink from '@/Jetstream/NavLink.vue'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink.vue'
    import ProfilePhoto from './ProfilePhoto.vue'
    import { BellIcon } from '@heroicons/vue/outline'
    import Notification from '@/Shared/Notification.vue'

    export default {
        components: {
            Link,
            ApplicationMark,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            JetResponsiveNavLink,
            ProfilePhoto,
            BellIcon,
            Notification,
        },

        data() {
            return {
                showingNavigationDropdown: false,
            }
        },

        computed: {
            haveNotice() {
                return this.$page.props.notifications.length != 0
            }
        },

        methods: {
            logout() {
                this.$inertia.post(route('logout'));
            },

            markAllNotificationAsRead() {
                this.$inertia.delete(route('notifications.markAllAsRead'));
            }
        },

        mounted() {
            const userId = this.$page.props.user.id

            Echo.private(`App.Models.User.${userId}`)
                .notification((notification) => {
                    this.$page.props.notifications.unshift(notification)

                    // var audio = new Audio('http://soundbible.com/mp3/analog-watch-alarm_daniel-simion.mp3');
                    // audio.play();
                })
        },

        unmounted() {
            // console.log(this.$page.props.user.id)
            if (this.$page.props.user != null) {
                const userId = this.$page.props.user.id
                Echo.leave(`App.Models.User.${userId}`)
            }

        },
    }
</script>
