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
import Popover from 'primevue/popover';
import SplitButton from "primevue/splitbutton";
import TieredMenu from 'primevue/tieredmenu';
import type { BreadcrumbItem } from '@/types';
const breadcrumbs : BreadcrumbItem[] = [
    {
        title: 'Articles',
        href: route('articles.index')
    },
    {
        title: 'Recycle Bin',
        href: route('articles.recycleBin')
    }
]

const props = defineProps<{
    articles: any[],
    tags: any[],
    categories: any[],
    search?: string
}>();

const toast = useToast();
const dt = ref();
const articles = ref(props.articles);
const categories = ref(props.categories);
const selectedArticles = ref();
const article = ref<any>({});
const submitted = ref(false);
const lazyLoading = ref(false);
const totalRecords = ref(props.articles.length);
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
    selectedArticles.value = [];
};


const onSort = (event: any) => {
    lazyParams.value = event;
    loadLazyData();
    selectedArticles.value = [];
};

const loadLazyData = () => {
    lazyLoading.value = true;
    setTimeout(() => {
        let data = [...props.articles];


        if (filters.value.global.value) {
            const search = filters.value.global.value.toLowerCase();
            data = data.filter((a: any) =>
                a.title.toLowerCase().includes(search) ||
                a.user.name.toLowerCase().includes(search)
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
        articles.value = data.slice(start, end);

        totalRecords.value = data.length;
        lazyLoading.value = false;
    }, 300);
};

loadLazyData();

const confirmRestoreArticle = (art:any) => {
    Swal.fire({
        title: 'Kembalikan Article ini?',
        html: 'anda yakin ingin mengembalikan article berjudul <br> <q><b>'+ art.title +'</b></q>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kembalikan', 
    }).then((result) => {
        if(result.isConfirmed){
            router.post(route('articles.restore' , art.id),{}, {
                preserveScroll: true,
                onSuccess: () => {
                  articles.value = articles.value.filter(val => val.id !== art.id);
                  Swal.fire({
                    title: 'Restored',
                    html: 'article berjudul <b><q>'+art.title+'</q></b> berhasil dikembalikan',
                    icon: 'success',
                    timer: 1000,
                    timerProgressBar: true,
                  })
                },
                onError: () => {
                  Swal.fire({
                    title: 'Failed!',
                    html: 'Operasi gagal dilaksanan'
                    ,icon: 'error',
                  })
                }
            })
        }
    })
}

const confirmDeleteArticle = (art: any) => {
    Swal.fire({
        icon: 'warning',
        title: 'Hapus Article ini?',
        text: art.title,
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('articles.forceDelete', art.id), {
                preserveState: true,
                onSuccess: () => {
                    articles.value = articles.value.filter(val => val.id !== art.id);
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'This article has been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The article was not deleted.',
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
        title: 'Hapus Articles terpilih?',
        html: `${selectedArticles.value.length} Artikel yang dipilih akan dihapus permanen`,
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            const ids = selectedArticles.value.map((a: any) => a.id);
            router.delete(route('articles.bulk.forceDelete', ids), {
                data: { ids },
                preserveState: true,
                onSuccess: () => {
                    articles.value = articles.value.filter(
                        (val) => !ids.includes(val.id)
                    );
                    selectedArticles.value = null;
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Selected articles have been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The articles were not deleted.',
                        icon: 'error',
                    });
                }
            });
        }
    });
};
const confirmRestoreSelected = () => {
    Swal.fire({
        icon: 'question',
        title: 'Kembalikan Articles terpilih?',
        html: `${selectedArticles.value.length} Artikel yang dipilih akan dikembalikan`,
        showCancelButton: true,
        confirmButtonText: 'Ya, kembalikan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#10b981'
    }).then((result) => {
        if (result.isConfirmed) {
            const ids = selectedArticles.value.map((a: any) => a.id);
            router.post(route('articles.bulk.restore', ids), {ids}, {
                preserveState: true,
                onSuccess: () => {
                    articles.value = articles.value.filter(
                        (val) => !ids.includes(val.id)
                    );
                    selectedArticles.value = null;
                    Swal.fire({
                        title: 'Restored!',
                        text: 'Selected articles have been restored.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The articles were not restored.',
                        icon: 'error',
                    });
                }
            });
        }
    });
};

const popoverRefs = ref({});

function showPopover(event, articleId) {
    if (popoverRefs.value[articleId]) {
        popoverRefs.value[articleId].show(event);
    }
}
function hidePopover(event, articleId) {
    if (popoverRefs.value[articleId]) {
        popoverRefs.value[articleId].hide(event);
    }
}
const getSeverity = (articles) => {
    switch (articles.status) {
        case 'published':
            return 'success';

        case 'pending':
            return 'secondary';

        case 'draft':
            return 'info';

        case 'rejected':
            return 'warn';

        default:
            return 'secondary';
    }
}

const menu = ref();
const items = [
    {
        label: 'Bulk Delete',
        icon: 'pi pi-trash',
        command: () => {
            confirmDeleteSelected()
        }
    },
    {
        label: 'Bulk Restore',
        icon: 'pi pi-history',
        command: () => {
            confirmRestoreSelected()
        }
    }
]
const toggle= (event) => {
    menu.value.toggle(event);
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div>
            <div class="card">
                <Toolbar class="mb-6">
                    <template #start>
                        <Button label="Action" icon="pi pi-ellipsis-v" iconPos="right" @click="toggle" :disabled="!selectedArticles || !selectedArticles.length"></Button>
                        <TieredMenu ref="menu" id="overlay_tmenu" :model="items" popup/>
                    </template>
                </Toolbar>

                <DataTable ref="dt" v-model:selection="selectedArticles" :value="articles" dataKey="id"
                    :showGridlines="true" :stripedRows="true" :lazy="true" :paginator="true" :rows="10"
                    :totalRecords="totalRecords" :filters="filters" @page="onPage" @sort="onSort"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[5, 10, 25]"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} articles"
                    class="container mx-auto">
                    <template #header>
                        <div class="flex flex-wrap gap-2 items-center justify-between">
                            <h4 class="m-0">Manage Articles</h4>
                            <span class="p-input-icon-left">
                                <i class="pi pi-search mr-2" />
                                <InputText v-model="filters['global'].value" placeholder="Search..."
                                    @input="loadLazyData" />
                            </span>
                        </div>
                    </template>

                    <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                    <Column field="title" header="Title" sortable style="min-width: 12rem">
                    </Column>
                    <Column field="user.name" header="Author" sortable style="min-width: 10rem"></Column>

                    <Column field="categories" header="Categories" sortable style="min-width: 14rem">
                        <template #body="slotProps">
                            <div class="flex flex-wrap gap-1">
                                <Tag v-for="cat in slotProps.data.categories" :key="cat.id" :value="cat.name"
                                    severity="info" />
                            </div>
                        </template>
                    </Column>

                    <Column field="tags" header="Tags" sortable style="min-width: 14rem">
                        <template #body="slotProps">
                            <div class="flex flex-wrap gap-1">
                                <Tag v-for="tag in slotProps.data.tags" :key="tag.id" :value="tag.name"
                                    severity="secondary" />
                            </div>
                        </template>
                    </Column>

                    <Column field="created_at" header="Created At" sortable style="min-width: 12rem"></Column>
                    <Column field="status" header="Status">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.status" :severity="getSeverity(slotProps.data)"
                                @click="showPopover($event, slotProps.data.id)" class="cursor-pointer" />
                            <Popover :ref="(el) => (popoverRefs[slotProps.data.id] = el)"
                                @mouseleave="hidePopover(slotProps.data.id)">
                                <div class="p-3 max-w-xs">
                                    <div class="flex flex-col items-center">
                                        <img class="rounded w-44 sm:w-64" :src="slotProps.data.cover
                                                ? `/storage/${slotProps.data.cover}`
                                                : `https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png`
                                            " :alt="slotProps.data.title" />
                                        <Tag :value="slotProps.data.status" :severity="getSeverity(slotProps.data)"
                                            class="absolute left-2 top-2" rounded />
                                    </div>
                                    <div class="pt-4">
                                        <div class="text-lg font-medium">
                                            {{
                                                slotProps.data.title.length > 40
                                                    ? slotProps.data.title.slice(0, 40) + "..."
                                            : slotProps.data.title
                                            }}
                                        </div>
                                        <div class="text-sm text-red-500 mt-2" v-if="slotProps.data.rejected_message">
                                            <span class="font-semibold">Rejected Reasons:</span>
                                            <p class="text-orange-500">
                                                {{ slotProps.data.rejected_message }}
                                            </p>
                                        </div>
                                        <div class="flex gap-2 mt-4">
                                            <Button icon="pi pi-eye" as="a" label="Preview" severity="secondary"
                                                class="flex-auto" :href="route('articles.show', slotProps.data.slug)" />
                                            <Button icon="pi pi-pencil" variant="outlined" as="a" severity="success"
                                                :href="route('articles.edit', slotProps.data.id)" />
                                        </div>
                                    </div>
                                </div>
                            </Popover>
                        </template>
                    </Column>
                    <Column :exportable="false" style="min-width: 12rem">
                        <template #body="slotProps">
                            <Button icon="pi pi-history" outlined rounded severity="success" class="mr-2"
                                @click="confirmRestoreArticle(slotProps.data)" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger"
                                @click="confirmDeleteArticle(slotProps.data)" />
                        </template>
                    </Column>
                      <template #empty>data not found</template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
