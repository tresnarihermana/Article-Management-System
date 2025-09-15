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
import { can } from "@/lib/can";
import CategoryTagModal from "@/components/Dashboard/CategoryModal.vue";
import TagModal from "@/components/Dashboard/TagModal.vue";
import TagBankModal from "@/components/Dashboard/TagBankModal.vue";

const breadcrumbs : BreadcrumbItem[] = [
    {
        title: 'Category',
        href: route('categories.index')
    }
]

const props = defineProps<{
    categories: any[],
}>();

const toast = useToast();
const dt = ref();
const categories = ref(props.categories);
const selectedCategories = ref();
const lazyLoading = ref(false);
const totalRecords = ref(props.categories.length);
const expandedRows = ref<any[]>([]);
const lazyParams = ref({
    first: 0,
    rows: 10,
    page: 0,
});
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const onPage = (event: any) => {
    lazyParams.value = event;
    loadLazyData();
    selectedCategories.value = [];
};

const onSort = (event: any) => {
    lazyParams.value = event;
    loadLazyData();
    selectedCategories.value = [];
};

const loadLazyData = () => {
    lazyLoading.value = true;
    setTimeout(() => {
        let data = [...props.categories];
        if (filters.value.global.value) {
            const search = filters.value.global.value.toLowerCase();
            data = data.filter((c: any) =>
                c.name.toLowerCase().includes(search) ||
                c.description?.toLowerCase().includes(search)
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
        categories.value = data.slice(start, end);
        totalRecords.value = data.length;
        lazyLoading.value = false;
    }, 300);
};

loadLazyData();

const confirmDeleteCategory = (cat: any) => {
    Swal.fire({
        icon: 'warning',
        title: 'Hapus Category ini?',
        text: cat.name,
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('categories.destroy', cat.id), {
                preserveState: true,
                onSuccess: () => {
                    categories.value = categories.value.filter(val => val.id !== cat.id);
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'This category has been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The category was not deleted.',
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
        title: 'Hapus Categories terpilih?',
        text: 'Category yang dipilih akan dihapus permanen',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            const ids = selectedCategories.value.map((c: any) => c.id);
            router.delete(route('categories.bulk-destroy', ids), {
                data: { ids },
                preserveState: true,
                onSuccess: () => {
                    categories.value = categories.value.filter((val) => !ids.includes(val.id));
                    selectedCategories.value = null;
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Selected categories have been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The categories were not deleted.',
                        icon: 'error',
                    });
                }
            });
        }
    });
};
function handleDelete(id) {
    Swal.fire({
        title: 'Delete this Tag?',
        text: 'Tag yang dipilih akan dihapus permanen',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
        
    }).then((result) => {
        if(result.isConfirmed) {
            router.delete(route('tags.destroy', id))
        }
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div>
            <div class="card">
                <Toolbar class="mb-6">
                    <template #start>
                        <CategoryTagModal :category="null" label="New" icon="pi pi-plus" class="mr-2"
                            @created="loadLazyData" />
                        <TagBankModal/>
                        <Button label="Delete" icon="pi pi-trash" severity="danger" outlined
                            @click="confirmDeleteSelected"
                            :disabled="!selectedCategories || !selectedCategories.length" />
                    </template>
                    <template #end>
                        <Button label="Export" icon="pi pi-upload" severity="secondary" @click="dt.exportCSV()" />
                    </template>
                </Toolbar>

                <DataTable ref="dt" v-model:selection="selectedCategories" v-model:expandedRows="expandedRows"
                    :value="categories" dataKey="id" :showGridlines="true" :stripedRows="true" :lazy="true"
                    :paginator="true" :rows="10" :totalRecords="totalRecords" :filters="filters" @page="onPage"
                    @sort="onSort"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[5, 10, 25]"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} categories"
                    class="container mx-auto">
                    <template #header>
                        <div class="flex flex-wrap gap-2 items-center justify-between">
                            <h4 class="m-0">Manage Categories</h4>
                            <span class="p-input-icon-left">
                                <i class="pi pi-search mr-2" />
                                <InputText v-model="filters['global'].value" placeholder="Search..."
                                    @input="loadLazyData" />
                            </span>
                        </div>
                    </template>

                    <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                    <Column expander style="width: 3rem" />
                    <Column field="id" header="ID" sortable style="min-width: 6rem"></Column>
                    <Column field="name" header="Name" sortable style="min-width: 12rem"></Column>
                    <Column field="description" header="Description" style="min-width: 14rem"></Column>
                    <Column field="articles" header="Used By" sortable style="min-width: 10rem">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.articles" severity="info" />
                        </template>
                    </Column>
                    <Column :exportable="false" style="min-width: 12rem">
                        <template #body="slotProps">
                            <div class="flex items-center gap-2">
                                <CategoryTagModal v-if="can('categories.edit')" :category="slotProps.data"
                                    @edited="loadLazyData" />
                                <Button v-if="can('categories.delete')" icon="pi pi-trash" outlined rounded
                                    severity="danger" @click="confirmDeleteCategory(slotProps.data)" />
                            </div>
                        </template>
                    </Column>

                    <template #expansion="slotProps">
                        <div class="p-4 border-t">
                            <h5 class="mb-3">Tags for {{ slotProps.data.name }}</h5>
                            <DataTable :value="slotProps.data.tags" dataKey="id" :showGridlines="true"
                                :stripedRows="true" class="w-full">
                                <Column field="id" header="ID" style="width: 4rem" />
                                <Column field="name" header="Name" style="min-width: 10rem" />
                                <Column field="slug" header="Slug" style="min-width: 10rem" />
                                <Column :exportable="false" header="Actions" style="min-width: 12rem">
                                    <template #body="tagProps">
                                        <div class="flex gap-2">
                                            <TagModal :tag="tagProps.data" :category_id="slotProps.data.id"
                                                @edited="loadLazyData" />
                                            <Button icon="pi pi-trash"  outlined rounded severity="danger"
                                                @click="handleDelete(tagProps.data.id)" />
                                        </div>
                                    </template>
                                </Column>
                            </DataTable>

                            <div class="mt-3">
                                <TagModal :tag="null" :category_id="slotProps.data.id" @created="loadLazyData" />
                            </div>
                        </div>
                    </template>


                    <template #empty>data not found</template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>