<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputError from '@/components/InputError.vue';
import Divider from 'primevue/divider';
import InputText from 'primevue/inputtext'
import Password from 'primevue/password';
import Checkbox from 'primevue/checkbox';
import Dialog from 'primevue/dialog';
import Message from 'primevue/message';
import Swal from 'sweetalert2'
import MultiSelect from 'primevue/multiselect';

const props = defineProps<{
    role: { id: number; name: string; permissions: { id: number; name: string }[] }
    permissions: { id: number; name: string }[]
}>();

const emit = defineEmits<{
    (e: 'created', role: any): void
    (e: 'edited', role: any): void
}>()

const visible = ref(false);
const isEdit = ref(!!props.role)
const form = useForm({
    name: '',
    permissions: [] as number[], // simpan id role yang dipilih
})

onMounted(() => {
    if (props.role) {
        form.name = props.role.name;
        form.permissions = props.role.permissions?.map((p: any) => p.id) ?? [];
    }
})

const submit = () => {
    if (isEdit.value) {
        form.put(route('roles.update', props.role.id), {
            onError: (errors) => {
                console.log(errors);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to update role',
                    icon: 'error'
                })
            },
            onSuccess: () => {
                visible.value = false;
                Swal.fire({
                    title: 'success',
                    text: 'Role update successfully',
                    icon: 'success'
                })
                emit('edited', { ...form });
            }
        });
    } else {
        form.post(route('roles.store'), {
            onError: (errors) => {
                console.log(errors);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to create role',
                    icon: 'error'
                })
            },
            onSuccess: () => {
                visible.value = false;
                Swal.fire({
                    title: 'Success',
                    text: 'Role created successfully',
                    icon: 'success'
                })
                emit('created', { ...form });
                form.reset();
            }
        });
    }
}
const loading = ref(false)
const resetPermission = () => {
    form.permissions = [];
    loading.value = true;
    setTimeout(() => {
        loading.value = false;
    }, 100);
}
const resetName = () => {
    form.name = '';
    loading.value = true;
    setTimeout(() => {
        loading.value = false;
    }, 100);
}
</script>

<template>
    <div>
        <!-- Trigger button -->
        <Button v-if="!isEdit" label="New" icon="pi pi-plus" class="mr-2" @click="visible = true" />
        <Button v-else icon="pi pi-pencil" rounded outlined @click="visible = true" />

        <!-- Modal Dialog -->
        <Dialog v-model:visible="visible" modal :header="!isEdit ? 'Create New Role' : 'Edit Role'" :style="{ width: '40rem' }"
            :breakpoints="{ '960px': '90vw', '640px': '100vw' }" dismissableMask>
            <form @submit.prevent="submit" class="flex flex-col gap-4">

                <!-- Role Name -->
                <div class="grid gap-2">
                    <div class="flex items-center gap-1">
                        <label for="name">Name</label>
                        <Button icon="pi pi-sync" size="small" class="!bg-transparent !border-0 !text-gray-800"
                            @click="resetName" :loading="loading" v-tooltip.right="'Reset Name'"></Button>
                    </div>
                    <InputText id="name" type="text" required autofocus v-model="form.name"
                        placeholder="Enter role name" />
                    <InputError :message="form.errors.name" />
                </div>

                <!-- Permissions -->
                <div class="grid gap-2">
                    <div class="flex items-center gap-1">
                        <label for="permissions">Permissions</label>
                        <Button icon="pi pi-sync" size="small" class="!bg-transparent !border-0 !text-gray-800"
                            @click="resetPermission" :loading="loading" v-tooltip.right="'Reset Permissions'"></Button>
                    </div>
                    <MultiSelect id="permissions" v-model="form.permissions" :options="permissions" optionLabel="name"
                        optionValue="id" display="chip" filter placeholder="Select permissions" class="w-full" :maxSelectedLabels="4"/>
                    <InputError :message="form.errors.permissions" />
                </div>

                <!-- Action buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <Button label="Cancel" severity="secondary" @click="visible = false" />
                    <Button label="Save" icon="pi pi-save" type="submit" />
                </div>
            </form>
        </Dialog>
    </div>
</template>
