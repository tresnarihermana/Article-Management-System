<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import InputText from 'primevue/inputtext';
import { Transition } from 'vue';
import { ref, watch } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import Swal from 'sweetalert2';
import Textarea from 'primevue/textarea';


const props = defineProps<Props>();

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}
const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage();
const user = page.props.auth.user as User;

const form = useForm({
    name: user.name,
    username: user.username,
    email: user.email,  
    bio: user.bio,
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: "Process Success",
                text: "Profile Berhasil Diperbarui",
                icon: "success",

            });
        },
        onError: () => {
            Swal.fire({
                title: "Process Failed",
                text: form.errors.email ?? form.errors.name ?? form.errors.username,
                icon: "error"
            });
        }
    });
};

const photoPreview = ref(null);
const avatarform = useForm({
    avatar: null,
});
const processAvatar = (file: File) => {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif']
    if (!allowedTypes.includes(file.type)) {
        alert('Only JPG, PNG, and GIF files are allowed.')
        return
    }

    if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB.')
        return
    }

    const img = new Image()
    img.src = URL.createObjectURL(file)

    img.onload = () => {
        const canvas = document.createElement('canvas')
        const targetSize = 512 // ukuran square, bisa diganti 256 atau 1024
        canvas.width = targetSize
        canvas.height = targetSize

        const ctx = canvas.getContext('2d')
        if (ctx) {
            // Crop jadi square (ambil tengah)
            const minSide = Math.min(img.width, img.height)
            const sx = (img.width - minSide) / 2
            const sy = (img.height - minSide) / 2

            ctx.drawImage(img, sx, sy, minSide, minSide, 0, 0, targetSize, targetSize)

            canvas.toBlob((blob) => {
                if (blob) {
                    const croppedFile = new File([blob], file.name, { type: 'image/jpeg' })
                    avatarform.avatar = croppedFile
                    photoPreview.value = URL.createObjectURL(croppedFile)
                }
            }, 'image/jpeg', 0.9)
        }
    }
}

const selectNewPhoto = (event) => {
    const file = event.target.files[0]
    if (file) {
        processAvatar(file)
    }
}

const updateProfilePhoto = () => {
    avatarform.post(route('profile.avatar'), {
        preserveScroll: true,
        onSuccess: () => {
            avatarform.reset('avatar');
            photoPreview.value = null;
        },
    });
}
const usernameWarning = ref('');
watch(() => form.username, (val) => {
    if (/\s/.test(val)) {
        usernameWarning.value = 'Username field cannot use spaces'
    } else {
        usernameWarning.value = ''
    }
})
const flash = page.props?.flash?.message;
if (flash || !user.username || !user.name) {
    Swal.fire({
        icon: "info",
        title: "Complete Your Profile",
        text: "Please Complete your Profile info before proceed",
    });
}
const openFileInput = () => {
    fileInput.value.click();
}
const { getInitials } = useInitials();
const usercoverform = useForm({
    cover: null,
})

const fileInput = ref<HTMLInputElement | null>(null)
const previewUrl = ref<string | null>(null)

const onFileChange = (e: Event) => {
    const files = (e.target as HTMLInputElement).files
    if (files && files.length > 0) {
        processFile(files[0])
    }
}

const onDrop = (e: DragEvent) => {
    const files = e.dataTransfer?.files
    if (files && files.length > 0) {
        processFile(files[0])
    }
}

const onDragOver = () => {
    // Optional: styling efek saat dragover
}

const processFile = (file: File) => {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif']
    if (!allowedTypes.includes(file.type)) {
        alert('Only JPG, PNG, and GIF files are allowed.')
        return
    }

    if (file.size > 10 * 1024 * 1024) {
        alert('File size must be less than 10MB.')
        return
    }

    const img = new Image()
    img.src = URL.createObjectURL(file)

    img.onload = () => {
        const targetRatio = 820 / 312
        const canvas = document.createElement('canvas')
        let sx, sy, sw, sh

        if (img.width / img.height > targetRatio) {
            
            sh = img.height
            sw = sh * targetRatio
            sx = (img.width - sw) / 2
            sy = 0
        } else {
    
            sw = img.width
            sh = sw / targetRatio
            sx = 0
            sy = (img.height - sh) / 2
        }

      
        canvas.width = 1640
        canvas.height = 624

        const ctx = canvas.getContext('2d')
        if (ctx) {
            ctx.drawImage(img, sx, sy, sw, sh, 0, 0, canvas.width, canvas.height)
            canvas.toBlob((blob) => {
                if (blob) {
                    const croppedFile = new File([blob], file.name, { type: 'image/jpeg' })
                    usercoverform.cover = croppedFile
                    previewUrl.value = URL.createObjectURL(croppedFile)
                }
            }, 'image/jpeg', 0.9)
        }
    }
}

const UploadUserCover = () => {
    usercoverform.post(route('profile.cover'), {
        preserveScroll: true,
        onSuccess: () => {
            usercoverform.reset('cover');
            previewUrl.value = null;
        },
    });
}

</script>
<style>
.profile-user-img {
    cursor: pointer;
}
</style>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Profile settings" />

        <SettingsLayout>
            <Link href="/settings/password">Ganti Password</Link>

            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />
                <div class="mt-6">
                    <h2 class="text-lg font-medium text-gray-900">Foto Cover Profil</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Unggah atau perbarui foto cover profil Anda.
                    </p>
                </div>
                <form @submit.prevent="UploadUserCover" class="p-4">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Upload Gambar Cover</label>
                    <div class="w-full border-2 border-gray-300 border-dashed rounded-lg p-6" id="dropzone"
                        @dragover.prevent="onDragOver" @drop.prevent="onDrop">
                        <input for="cover" ref="fileInput" type="file" accept="image/*"
                            class="inset-0 w-full h-full opacity-0 z-50" @change="onFileChange" />
                        <div class="text-center pointer-events-none">
                            <img class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg"
                                alt="upload" />
                            <h3 class="mt-2 text-sm font-medium">
                                <label for="file-upload" class="relative cursor-pointer">
                                    <span>Drag and drop</span>
                                    <span class="text-green-600"> atau telusuri </span>
                                    <span>untuk mengunggah</span>
                                    <input id="file-upload" name="file-upload" type="file" class="sr-only" />
                                </label>
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF maksimal 10MB</p>
                        </div>
                        <img
          v-if="!previewUrl"
          :src="user?.cover"
          class="mt-4 mx-auto max-h-40"
          alt="Cover Saat Ini"
        />
        <img
          v-if="previewUrl"
          :src="previewUrl"
          class="mt-4 mx-auto max-h-40"
          alt="Preview Cover Baru"
        />
                    </div>
                    <div class="flex items-center gap-4 mt-5">
                        <Button  type="submit" :disabled="usercoverform.processing || !usercoverform.cover">
                            Unggah
                        </Button>

                        <p v-if="props.status === 'profile-cover-updated'" class="text-sm font-medium text-green-600">
                            Foto Cover profil berhasil diperbarui.
                        </p>
                    </div>
                </form>
                <div class="mt-6">
                    <h2 class="text-lg font-medium text-gray-900">Foto Profil</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Unggah atau perbarui foto profil Anda.
                    </p>
                </div>
                <div class="mt-4">
                    <Avatar class="w-32 h-32 rounded-full object-cover profile-user-img" @click="openFileInput">
                        <AvatarImage v-if="user.avatar || photoPreview" :src="photoPreview ||  user.avatar"
                            alt="Foto Profil" class="w-32 h-32 rounded-full object-cover profile-user-img"
                            @click="openFileInput" />
                        <AvatarFallback class="rounded-lg text-black dark:text-white">
                            {{ getInitials(user.username) }}
                        </AvatarFallback>
                    </Avatar>
                </div>

                <form @submit.prevent="updateProfilePhoto" class="mt-4 space-y-4">
                    <div>
                        <input ref="fileInput" id="avatar" type="file" accept="image/*" class="mt-1 block w-full"
                            @change="selectNewPhoto" />
                        <InputError class="mt-2" :message="avatarform.errors.avatar" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="avatarform.processing || !avatarform.avatar">
                            Unggah
                        </Button>

                        <p v-if="props.status === 'profile-photo-updated'" class="text-sm font-medium text-green-600">
                            Foto profil berhasil diperbarui.
                        </p>
                    </div>
                </form>
                <form @submit.prevent="submit" class="space-y-6">

                    <div class="grid gap-2">
                        <Label for="name">fullname</Label>
                        <InputText id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name"
                            autofocus="true" placeholder="enter your fullname" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="username">Username</Label>
                        <InputText id="username" class="mt-1 block w-full" v-model="form.username" required
                            autocomplete="username" placeholder="enter your username" />
                        <InputError class="mt-2" :message="form.errors.username ?? usernameWarning" />
                        <!-- <p v-if="usernameWarning" class="text-sm text-red-600 mt-1">
                            {{ usernameWarning }}
                        </p> -->
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <InputText id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                            autocomplete="username" placeholder="Email address" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link :href="route('verification.send')" method="post" as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500">
                            Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label for="bio">Bio Profile</Label>
                        <Textarea id="bio" class="mt-1 block w-full" v-model="form.bio" autocomplete="false"
                            placeholder="Lets people now a bit of you!" rows="5" cols="30" autoResize />
                        <InputError class="mt-2" :message="form.errors.bio" />
                    </div>
                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
