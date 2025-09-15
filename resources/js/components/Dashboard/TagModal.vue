<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputError from '@/components/InputError.vue';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import Swal from 'sweetalert2';

const props = defineProps<{
    tag?: { id: number; name: string;  }
    category_id:number
}>();

const emit = defineEmits<{
    (e: 'created', tag: any): void
    (e: 'edited', tag: any): void
}>()

const visible = ref(false);
const isEdit = ref(!!props.tag);

const form = useForm({
    name: '',
    category_id: props.category_id,
});

onMounted(() => {
    if (props.tag) {
        form.name = props.tag.name;
        form.category_id= props.category_id
    }
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('tags.update', props.tag!.id), {
            onError: () => {
                Swal.fire('Error', 'Failed to update tag', 'error');
            },
            onSuccess: () => {
                visible.value = false;
                Swal.fire('Success', 'Tag updated successfully', 'success');
                emit('edited', { ...form });
            }
        });
    } else {
        form.post(route('tags.store'), {
            onError: () => {
                Swal.fire('Error', 'Failed to create tag', 'error');
            },
            onSuccess: () => {
                visible.value = false;
                Swal.fire('Success', 'Tag created successfully', 'success');
                emit('created', { ...form });
                form.reset();
            }
        });
    }
};

const resetName = () => {
    form.name = '';
};
</script>

<template>
    <div>
        <Button v-if="!isEdit" label="New Tag" icon="pi pi-plus" class="mr-2" @click="visible = true" />
        <Button v-else icon="pi pi-pencil" rounded outlined @click="visible = true" />

        <Dialog v-model:visible="visible" modal :header="!isEdit ? 'Create Tag' : 'Edit Tag'"
            :style="{ width: '30rem' }" :breakpoints="{ '960px': '90vw', '640px': '100vw' }" dismissableMask>

            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <div class="grid gap-2">
                    <div class="flex items-center gap-1">
                        <label for="name">Tag Name</label>
                        <Button icon="pi pi-sync" size="small" class="!bg-transparent !border-0 !text-gray-800"
                            @click="resetName" v-tooltip.right="'Reset Name'"></Button>
                    </div>
                    <InputText id="name" type="text" v-model="form.name" placeholder="Enter tag name" required />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <Button label="Cancel" severity="secondary" @click="visible = false" />
                    <Button label="Save" icon="pi pi-save" type="submit" />
                </div>
            </form>
        </Dialog>
    </div>
</template>
