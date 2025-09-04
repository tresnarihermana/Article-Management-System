<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputError from '@/components/InputError.vue';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import Swal from 'sweetalert2';
import MultiSelect from 'primevue/multiselect';

const props = defineProps<{
    permission?: { id: number; name: string; roles: { id: number; name: string }[] }
    roles: { id: number; name: string }[]
}>();

const emit = defineEmits<{
    (e: 'created', permission: any): void
    (e: 'edited', permission: any): void
}>()

const visible = ref(false);
const isEdit = ref(!!props.permission);

const form = useForm({
    name: '',
    roles: [] as number[],
});

onMounted(() => {
    if (props.permission) {
        form.name = props.permission.name;
        form.roles = props.permission.roles?.map(r => r.id) ?? [];
    }
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('permissions.update', props.permission!.id), {
            onError: () => {
                Swal.fire('Error', 'Failed to update permission', 'error');
            },
            onSuccess: () => {
                visible.value = false;
                Swal.fire('Success', 'Permission updated successfully', 'success');
                emit('edited', { ...form });
            }
        });
    } else {
        form.post(route('permissions.store'), {
            onError: () => {
                Swal.fire('Error', 'Failed to create permission', 'error');
            },
            onSuccess: () => {
                visible.value = false;
                Swal.fire('Success', 'Permission created successfully', 'success');
                emit('created', { ...form });
                form.reset();
            }
        });
    }
};

const resetName = () => {
    form.name = '';
};
const resetRoles = () => {
    form.roles = [];
};
</script>

<template>
    <div>
        <Button v-if="!isEdit" label="New Permission" icon="pi pi-plus" class="mr-2" @click="visible = true" />
        <Button v-else icon="pi pi-pencil" rounded outlined @click="visible = true" />

        <Dialog v-model:visible="visible" modal :header="!isEdit ? 'Create Permission' : 'Edit Permission'"
            :style="{ width: '40rem' }" :breakpoints="{ '960px': '90vw', '640px': '100vw' }" dismissableMask>

            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <div class="grid gap-2">
                    <div class="flex items-center gap-1">
                        <label for="name">Permission Name</label>
                        <Button icon="pi pi-sync" size="small" class="!bg-transparent !border-0 !text-gray-800"
                            @click="resetName" v-tooltip.right="'Reset Name'"></Button>
                    </div>
                    <InputText id="name" type="text" v-model="form.name" placeholder="Enter permission name" required />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center gap-1">
                        <label for="roles">Assign to Roles</label>
                        <Button icon="pi pi-sync" size="small" class="!bg-transparent !border-0 !text-gray-800"
                            @click="resetRoles" v-tooltip.right="'Reset Roles'"></Button>
                    </div>
                    <MultiSelect id="roles" v-model="form.roles" :options="roles" optionLabel="name" optionValue="id"
                        display="chip" filter placeholder="Select roles" class="w-full" :maxSelectedLabels="4" />
                    <InputError :message="form.errors.roles" />
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <Button label="Cancel" severity="secondary" @click="visible = false" />
                    <Button label="Save" icon="pi pi-save" type="submit" />
                </div>
            </form>
        </Dialog>
    </div>
</template>
