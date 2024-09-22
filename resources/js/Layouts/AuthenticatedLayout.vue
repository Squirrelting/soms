<script setup>
import { ref } from "vue";
import MenuItem from "@/Components/MenuItem.vue";
import { Link, router } from '@inertiajs/vue3';
import Logo from '@/Pages/Images/SCNHS-Logo.png';

 
const showingNavigationDropdown = ref(false);

const logOut = () => {
    router.post(route('logout'));
}
</script>

<template>
    <header>
        <!--Nav-->
        <nav
            aria-label="menu nav"
            class="bg-gray-300 pt-2 md:pt-1 pb-1 px-1 mt-0 h-auto fixed w-full z-20 top-0"
        >
            <div class="flex flex-wrap items-center">
                <div
                    class="flex flex-shrink md:w-1/3 justify-center md:justify-start text-white"
                >
                    <a href="#" aria-label="Home">
                        <span class="text-xl pl-2"
                            ><i class="em em-grinning"></i
                        ></span>
                    </a>
                </div>

                <div
                    class="flex flex-1 md:w-1/3 justify-center md:justify-start text-white px-2"
                >
                    
                </div>

                <div class="dropdown dropdown-end">
    <!-- Button with gray color -->
    <div tabindex="0" role="button" class="btn m-1 text-gray-700 bg-gray-200">Hi, {{ $page.props.auth.user.name }}</div>
    
    <!-- Dropdown menu with gray background and text -->
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
                    class="bg-gray-300 shadow-xl h-20 fixed bottom-0 mt-12 md:relative md:h-screen z-10 w-full md:w-48 content-center"
                >
                    <div
                        class="md:mt-14 md:w-48 md:fixed md:left-0 md:top-0 content-center md:content-start text-left justify-between"
                    >
                        <ul
                            class="list-reset flex flex-row md:flex-col pt-3 md:py-3 px-1 md:px-2 text-center md:text-left"
                        >
                            <div class="flex justify-center items-center">
                                <img class="w-20 h-20 rounded-full" :src="Logo" alt="logo here">
                                </div>
                            <MenuItem label="Dashboard" pattern="dashboard" route="dashboard"/>
                            <MenuItem label="Register" pattern="register" route="register"/>
                            <MenuItem label="Signatory" pattern="signatory" route="signatorypage"/>
                            <MenuItem label="Roles" pattern="roles.index" route="users.roles-permissions.roles.index"/>
                            <MenuItem label="Permissions" pattern="permissions.index" route="users.roles-permissions.permissions.index"/>
                        </ul>
                    </div>
                </div>
            </nav>
            <section class="w-full">
                <div
                    class="mt-5 md:mt-20 lg:mt-10 flex-1 pt-5"
                >
                    <slot />
                </div>
            </section>
        </div>
    </main>
</template>
