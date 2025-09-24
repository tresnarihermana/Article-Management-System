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
import { useInitials } from "@/composables/useInitials";
import Select from "primevue/select";
import { can } from "@/lib/can";
import UserModal from "@/components/Dashboard/UserModal.vue";
import { type BreadcrumbItem } from '@/types';

const { getInitials } = useInitials();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: route('users.index'),
    },
];
const props = defineProps<{
    users: any[],
    roles: any[],
    search?: string,
    deletedCount: number,
}>();

const toast = useToast();
const dt = ref();
const users = ref(props.users);
const selectedUsers = ref();
const lazyLoading = ref(false);
const totalRecords = ref(props.users.length);
const lazyParams = ref({
    first: 0,
    rows: 10,
    page: 0,
});
const filters = ref({
    global: { value: props.search ?? null, matchMode: FilterMatchMode.CONTAINS },
    is_active: { value: null, matchMode: FilterMatchMode.EQUALS }
});

const onPage = (event: any) => {
    lazyParams.value = event;
    loadLazyData();
    selectedUsers.value = [];
};

const onSort = (event: any) => {
    lazyParams.value = event;
    loadLazyData();
    selectedUsers.value = [];
};


const loadLazyData = () => {
    lazyLoading.value = true;
    setTimeout(() => {
        let data = [...props.users];
        if (filters.value.global.value) {
            const search = filters.value.global.value.toLowerCase();
            data = data.filter((u: any) =>
                u.name.toLowerCase().includes(search) ||
                u.username.toLowerCase().includes(search) ||
                u.email.toLowerCase().includes(search)
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
        users.value = data.slice(start, end);
        totalRecords.value = data.length;
        lazyLoading.value = false;
    }, 300);
};

loadLazyData();

const confirmDeleteUser = (usr: any) => {
    Swal.fire({
        icon: 'warning',
        title: 'Hapus User ini?',
        html: 'Anda yakin ingin menghapus User dengan nama <q><b>'+usr.name+'</b></q>.',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('users.destroy', usr.id), {
                preserveState: true,
                onSuccess: () => {
                    users.value = users.value.filter(val => val.id !== usr.id);
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'This user has been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The user was not deleted.',
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
        title: 'Hapus Users terpilih?',
        text: 'User yang dipilih akan dipindahkan ke recycle bin',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            const ids = selectedUsers.value.map((u: any) => u.id);
            router.delete(route('users.bulk-destroy', ids), {
                data: { ids },
                preserveState: true,
                onSuccess: () => {
                    users.value = users.value.filter(
                        (val) => !ids.includes(val.id)
                    );
                    selectedUsers.value = null;
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Selected users have been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The users were not deleted.',
                        icon: 'error',
                    });
                }
            });
        }
    });
};
const statusOptions = ref([
    { label: 'Active', value: true, severity: 'success' },
    { label: 'Inactive', value: false, severity: 'warn' },
]);

function toggleStatus(id) {
    router.put(route('users.toggleStatus', id), {}, {
        onSuccess: () => {
            users.value = users.value.map(u => {
                if (u.id === id) {
                    return { ...u, is_active: !u.is_active };
                }
                return u;
            });
        },
        onError: (errors) => {
            console.error(errors)
        }
    })
}
const loading = ref(false);
const refresh = () => {
    router.visit(route('users.index'), {
  only: ['users'],
  preserveScroll: true,
})

    loading.value = true;
    setTimeout(() => {
        loading.value = false;
    }, 500);
}
const handleEdit = () => {
    loadLazyData();
    router.reload({ only: ['users'] }); 
}
const handleRecycle = () => {
    router.visit(route('users.recycleBin'), {
        only: ['users'],
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div>
            <div class="card">
                <Toolbar class="mb-6 ">
                    <template #start>
                        <UserModal :roles="roles" class="mr-2" @created="loadLazyData" :isEdit/>
                        <Button label="Delete" icon="pi pi-trash" severity="danger" outlined v-if="can('users.delete')"
                            @click="confirmDeleteSelected" :disabled="!selectedUsers || !selectedUsers.length" />
                    </template>
                    <template #end>
                      <Button label="Restore" v-if="deletedCount > 0" icon="pi pi-history" @click="handleRecycle" severity="contrast" :badge="deletedCount" class="mr-2"/>
                        <Button label="Export" zicon="pi pi-upload" severity="secondary" @click="dt.exportCSV()" />
                    </template>
                </Toolbar>

                <DataTable ref="dt" v-model:selection="selectedUsers" :value="users" dataKey="id" :showGridlines="true"
                    :stripedRows="true" :lazy="true" :paginator="true" :rows="10" :totalRecords="totalRecords"
                    :filters="filters" @page="onPage" @sort="onSort"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[5, 10, 25]"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} users"
                    class="container mx-auto">
                    <template #header>
                        <div class="flex flex-wrap gap-2 items-center justify-between">
                            <div class="flex items-center gap-3">
                                <h4 class="m-0">Manage Users</h4>
                                <Button icon="pi pi-sync" rounded @click="refresh" :loading="loading"
                                    v-tooltip.right="'Refresh Data'" class="!bg-transparent !border-0 !text-gray-800" />
                            </div>
                            <div>
                                <span class="p-input-icon-left">
                                    <i class="pi pi-search mr-2" />
                                    <InputText v-model="filters['global'].value" placeholder="Search..."
                                        @input="loadLazyData" />
                                </span>
                            </div>
                        </div>
                    </template>

                    <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                    <Column field="name" header="Name" sortable style="min-width: 12rem">
                        <template #body="slotProps">
                            <div class="flex items-center">
                                <img class="w-8 h-8 rounded-full mr-2"
                                    :src="slotProps.data.avatar ? `/storage/` + slotProps.data.avatar : 'https://ui-avatars.com/api/?name=' + getInitials(slotProps.data.username) + '&background=random'" />
                                {{ slotProps.data.name }}
                            </div>
                        </template>
                    </Column>
                    <Column field="username" header="Username" sortable style="min-width: 10rem"></Column>
                    <Column field="roles" header="Roles" style="min-width: 14rem">
                        <template #body="slotProps">
                            <div class="flex flex-wrap gap-1">
                                <Tag v-for="role in slotProps.data.roles" :key="role.id" :value="role.name"
                                    severity="info" />
                            </div>
                        </template>
                    </Column>
                    <Column field="email" header="Email" sortable style="min-width: 14rem"></Column>
                    <Column field="is_active" header="Status" style="min-width: 10rem" filterField="is_active" sortable
                        filterMatchMode="equals">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.is_active ? 'Active' : 'Inactive'"
                                :severity="slotProps.data.is_active ? 'success' : 'warn'"
                                :class="can('users.togglestatus') ? 'cursor-pointer' : 'cursor-not-allowed'"
                                v-tooltip.bottom="can('users.togglestatus') ? 'aktif/nonaktifkan akun' : null"
                                @click="can('users.togglestatus') ? toggleStatus(slotProps.data.id) : null" />
                        </template>
                        <template #filter="{ filterModel }">
                            <Select v-model="filterModel.value" :options="statusOptions" placeholder="Select One"
                                showClear>
                                <template #option="slotProps">
                                    <Tag :value="slotProps.option.label" :severity="slotProps.option.severity" />
                                </template>
                            </Select>
                        </template>
                    </Column>

                    <Column :exportable="false" style="min-width: 12rem"
                      >
                        <template #body="slotProps">
                            <div class="flex">
                                <UserModal :user="slotProps.data" :roles="roles"
                                    v-tooltip.bottom="`Edit User`"  @edited="handleEdit" 
                                    />
                                <Button icon="pi pi-trash" outlined rounded severity="danger"
                                    v-tooltip.bottom="`Delete User`"
                                    @click="confirmDeleteUser(slotProps.data)" />
                            </div>
                        </template>
                    </Column>
                <template #empty>data not found</template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
