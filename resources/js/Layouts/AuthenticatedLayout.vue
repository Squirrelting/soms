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
  <main class="flex h-screen">
    <!-- Sidebar -->
    <aside :class="[
          'bg-blue-200 text-gray-800 fixed z-20 h-screen transition-all duration-300',
          isSidebarCollapsed ? 'w-16' : 'w-36'
        ]">
      <div class="flex justify-center items-center py-4">
        <img :class="[isSidebarCollapsed ? 'w-8 h-8' : 'w-16 h-16']" class="rounded-full transition-all duration-500" src="/Images/SCNHS-Logo.png" alt="logo" />
      </div>
      <ul class="px-2 space-y-2">
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
        <details v-if="hasPermission('Manage POD Users')" class="bg-gray-300 rounded-lg mt-4">
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
    </aside>

    <!-- Main Content -->
    <div :class="[
          'flex-1 transition-all duration-300',
          isSidebarCollapsed ? 'ml-16' : 'ml-36'
        ]">
      
      <!-- Navbar -->
      <header class="fixed top-0 left-0 right-0 bg-white transition-all duration-300 z-10"
              :class="[isSidebarCollapsed ? 'pl-16' : 'pl-36']">
        <div class="flex items-center h-16 px-4 text-blue-800">
          <button class="btn btn-square btn-ghost" @click="toggleSidebar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block h-5 w-5 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>
          <a class="ml-4 text-2xl font-bold text-blue-800">SOMS - Santiago City National High School</a>


          <!-- Admin Privilege Dropdown -->
          <div v-if="hasPermission('Manage POD Users')" class="dropdown dropdown-end ml-auto">
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
          <div class="dropdown dropdown-end">
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
      <!-- Content Section -->
      <section class="pt-16 px-4 bg-gray-100 min-h-screen w-full">
        <slot />
      </section>
    </div>
  </main>
</template>

