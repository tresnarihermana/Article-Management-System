<script setup lang="ts">
import PageLayout from '@/layouts/PageLayout.vue';

</script>
<template>
  <PageLayout>
    <div class="relative mx-auto mt-30 max-w-2xl px-4 sm:px-6 md:max-w-4xl lg:max-w-5xl">
        <button class="flex items-center justify-center gap-1 rounded-full border-2 border-black px-2 py-1 shadow-lg">
            <i class="pi pi-sparkles"></i>
            <h1 class="text-xs font-medium">About us</h1>
        </button>
        <div class="mt-2 grid grid-cols-1 gap-2 *:overflow-hidden *:rounded-xl sm:grid-cols-2 md:grid-cols-3">
            <div class="col-span-full min-h-24 p-2 sm:col-span-2 sm:row-span-2 sm:p-4">
                <h1 class="text-3xl font-bold sm:text-4xl md:text-5xl">Lorem, ipsum dolor sit amet consectetur adipisicing elit.</h1>
            </div>

            <div class="min-h-24 p-2 sm:p-4">
                <span class="font-sans text-xs font-bold tracking-tighter text-gray-500"
                    >Lorem ipsum dolor sit amet consectetur adipisicing elit. Magnam exercitationem doloremque aliquid eveniet saepe accusantium
                    temporibus ipsam inventore.</span
                >
            </div>

            <div class="relative flex min-h-[8rem] flex-col items-center justify-center rounded-xl bg-black p-4 whitespace-nowrap sm:col-span-1">
                <span class="text-center text-4xl font-bold tracking-[.26rem] text-white sm:text-5xl">+192K</span>
                <span class="flex justify-items-start text-xs leading-4 text-gray-300">Published Articles</span>
            </div>

            <div
                class="relative flex min-h-[8rem] flex-col items-center justify-center !rounded-ss-[-9rem] !rounded-se-[7rem] !rounded-br-[1rem] !rounded-bl-[3rem] border bg-green-600 col-span-1 sm:col-span-2 md:col-span-1 lg:col-span-1 max-md:order-1 max-sm:min-h-[12rem]"
            >
                <span class="text-center text-4xl font-bold tracking-[.26rem] text-white sm:text-5xl">+192K</span>
                <span class="text-xs leading-4 text-gray-300">Published Articles</span>
            </div>

            <div class="relative flex min-h-[8rem] flex-col items-center justify-center rounded-xl bg-black p-4 whitespace-nowrap sm:col-span-1">
                <span class="text-center text-4xl font-bold tracking-[.26rem] text-white sm:text-5xl">+2K</span>
                <span class="flex justify-items-start text-xs leading-4 text-gray-300">Verified Authors</span>
            </div>
            <div class="relative flex min-h-[8rem] flex-col items-center justify-center rounded-xl bg-black p-4 whitespace-nowrap sm:col-span-1">
                <span class="text-center text-4xl font-bold tracking-[.26rem] text-white sm:text-5xl">+345K</span>
                <span class="flex justify-items-start text-xs leading-4 text-gray-300">Active Users</span>
            </div>
        </div>
        <div class="mt-12"></div>

        <button class="flex items-center justify-center gap-1 rounded-full border-2 border-black px-2 py-1 shadow-lg">
            <i class="pi pi-phone"></i>
            <h1 class="text-xs font-medium">Contact & More</h1>
        </button>

        <div class="mt-2 grid grid-cols-1 gap-2 *:overflow-hidden *:rounded-xl sm:grid-cols-2 md:grid-cols-3">
            <div class="col-span-full flex flex-col gap-4 rounded-xl border p-6 sm:col-span-2">
                <h2 class="text-2xl font-bold sm:text-3xl">Ayo Mulai Proyek Anda</h2>
                <input
                    type="text"
                    placeholder="Nama Lengkap"
                    class="rounded-lg border-2 border-gray-200 p-2 text-sm focus:border-black focus:ring-0"
                />
                <input
                    type="email"
                    placeholder="Email Anda"
                    class="rounded-lg border-2 border-gray-200 p-2 text-sm focus:border-black focus:ring-0"
                />
                <textarea
                    placeholder="Pesan Anda"
                    rows="4"
                    class="rounded-lg border-2 border-gray-200 p-2 text-sm focus:border-black focus:ring-0"
                ></textarea>
                <button class="w-full rounded-lg bg-black py-2 text-sm font-semibold text-white transition hover:bg-gray-800">Kirim Pesan</button>
            </div>

            <div class="flex flex-col gap-2 sm:col-span-1 sm:gap-4">
                <div class="flex h-1/2 min-h-[150px] flex-col justify-between rounded-xl border bg-gray-50 p-4">
                    <span class="text-xs font-bold tracking-wider text-gray-500 uppercase">Hubungi Kami</span>
                    <p class="text-sm font-semibold sm:text-base">hello@yourcompany.com</p>
                    <p class="text-sm font-semibold sm:text-base">+62 123 4567 890</p>
                    <p class="text-xs text-gray-600">Jl. Contoh No. 123, Jakarta, Indonesia</p>
                </div>

                <div class="flex h-1/2 min-h-[150px] flex-col justify-between rounded-xl bg-black p-4">
                    <span class="text-xs font-bold tracking-wider text-gray-400 uppercase">Ikuti Kami</span>
                    <div class="flex gap-4">
                        <a href="#" class="text-white hover:text-green-500"><i class="pi pi-instagram text-2xl"></i></a>
                        <a href="#" class="text-white hover:text-green-500"><i class="pi pi-twitter text-2xl"></i></a>
                        <a href="#" class="text-white hover:text-green-500"><i class="pi pi-linkedin text-2xl"></i></a>
                    </div>
                    <span class="text-xs text-gray-500">© 2024 YourCompany. All Rights Reserved.</span>
                </div>
            </div>
        </div>
    </div>
    </PageLayout>
</template>
