<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Toolbar from 'primevue/toolbar';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import { FilterMatchMode } from '@primevue/core/api';
import { can } from '@/lib/can';

const page = usePage();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: route('roles.index'),
    },
];

const props = defineProps<{
    roles: any[],
    permissions: any[],
    search?: string
}>();

const dt = ref();
const roles = ref(props.roles);
const selectedRoles = ref();
const lazyLoading = ref(false);
const totalRecords = ref(props.roles.length);
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
    selectedRoles.value = [];
};

const onSort = (event: any) => {
    lazyParams.value = event;
    loadLazyData();
    selectedRoles.value = [];
};

const loadLazyData = () => {
    lazyLoading.value = true;
    setTimeout(() => {
        let data = [...props.roles];
        if (filters.value.global.value) {
            const search = filters.value.global.value.toLowerCase();
            data = data.filter((r: any) =>
                r.name.toLowerCase().includes(search) ||
                r.permissions.some((p: any) => p.name.toLowerCase().includes(search))
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
        roles.value = data.slice(start, end);
        totalRecords.value = data.length;
        lazyLoading.value = false;
    }, 300);
};

loadLazyData();

const confirmDeleteRole = (role: any) => {
    Swal.fire({
        icon: 'warning',
        title: 'Hapus Role ini?',
        text: role.name,
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('roles.destroy', role.id), {
                preserveState: true,
                onSuccess: () => {
                    roles.value = roles.value.filter(val => val.id !== role.id);
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'This role has been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The role was not deleted.',
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
        title: 'Hapus Roles terpilih?',
        text: 'Role yang dipilih akan dihapus permanen',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            const ids = selectedRoles.value.map((r: any) => r.id);
            router.delete(route('roles.bulk-destroy', ids), {
                data: { ids },
                preserveState: true,
                onSuccess: () => {
                    roles.value = roles.value.filter(
                        (val) => !ids.includes(val.id)
                    );
                    selectedRoles.value = null;
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Selected roles have been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The roles were not deleted.',
                        icon: 'error',
                    });
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Roles" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <Button v-if="can('roles.create')" label="New" icon="pi pi-plus" class="mr-2" as="a" :href="route('roles.create')" />
                    <Button label="Delete" icon="pi pi-trash" severity="danger" outlined
                        @click="confirmDeleteSelected" :disabled="!selectedRoles || !selectedRoles.length" />
                </template>
            </Toolbar>

            <DataTable ref="dt" v-model:selection="selectedRoles" :value="roles" dataKey="id"
                :showGridlines="true"
                :stripedRows="true"
                :lazy="true" :paginator="true" :rows="10" :totalRecords="totalRecords"
                :filters="filters"
                @page="onPage"
                @sort="onSort"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords} roles"
                class="container mx-auto">
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h4 class="m-0">Manage Roles</h4>
                        <span class="p-input-icon-left">
                            <i class="pi pi-search mr-2" />
                            <InputText v-model="filters['global'].value" placeholder="Search..."  @input="loadLazyData" />
                        </span>
                    </div>
                </template>

                <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                <Column field="id" header="ID" sortable style="min-width: 6rem"></Column>
                <Column field="name" header="Name" sortable style="min-width: 12rem"></Column>
                <Column field="permissions" header="Permissions" style="min-width: 14rem">
                    <template #body="slotProps">
                        <div class="flex flex-wrap gap-1">
                            <Tag v-for="(permission, index) in slotProps.data.permissions.slice(0, 5)" :key="permission.id" :value="permission.name" severity="info" />
                            <Tag v-if="slotProps.data.permissions.length > 5" :value="'+' + (slotProps.data.permissions.length - 5) + ' more'" severity="secondary" />
                        </div>
                    </template>
                </Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button v-if="can('roles.edit')" icon="pi pi-pencil" outlined rounded class="mr-2" as="a"
                            :href="route('roles.edit', slotProps.data)" />
                        <Button v-if="can('roles.delete')" icon="pi pi-trash" outlined rounded severity="danger"
                            @click="confirmDeleteRole(slotProps.data)" />
                    </template>
                </Column>
                <template #empty>data not found</template>
            </DataTable>
        </div>
    </AppLayout>
</template>
