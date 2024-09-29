<script setup>
import { ref, onMounted } from "vue";
import MenuItem from "@/Components/MenuItem.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import {
  HomeIcon,
  UsersIcon,
  PencilIcon,
  UserPlusIcon,
  KeyIcon,
  MagnifyingGlassPlusIcon,
} from "@heroicons/vue/24/solid";

// Create a reactive variable for the sidebar state
const isSidebarCollapsed = ref(false);

// Function to toggle the sidebar state
const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
  // Store the sidebar state in local storage
  localStorage.setItem("isSidebarCollapsed", isSidebarCollapsed.value);
};

// Function to log out
const logOut = () => {
  router.post(route("logout"));
};

// On mounted, check local storage for the sidebar state
onMounted(() => {
  const storedState = localStorage.getItem("isSidebarCollapsed");
  if (storedState !== null) {
    isSidebarCollapsed.value = JSON.parse(storedState);
  }
});
</script>

<template>
  <header>
    <nav aria-label="menu nav" class="bg-gray-300 pt-2 md:pt-1 pb-1 px-1 mt-0 h-auto fixed w-full z-20 top-0">
      <div class="flex items-center justify-between">
        <div class="">
            <button
          class="hidden md:block p-2 text-gray-700 bg-gray-400"
          @click="toggleSidebar"
          :aria-expanded="isSidebarCollapsed"
          aria-controls="sidebar"
        >
          Hide/Show Sidebar
        </button>
        </div>

        <div class="dropdown dropdown-end">
          <div tabindex="0" role="button" class="btn m-1 text-gray-700 bg-gray-200">
            Hi, {{ $page.props.auth.user.name }}
          </div>
          <ul tabindex="0" class="dropdown-content menu bg-gray-300 text-gray-700 rounded-box z-[1] w-52 p-2 shadow">
            <li>
              <Link :href="route('profile.edit')" class="text-gray-700 hover:bg-gray-200">Profile</Link>
            </li>
            <li>
              <button @click="logOut()" class="text-gray-700 hover:bg-gray-200">Log out</button>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <div class="flex flex-col md:flex-row">
      <nav aria-label="alternative nav">
        <div
          id="sidebar"
          :class="[
            'bg-gray-300 shadow-xl fixed bottom-0 mt-12 md:relative md:h-screen z-10 w-full md:w-48 content-center transition-width duration-300',
            isSidebarCollapsed ? 'md:w-16' : 'md:w-48'
          ]"
        >
          <div class="md:mt-14 md:fixed md:left-0 md:top-0 content-center md:content-start text-left justify-between">
            <ul class="list-reset flex flex-row md:flex-col pt-3 md:py-3 px-1 md:px-2 text-center md:text-left">
              <div v-if="!isSidebarCollapsed" class="flex justify-center items-center">
                <img class="w-20 h-20 rounded-full" src="/Images/SCNHS-Logo.png" alt="logo" />
              </div>

              <!-- MenuItems -->
              <MenuItem
                v-if="$page.props.user.permissions.includes('Manage Students')"
                :label="isSidebarCollapsed ? '' : 'Dashboard'"
                pattern="dashboard"
                route="dashboard"
              >
                <HomeIcon class="h-5 text-black" />
              </MenuItem>

              <MenuItem
                v-if="$page.props.user.permissions.includes('Manage Students')"
                :label="isSidebarCollapsed ? '' : 'Students'"
                pattern="students.index"
                route="students.index"
              >
                <UsersIcon class="h-5 text-black" />
              </MenuItem>

              <MenuItem
                v-if="$page.props.user.permissions.includes('Manage Standard Users')"
                :label="isSidebarCollapsed ? '' : 'Register'"
                pattern="register"
                route="register"
              >
                <UserPlusIcon class="h-5 text-black" />
              </MenuItem>

              <MenuItem
                v-if="$page.props.user.permissions.includes('Manage Students')"
                :label="isSidebarCollapsed ? '' : 'Signatory'"
                pattern="signatory.index"
                route="signatory.index"
              >
                <PencilIcon class="h-5 text-black" />
              </MenuItem>

              <MenuItem
                v-if="$page.props.user.permissions.includes('Manage Standard Users')"
                :label="isSidebarCollapsed ? '' : 'Roles'"
                pattern="roles.index"
                route="users.roles-permissions.roles.index"
              >
                <KeyIcon class="h-5 text-black" />
              </MenuItem>

              <MenuItem
                v-if="$page.props.user.permissions.includes('Manage Standard Users')"
                :label="isSidebarCollapsed ? '' : 'Permissions'"
                pattern="permissions.index"
                route="users.roles-permissions.permissions.index"
              >
                <MagnifyingGlassPlusIcon class="h-5 text-black" />
              </MenuItem>
            </ul>
          </div>
        </div>
      </nav>

      <section class="w-full">
        <div class="mt-5 md:mt-20 lg:mt-10 flex-1 pt-5">
          <slot />
        </div>
      </section>
    </div>
  </main>
</template>

<style>
.transition-width {
  transition: width 0.3s ease;
}
</style>
