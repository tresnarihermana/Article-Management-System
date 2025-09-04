<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputError from '@/components/InputError.vue';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import Swal from 'sweetalert2';
import Textarea from 'primevue/textarea';

const props = defineProps<{
    category?: { id: number; name: string; description: string }
}>();

const emit = defineEmits<{
    (e: 'created', category: any): void
    (e: 'edited', category: any): void
}>()

const visible = ref(false);
const isEdit = ref(!!props.category);

const form = useForm({
    name: '',
    description: '',
});

onMounted(() => {
    if (props.category) {
        form.name = props.category.name;
        form.description = props.category.description;
    }
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('categories.update', props.category!.id), {
            onError: () => {
                Swal.fire('Error', 'Failed to update category', 'error');
            },
            onSuccess: () => {
                visible.value = false;
                Swal.fire('Success', 'Category updated successfully', 'success');
                emit('edited', { ...form });
            }
        });
    } else {
        form.post(route('categories.store'), {
            onError: () => {
                Swal.fire('Error', 'Failed to create category', 'error');
            },
            onSuccess: () => {
                visible.value = false;
                Swal.fire('Success', 'Category created successfully', 'success');
                emit('created', { ...form });
                form.reset();
            }
        });
    }
};

const resetName = () => {
    form.name = '';
};
const resetDescription = () => {
    form.description = '';
};
</script>

<template>
    <div>
        <Button v-if="!isEdit" label="New Category" icon="pi pi-plus"  @click="visible = true" />
        <Button v-else icon="pi pi-pencil" rounded outlined @click="visible = true" />

        <Dialog v-model:visible="visible" modal :header="!isEdit ? 'Create Category' : 'Edit Category'"
            :style="{ width: '40rem' }" :breakpoints="{ '960px': '90vw', '640px': '100vw' }" dismissableMask>

            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <div class="grid gap-2">
                    <div class="flex items-center gap-1">
                        <label for="name">Category Name</label>
                        <Button icon="pi pi-sync" size="small" class="!bg-transparent !border-0 !text-gray-800"
                            @click="resetName" v-tooltip.right="'Reset Name'"></Button>
                    </div>
                    <InputText id="name" type="text" v-model="form.name" placeholder="Enter category name" required />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center gap-1">
                        <label for="description">Description</label>
                        <Button icon="pi pi-sync" size="small" class="!bg-transparent !border-0 !text-gray-800"
                            @click="resetDescription" v-tooltip.right="'Reset Description'"></Button>
                    </div>
                    <Textarea id="description" v-model="form.description" placeholder="Enter category description"
                        rows="4" autoResize class="w-full" />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <Button label="Cancel" severity="secondary" @click="visible = false" />
                    <Button label="Save" icon="pi pi-save" type="submit" />
                </div>
            </form>
        </Dialog>
    </div>
</template>
