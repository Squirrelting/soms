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
  CalculatorIcon,
  PowerIcon,
  IdentificationIcon,
} from "@heroicons/vue/24/solid";

const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
  localStorage.setItem("isSidebarCollapsed", isSidebarCollapsed.value);
};

const logOut = () => {
  router.post(route("logout"));
};

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
      (permission) => permission.name.toLowerCase() === input.toLowerCase()
    )
  );
}
</script>


<template>

  <main class="flex">
    <nav>
      <div id="sidebar" :class="[
          'bg-blue-200 text-gray-800 fixed md:relative z-20 transition-all duration-300 h-screen',
          isSidebarCollapsed ? 'w-16' : 'w-36'
        ]">
        <div>
          <div class="flex justify-center items-center">
            <img :class="[isSidebarCollapsed ? 'w-10 h-10' : 'w-20 h-20']" class="rounded-full transition-all duration-700 mt-5" src="/Images/SCNHS-Logo.png" alt="logo" />
          </div>
          <ul class="p-2">
            <MenuItem v-if="hasPermission('Manage Students')" :label="isSidebarCollapsed ? '' : 'Dashboard'" pattern="dashboard" route="dashboard">
              <HomeIcon class="h-4 w-4 text-gray-800" />
            </MenuItem>
            <MenuItem v-if="hasPermission('Manage Students')" :label="isSidebarCollapsed ? '' : 'Students'" pattern="students.index" route="students.index">
              <UsersIcon class="h-4 w-4 text-gray-800" />
            </MenuItem>
            <MenuItem v-if="hasPermission('Manage Students')" :label="isSidebarCollapsed ? '' : 'Reports'" pattern="reports.index" route="reports.index">
              <CalculatorIcon class="h-4 w-4 text-gray-800" />
            </MenuItem>
            <MenuItem v-if="hasPermission('Manage Students')" :label="isSidebarCollapsed ? '' : 'Good Moral'" pattern="print.index" route="print.index">
              <PencilIcon class="h-4 w-4 text-gray-800" />
            </MenuItem>
            <MenuItem v-if="hasPermission('Manage POD Users')" :label="isSidebarCollapsed ? '' : 'Roles'" pattern="users.roles-permissions.roles.index" route="users.roles-permissions.roles.index">
              <KeyIcon class="h-4 w-4 text-gray-800" />
            </MenuItem>

            <details close class="bg-gray-300 rounded-lg mt-4">
              <summary class="p-2 text-gray-800 text-xs cursor-pointer">Add Offense</summary>
              <ul class="pl-4">
                <li>
                  <MenuItem v-if="hasPermission('Manage POD Users')" :label="isSidebarCollapsed ? '' : 'Minor Offense'" pattern="minoroffense.index" route="minoroffense.index">
                    <IdentificationIcon class="h-4 w-4 text-gray-800" />
                  </MenuItem>
                </li>
                <li>
                  <MenuItem v-if="hasPermission('Manage POD Users')" :label="isSidebarCollapsed ? '' : 'Major Offense'" pattern="majoroffense.index" route="majoroffense.index">
                    <IdentificationIcon class="h-4 w-4 text-gray-800" />
                  </MenuItem>
                </li>
              </ul>
            </details>
          </ul>
        </div>
      </div>
    </nav>
    
    <header>
      <div class="navbar fixed z-10 bg-gray-500 text-gray-800 shadow-lg">
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
        <a class="text-2xl font-bold text-gray-800">SOMS</a>
      </div>

 <!-- User & Admin Options -->

        <!-- Admin Privilege Dropdown -->
        <div v-if="hasPermission('Manage POD Users')" class="dropdown dropdown-end">
          <div tabindex="0" role="button" class="btn m-1 text-gray-700 bg-blue-200">
            Admin Privilege
          </div>
          <ul class="dropdown-content menu bg-gray-300 text-gray-700 rounded-box z-[1] w-52 p-2 shadow">
            <li>
              <Link :href="route('user.index')" class="text-gray-700 hover:bg-gray-200">
                <UserPlusIcon class="h-4 w-4 text-black" /> User
              </Link>
            </li>
            <li>
              <Link :href="route('signatory.index')" class="text-gray-700 hover:bg-gray-200">
                <PencilIcon class="h-4 w-4 text-black" /> Signatory
              </Link>
            </li>
            <li>
              <Link :href="route('section.index')" class="text-gray-700 hover:bg-gray-200">
                <ChatBubbleLeftEllipsisIcon class="h-4 w-4 text-black" /> Add Section
              </Link>
            </li>
          </ul>
        </div>

        <!-- User Profile & Logout Dropdown -->
        <div class="dropdown dropdown-end mr-36">
          <div tabindex="0" role="button" class="btn m-1 text-gray-700 bg-gray-200">
            Hi, {{ $page.props.auth.user.name }}
          </div>
          <ul class="dropdown-content menu bg-gray-300 text-gray-700 rounded-box z-[1] w-52 p-2 shadow">
            <li>
              <Link :href="route('profile.edit')" class="text-gray-700 hover:bg-gray-200">
                <UserPlusIcon class="h-4 w-4 text-black" /> Profile
              </Link>
            </li>
            <li>
              <button @click="logOut()" class="text-gray-700 hover:bg-gray-200">
                <PowerIcon class="h-4 w-4 text-black" /> Log out
              </button>
            </li>
          </ul>
        </div>
      </div>
    </header>
    <section class="w-full mt-5 md:mt-20 lg:mt-10 flex-1 pt-7 bg-gray-200">
      <slot />
    </section>
  </main>
</template>
