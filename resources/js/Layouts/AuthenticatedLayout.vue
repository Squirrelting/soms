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
  ChatBubbleLeftEllipsisIcon,
} from "@heroicons/vue/24/solid";

// Reactive state for sidebar collapsed/expanded
const isSidebarCollapsed = ref(false);

// Toggle function for collapsing sidebar
const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
  localStorage.setItem("isSidebarCollapsed", isSidebarCollapsed.value);
};

// Function to handle log out
const logOut = () => {
  router.post(route("logout"));
};

// Initialize sidebar state on mount
onMounted(() => {
  const storedState = localStorage.getItem("isSidebarCollapsed");
  if (storedState !== null) {
    isSidebarCollapsed.value = JSON.parse(storedState);
  }
});

function hasPermission(input) {
    const permissions = usePage().props.auth.user.roles[0].permissions;
    return (
        Array.isArray(permissions) &&
        permissions.some(
            (permission) =>
                permission.name.toLowerCase() === input.toLowerCase()
        )
    );
}


</script>

<template>
  <header>
    <div class="navbar fixed z-20 bg-gray-400" >
  <div class="flex-none">
    <button class="btn btn-square btn-ghost"
    @click="toggleSidebar"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        class="inline-block h-5 w-5 stroke-current">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 6h16M4 12h16M4 18h16"></path>
      </svg>
    </button>
  </div>
  <div class="flex-1">
    <a class="btn btn-ghost text-xl">SOMS</a>
  </div>
  <div class="flex-none">
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
</div>
  </header>

  <main>
    <div class="flex flex-col md:flex-row">
      <nav aria-label="alternative nav">
        <div
          id="sidebar"
          :class="[ 
            'bg-gray-300 shadow-xl fixed bottom-0 mt-12 md:relative md:h-screen z-10 content-center transition-all duration-300',
            isSidebarCollapsed ? 'md:w-16' : 'md:w-36'
          ]"
        >
          <div class="md:mt-14 md:fixed md:left-0 md:top-0 content-center md:content-start text-left justify-between">
            <ul class="list-reset flex flex-row md:flex-col pt-3 md:py-3 px-1 md:px-2 text-center md:text-left">


              <!-- Logo and Menu Items -->
              <div class="flex justify-center items-center mt-4">
                <img
                  :class="[isSidebarCollapsed ? 'w-10 h-10' : 'w-20 h-20']"
                  class="rounded-full transition-all duration-300"
                  src="/Images/SCNHS-Logo.png"
                  alt="logo"
                />
              </div>

              <MenuItem
                v-if="hasPermission('Manage Students')"
                :label="isSidebarCollapsed ? '' : 'Dashboard'"
                pattern="dashboard"
                route="dashboard"
              >
                <HomeIcon class="h-5 text-black" />
              </MenuItem>

              <MenuItem
                v-if="hasPermission('Manage Students')"
                :label="isSidebarCollapsed ? '' : 'Students'"
                pattern="students.index"
                route="students.index"
              >
                <UsersIcon class="h-5 text-black" />
              </MenuItem>

              <MenuItem
                v-if="hasPermission('Manage POD Users')"
                :label="isSidebarCollapsed ? '' : 'Register'"
                pattern="user.index"
                route="user.index"
              >
                <UserPlusIcon class="h-5 text-black" />
              </MenuItem>

              <MenuItem
                v-if="hasPermission('Manage Students')"
                :label="isSidebarCollapsed ? '' : 'Signatory'"
                pattern="signatory.index"
                route="signatory.index"
              >
                <PencilIcon class="h-5 text-black" />
              </MenuItem>

              <MenuItem
                v-if="hasPermission('Manage POD Users')"
                :label="isSidebarCollapsed ? '' : 'Roles'"
                pattern="users.roles-permissions.roles.index"
                route="users.roles-permissions.roles.index"
              >
                <KeyIcon class="h-5 text-black" />
              </MenuItem>

              <MenuItem
                v-if="hasPermission('Manage POD Users')"
                :label="isSidebarCollapsed ? '' : 'Add Section'"
                pattern="section.index"
                route="section.index"
              >
                <ChatBubbleLeftEllipsisIcon class="h-5 text-black" />
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
  transition: width 1s ease;
}
</style>
