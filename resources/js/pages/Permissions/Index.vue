<script setup lang="ts">
import { ref } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useToast } from "primevue/usetoast";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Toolbar from "primevue/toolbar";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Tag from "primevue/tag";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import Swal from "sweetalert2";

const props = defineProps<{
    permissions: any[],
    roles: any[],
    search?: string
}>();

const toast = useToast();
const dt = ref();
const permissions = ref(props.permissions);
const selectedPermissions = ref();
const lazyLoading = ref(false);
const totalRecords = ref(props.permissions.length);
const lazyParams = ref({
    first: 0,
    rows: 10,
    page: 0,
});
const filters = ref({
    global: { value: props.search ?? null, matchMode: FilterMatchMode.CONTAINS }
});

const onPage = (event: any) => {
    lazyParams.value = event;
    loadLazyData();
    selectedPermissions.value = [];
};

const onSort = (event: any) => {
    lazyParams.value = event;
    loadLazyData();
    selectedPermissions.value = [];
};

const loadLazyData = () => {
    lazyLoading.value = true;
    setTimeout(() => {
        let data = [...props.permissions];
        if (filters.value.global.value) {
            const search = filters.value.global.value.toLowerCase();
            data = data.filter((p: any) =>
                p.name.toLowerCase().includes(search)
            );
        }
        if (lazyParams.value.sortField) {
            data.sort((a: any, b: any) => {
                const field = lazyParams.value.sortField;
                let value1 = a[field];
                let value2 = b[field];
                if (field.includes('.')) {
                    const keys = field.split('.');
                    value1 = keys.reduce((obj, key) => obj?.[key], a);
                    value2 = keys.reduce((obj, key) => obj?.[key], b);
                }
                let result = 0;
                if (value1 == null && value2 != null) result = -1;
                else if (value1 != null && value2 == null) result = 1;
                else if (value1 == null && value2 == null) result = 0;
                else if (typeof value1 === 'string' && typeof value2 === 'string')
                    result = value1.localeCompare(value2);
                else result = value1 < value2 ? -1 : value1 > value2 ? 1 : 0;
                return (lazyParams.value.sortOrder ?? 1) * result;
            });
        }
        const start = lazyParams.value.first;
        const end = start + lazyParams.value.rows;
        permissions.value = data.slice(start, end);
        totalRecords.value = data.length;
        lazyLoading.value = false;
    }, 300);
};

loadLazyData();

const confirmDeletePermission = (perm: any) => {
    Swal.fire({
        icon: 'warning',
        title: 'Delete Permission?',
        text: perm.name,
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('permissions.destroy', perm.id), {
                preserveState: true,
                onSuccess: () => {
                    permissions.value = permissions.value.filter(val => val.id !== perm.id);
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'This permission has been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The permission was not deleted.',
                        icon: 'error',
                    });
                }
            });
        }
    });
};

const confirmDeleteSelected = () => {
    Swal.fire({
        icon: 'warning',
        title: 'Delete selected permissions?',
        text: 'Selected permissions will be permanently deleted',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            const ids = selectedPermissions.value.map((p: any) => p.id);
            router.delete(route('permissions.bulk-destroy', ids), {
                data: { ids },
                preserveState: true,
                onSuccess: () => {
                    permissions.value = permissions.value.filter(
                        (val) => !ids.includes(val.id)
                    );
                    selectedPermissions.value = null;
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Selected permissions have been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The permissions were not deleted.',
                        icon: 'error',
                    });
                }
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <div>
            <div class="card">
                <Toolbar class="mb-6">
                    <template #start>
                        <Button label="New" icon="pi pi-plus" class="mr-2" as="a" :href="route('permissions.create')" />
                        <Button label="Delete" icon="pi pi-trash" severity="danger" outlined
                            @click="confirmDeleteSelected" :disabled="!selectedPermissions || !selectedPermissions.length" />
                    </template>
                    <template #end>
                        <Button label="Export" icon="pi pi-upload" severity="secondary" @click="dt.exportCSV()" />
                    </template>
                </Toolbar>

                <DataTable ref="dt" v-model:selection="selectedPermissions" :value="permissions" dataKey="id"
                    :showGridlines="true"
                    :stripedRows="true"
                    :lazy="true" :paginator="true" :rows="10" :totalRecords="totalRecords"
                    :filters="filters"
                    @page="onPage"
                    @sort="onSort"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[5, 10, 25]"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} permissions"
                    class="container mx-auto">
                    <template #header>
                        <div class="flex flex-wrap gap-2 items-center justify-between">
                            <h4 class="m-0">Manage Permissions</h4>
                            <span class="p-input-icon-left">
                                <i class="pi pi-search mr-2" />
                                <InputText v-model="filters['global'].value" placeholder="Search..."  @input="loadLazyData" />
                            </span>
                        </div>
                    </template>

                    <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                    <Column field="id" header="ID" sortable style="min-width: 8rem"></Column>
                    <Column field="name" header="Name" sortable style="min-width: 12rem"></Column>
                    <Column field="roles" header="Roles" style="min-width: 14rem">
                        <template #body="slotProps">
                            <div class="flex flex-wrap gap-1">
                                <Tag v-for="role in slotProps.data.roles" :key="role.id" :value="role.name" severity="info" />
                            </div>
                        </template>
                    </Column>
                    <Column :exportable="false" style="min-width: 12rem">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" outlined rounded class="mr-2" as="a"
                                :href="route('permissions.edit', slotProps.data)" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger"
                                @click="confirmDeletePermission(slotProps.data)" />
                        </template>
                    </Column>
                    <template #empty>data not found</template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
