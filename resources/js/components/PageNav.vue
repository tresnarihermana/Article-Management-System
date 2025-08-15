<template>
  <header class="bg-white">
    <nav
      class="mx-auto flex max-w-8xl items-center justify-between p-6 lg:px-8 fixed w-full z-20 top-0 start-0 bg-white/30 backdrop-blur-md border border-white/20 shadow-lg h-15 dark:bg-black/60 dark:border-black dark:text-gray-200"
      aria-label="Global">
      <div class="flex lg:flex-1">
        <a href="/" class="-m-1.5 p-1.5">
          <span class="sr-only">Article Management System</span>
          <div class="text-4xl font-bold ">
            AMS<span class="text-green-700 dark:text-green-500">.</span>
          </div>

        </a>
      </div>
      <div class="flex lg:hidden">
        <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
          @click="mobileMenuOpen = true">
          <span class="sr-only">Open main menu</span>
          <Bars3Icon class="size-6" aria-hidden="true" />
        </button>
      </div>
      <PopoverGroup class="hidden lg:flex lg:gap-x-12">
        <a :href="route('articles.list')"
          class="text-sm/6 font-semibold text-gray-900 dark:text-gray-200 hover:text-green-600 dark:hover:text-green-500 ">Articles</a>
        <Popover class="relative">
          <PopoverButton
            class="flex items-center gap-x-1 text-sm/6 font-semibold text-gray-900 dark:text-gray-200  hover:text-green-600 dark:hover:text-green-500">
            Categories
            <ChevronDownIcon class="size-5 flex-none text-gray-400" aria-hidden="true" />
          </PopoverButton>

          <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
            <PopoverPanel
              class="absolute left-30 top-7 z-10 mt-3 w-screen max-w-7xl -translate-x-1/2 overflow-hidden rounded-xl dark:backdrop-blur-md bg-white shadow-lg ring-1 ring-gray-900/5 dark:bg-black/50">


              <div class="p-8 grid grid-cols-3 gap-8">

                <div v-for="item in products" :key="item.name" class="flex gap-x-4">
                  <div
                    class="flex size-12 flex-none items-center justify-center rounded-lg bg-gray-50 group-hover:bg-white dark:bg-gray-800/50 dark:group-hover:bg-gray-800">
                    <component :is="item.icon" class="size-6 text-green-600 group-hover:text-indigo-600"
                      aria-hidden="true" />
                  </div>
                  <div>
                    <a :href="item.href"
                      class="block font-semibold text-gray-900 hover:text-green-600 dark:text-gray-300 dark:hover:text-green-500">
                      {{ item.name }}
                    </a>
                    <p class="mt-1 text-gray-600 text-sm dark:text-gray-500">{{ item.description }}</p>
                  </div>
                </div>
              </div>


              <div class="grid grid-cols-2 divide-x divide-gray-900/5 bg-gray-50 dark:bg-black/60">
                <a v-for="item in callsToAction" :key="item.name" :href="item.href"
                  class="flex items-center justify-center gap-x-2.5 p-4 text-sm font-semibold text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-black/70 dark:hover:text-green-500">
                  <component :is="item.icon" class="size-5 flex-none text-gray-400 dark:text-green-600 "
                    aria-hidden="true" />
                  {{ item.name }}
                </a>
              </div>
            </PopoverPanel>

          </transition>
        </Popover>

        <a href="#"
          class="text-sm/6 font-semibold text-gray-900 dark:text-gray-200 hover:text-green-600 dark:hover:text-green-500">Marketplace</a>
        <a href="#"
          class="text-sm/6 font-semibold text-gray-900 dark:text-gray-200 hover:text-green-600 dark:hover:text-green-500">About
          us</a>
      </PopoverGroup>
      <div class="hidden lg:flex lg:flex-1 lg:justify-end items-center gap-4">
        <SearchBar v-if="!$page.props.auth.user" />
        <a v-if="!$page.props.auth.user" :href="route('login')"
          class="text-sm/6 font-semibold text-gray-900 dark:text-gray-300">
          Log in <span aria-hidden="true">&rarr;</span>
        </a>
        <Popover v-if="$page.props.auth.user" class="relative hidden lg:flex lg:flex-1 lg:justify-end">
          <SearchBar />
          <PopoverButton class="flex items-center gap-x-2 text-sm/6 font-semibold text-gray-900 dark:text-gray-300">
            <img
              :src="$page.props.auth.user.avatar ? '/storage/' + $page.props.auth.user.avatar : 'https://ui-avatars.com/api/?name=' + $page.props.auth.user.username"
              alt="Profile Photo" class="h-8 w-8 rounded-full object-cover" />
            {{ $page.props.auth.user.username }}
            <ChevronDownIcon class="size-5 flex-none text-gray-400" aria-hidden="true" />
          </PopoverButton>

          <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
            <PopoverPanel
              class="absolute right-0 z-20 mt-12 w-48 rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5 dark:bg-black/60 dark:backdrop-blur-md">
              <div class="py-2">
                <a :href="route('profile.edit')"
                  class="block px-4 py-2 text-sm/6 text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-green-700">
                  Edit Profile
                </a>
                <a :href="route('dashboard')"
                  class="block px-4 py-2 text-sm/6 text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-green-700">
                  Dashboard
                </a>
                <button :href="route('logout')" @click="handleLogout" type="submit"
                  class="w-full text-left px-4 py-2 text-sm/6 text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-green-700">
                  Logout
                </button>
              </div>
            </PopoverPanel>
          </transition>
        </Popover>
        <ThemeMode></ThemeMode>
      </div>

    </nav>
    <Dialog class="lg:hidden" @close="mobileMenuOpen = false" :open="mobileMenuOpen">
      <div class="fixed inset-0 z-50" />
      <DialogPanel
        class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white p-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10 dark:bg-neutral-950">
        <div class="flex items-center justify-between">
          <a href="#" class="-m-1.5 p-1.5">
            <span class="sr-only">Article Management System</span>
            <div class="text-4xl font-bold dark:text-gray-200">
              AMS<span class="text-green-700 dark:text-green-500">.</span>
            </div>
          </a>
          <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700" @click="mobileMenuOpen = false">
            <span class="sr-only">Close menu</span>
            <XMarkIcon class="size-6" aria-hidden="true" />
          </button>
        </div>
        <div class="mt-6 flow-root">
          <div class="-my-6 divide-y divide-gray-500/10">
            <div class="space-y-2 py-6">
              <MobileSearchbar />
              <a href="#"
                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-green-700 ">Articles</a>
              <Disclosure as="div" class="-mx-3" v-slot="{ open }">
                <DisclosureButton
                  class="flex w-full items-center justify-between rounded-lg py-2 pr-3.5 pl-3 text-base/7 font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-green-700">
                  Product
                  <ChevronDownIcon :class="[open ? 'rotate-180' : '', 'size-5 flex-none']" aria-hidden="true" />
                </DisclosureButton>
                <DisclosurePanel class="mt-2 space-y-2">
                  <DisclosureButton v-for="item in [...products, ...callsToAction]" :key="item.name" as="a"
                    :href="item.href"
                    class="block rounded-lg py-2 pr-3 pl-6 text-sm/7 font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-green-700">
                    {{
                      item.name }}</DisclosureButton>
                </DisclosurePanel>
              </Disclosure>
              <a href="#"
                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-green-700">Marketplace</a>
              <a href="#"
                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-green-700">Company</a>
            </div>
            <div class="py-6">
              <a v-if="!$page.props.auth.user" :href="route('login')"
                class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-green-700">Log
                in</a>
              <div class="" v-if="$page.props.auth.user">
                <div class="flex items-center gap-3">
                  <Disclosure as="div" class="-mx-3" v-slot="{ open }">
                    <DisclosureButton
                      class="flex w-full items-center justify-between rounded-lg py-2 pr-3.5 pl-3 text-base/7 font-semibold text-gray-900 hover:bg-gray-50 dark:hover:bg-green-700">
                      <img
                        :src="$page.props.auth.user.avatar ? '/storage/' + $page.props.auth.user.avatar : 'https://ui-avatars.com/api/?name=' + $page.props.auth.user.username"
                        alt="Profile Photo" class="h-8 w-8 rounded-full object-cover" />
                      <span class="text-sm font-semibold text-gray-900 dark:text-gray-200 px-2"> {{
                        $page.props.auth.user.username }}</span>
                      <ChevronDownIcon :class="[open ? 'rotate-180' : '', 'size-5 flex-none', 'dark:text-gray-200']"
                        aria-hidden="true" />
                    </DisclosureButton>
                    <DisclosurePanel class="mt-2 space-y-2">
                      <DisclosureButton as="a" :href="route('profile.edit')"
                        class="block rounded-lg py-2 pr-3 pl-6 text-sm/7 font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-green-700">
                        Edit Proflie</DisclosureButton>
                      <DisclosureButton as="a" :href="route('dashboard')"
                        class="block rounded-lg py-2 pr-3 pl-6 text-sm/7 font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-green-700">
                        Dashboard</DisclosureButton>
                      <DisclosureButton as="button" :href="route('logout')" @click="handleLogout"
                        class="block w-full text-left rounded-lg py-2 pr-3 pl-6 text-sm/7 font-semibold text-red-900 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-green-700">
                        Logout</DisclosureButton>
                    </DisclosurePanel>
                  </Disclosure>
                </div>
              </div>
            </div>
          </div>
        </div>
      </DialogPanel>
    </Dialog>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import {
  Dialog,
  DialogPanel,
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Popover,
  PopoverButton,
  PopoverGroup,
  PopoverPanel,
} from '@headlessui/vue'
import {
  Bars3Icon,
  SquaresPlusIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'
import { ChatBubbleLeftRightIcon, ChevronDownIcon, ComputerDesktopIcon, ListBulletIcon, NewspaperIcon, PencilIcon, RocketLaunchIcon, SparklesIcon } from '@heroicons/vue/20/solid'
import ThemeMode from './ThemeMode.vue'
import SearchBar from '@/components/SearchBar.vue';
import MobileSearchbar from './MobileSearchbar.vue'

const products = [
  { name: 'Technology', description: 'Dapatkan informasi terbaru perkembangan teknologi', href: route('category.show', 'teknologi'), icon: ComputerDesktopIcon },
  { name: 'News', description: 'Kumpulan berita terkini paling faktual', href: route('category.show', 'lifestyle'), icon: NewspaperIcon },
  { name: 'Life Style', description: 'Tips & tricks buat kamu yang mau improvisasi diri', href: route('category.show', 'lifestyle'), icon: SparklesIcon },
  { name: 'Hobby', description: 'Dari diving di lautan hingga sky diving dari langit semua ada disini', href: route('category.show', 'hobi'), icon: RocketLaunchIcon },
  { name: 'Review', description: 'Lihat pengalaman terseru mengenai game, software dan film!', href: route('category.show', 'review'), icon: ChatBubbleLeftRightIcon },
  { name: 'See More', description: 'Lihat lebih banyak Kategori seru lainnya!', href: '#', icon: SquaresPlusIcon },
]
const callsToAction = [
  { name: 'Show All Articles', href: '#', icon: ListBulletIcon },
  { name: 'Become Writer', href: '#', icon: PencilIcon },
]
const mobileMenuOpen = ref(false)
const handleLogout = () => {
  router.post(route('logout'));
};
</script>