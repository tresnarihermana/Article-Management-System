<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputError from '@/components/InputError.vue';
import Divider from 'primevue/divider';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Checkbox from 'primevue/checkbox';
import Dialog from 'primevue/dialog';
import Message from 'primevue/message';
import Swal from 'sweetalert2';
import { getInitials } from '@/composables/useInitials';
import Avatar from 'primevue/avatar';
const props = defineProps({
    roles: {
        type: Array,
        default: () => []
    },
    user: {
        type: Object,
        default: null
    }
})

const emit = defineEmits<{
    (e: 'created', user: any): void
    (e: 'edited', user: any): void
}>()

const visible = ref(false)
const isEdit = ref(!!props.user)

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    verified_email: false,
    roles: [],
    is_active: true,
})

onMounted(() => {
    if (props.user) {
        form.name = props.user.name
        form.username = props.user.username
        form.email = props.user.email
        form.verified_email = props.user.verified_email ?? false
        form.is_active = props.user.is_active ?? true
        form.roles = props.user.roles?.map((r: any) => r.id) ?? []
    }
})

const submit = () => {
    if (isEdit.value) {
        form.put(route('users.update', props.user.id), {
            onSuccess: () => {
                emit('edited', form.data())
                visible.value = false
                Swal.fire({ title: 'Success', text: 'User updated successfully', icon: 'success' })
            },
            onError: () => {
                Swal.fire({ title: 'Error', text: 'Failed to update user', icon: 'error' })
            },
        })
    } else {
        form.post(route('users.store'), {
            onSuccess: () => {
                emit('created', form.data())
                visible.value = false
                Swal.fire({ title: 'Success', text: 'User created successfully', icon: 'success' })
                form.reset()
            },
            onError: () => {
                Swal.fire({ title: 'Error', text: 'Failed to create user', icon: 'error' })
            },
        })
    }
}

const usernameWarning = ref('')
watch(() => form.username, (val) => {
    usernameWarning.value = /\s/.test(val) ? 'Username field cannot use spaces' : ''
})

const rules = reactive({
    minLength: false,
    hasUppercase: false,
    hasLowercase: false,
    hasNumber: false,
    hasSymbol: false,
})
function validatePassword() {
    const val = form.password
    rules.minLength = val.length >= 8
    rules.hasUppercase = /[A-Z]/.test(val)
    rules.hasLowercase = /[a-z]/.test(val)
    rules.hasNumber = /[0-9]/.test(val)
    rules.hasSymbol = /[^A-Za-z0-9]/.test(val)
}
watch(() => form.password, validatePassword)
</script>


<template>
    <div>
        <Button v-if="!isEdit" label="New" icon="pi pi-plus" @click="visible = true" />
        <Button v-else icon="pi pi-pencil" outlined rounded class="mr-2" @click="visible = true" />

        <Dialog v-model:visible="visible" modal :header="isEdit ? 'Edit User' : 'Create New User'"
            :style="{ width: '40rem' }" :breakpoints="{ '960px': '90vw', '640px': '100vw' }" dismissableMask>
            <template #header>
                <div class="flex flex-col" v-if="isEdit">
                    <div class="inline-flex items-center justify-center gap-2">
                        <Avatar :image="props.user
                            ? (props.user.avatar_url ?? 'https://ui-avatars.com/api/?name=' + getInitials(props.user.username) + '&background=random')
                            : 'https://ui-avatars.com/api/?name=U&background=random'" shape="circle" />

                        <span class="font-bold whitespace-nowrap">{{ props.user?.name }}</span>
                    </div>
                    <div class="mt-1">
                        <span class="text-sm text-gray-500"> Update This User Profile</span>
                    </div>
                </div>
                <div class="flex flex-col" v-else>  
                    <h1 class="text-xl font-bold">Create a New User</h1>
                </div>
            </template>
            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <div class="grid gap-2">
                    <label for="name">Fullname</label>
                    <InputText id="name" type="text" required autofocus v-model="form.name"
                        placeholder="Enter fullname" />
                    <InputError :message="form.errors.name" />
                </div>
                <div class="grid gap-2">
                    <label for="username">Username</label>
                    <InputText id="username" type="text" required v-model="form.username"
                        placeholder="Enter username" />
                    <InputError :message="form.errors.username ?? usernameWarning" />
                </div>
                <div class="grid gap-2">
                    <label for="email">Email address</label>
                    <InputText id="email" type="email" required v-model="form.email" placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>
                <div class="flex items-center gap-2">
                    <Checkbox inputId="verified_email" v-model="form.verified_email" :binary="true" />
                    <label for="verified_email">Verified This Email</label>
                </div>
                <div class="flex items-center gap-2">
                    <Checkbox inputId="is_active" v-model="form.is_active" :binary="true" />
                    <label for="is_active">Aktifkan user ini</label>
                </div>
                <div class="grid gap-2 w-full">
                    <label for="password">Password</label>
                    <Password id="password" v-model="form.password" placeholder="Password" toggleMask
                        inputClass="w-full" class="w-full">
                        <template #footer>
                            <Divider />
                            <ul class="pl-2 my-0 leading-normal text-sm">
                                <li v-if="!rules.minLength">Password minimal 8 karakter</li>
                                <li v-if="!rules.hasUppercase">Harus ada huruf besar</li>
                                <li v-if="!rules.hasLowercase">Harus ada huruf kecil</li>
                                <li v-if="!rules.hasNumber">Harus ada angka</li>
                                <li v-if="!rules.hasSymbol">Harus ada simbol (!@#$%^&*)</li>
                            </ul>
                        </template>
                    </Password>
                    <InputError :message="form.errors.password" />
                </div>
                <div class="grid gap-2">
                    <label for="password_confirmation">Confirm password</label>
                    <Password id="password_confirmation" v-model="form.password_confirmation"
                        placeholder="Confirm password" toggleMask inputClass="w-full" class="w-full"
                        :feedback="false" />
                    <InputError :message="form.errors.password_confirmation" />
                </div>
                <div class="grid gap-2">
                    <label for="roles">Roles</label>
                    <div class="flex flex-wrap gap-4">
                        <div v-for="role in props.roles" :key="role.id" class="flex items-center gap-2">
                            <Checkbox v-model="form.roles" :value="role.id" :inputId="'perm-' + role.id" />
                            <label :for="'perm-' + role.id">{{ role.name }}</label>
                        </div>
                    </div>
                    <Message v-if="form.errors.roles" severity="error" size="small" variant="simple">
                        {{ form.errors?.roles }}
                    </Message>
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <Button label="Cancel" severity="secondary" @click="visible = false" />
                    <Button :label="isEdit ? 'Update' : 'Save'" icon="pi pi-save" type="submit" />
                </div>
            </form>
        </Dialog>
    </div>
</template>
