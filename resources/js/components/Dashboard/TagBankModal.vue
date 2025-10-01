<script setup lang="ts">
import { ref, onMounted } from 'vue';
import  Dialog  from 'primevue/dialog';
import Column   from 'primevue/column';
import DataTable from 'primevue/datatable';
import Button from 'primevue/button';

const visible = ref(false)
const childmodal = ref(false)

const data = {
    value: [
        {name: 'hello', code: 'NY'},
        {name: 'hello', code: 'NY'},
        {name: 'hello', code: 'NY'},
    ]
}
const muncul = ref(false)
function ismuncul() {
    muncul.value = !muncul.value
}
const expandedRow = ref<any[]>([]);
const color = ref(false)
function changeColor(){
    color.value = !color.value
}
</script>
<template>
<div>
    <Button label="hello" icon="pi pi-eye" @click="visible=true"></Button>
    <Dialog v-model:visible="visible" :header="'hello ini header'" modal class="w-[60rem] h-auto aspect-square">
        <h1>hello</h1>
        <div class="flex grid-cols-3 gap-2 *:rounded-2xl">
            <div class="flex min-w-[10rem]" @click="changeColor" :class="color? 'bg-red-400 col-span-2 duration-20000 transition ease-in-out' : 'bg-blue-400'"></div>
            <div class="flex bg-red-400 min-w-[10rem]"></div>
            <div class="bg-red-400 min-w-[10rem]"></div>
        </div>
        <Button label="modal dalam modal" icon="pi pi-eye" @click="childmodal=true"></Button>
        <Dialog v-model:visible="childmodal">    
            <DataTable :value="data.value" v-model:expanded-rows="expandedRow">
                <Column header="hello" field="name"></Column>
                <Column header="hay" field="code"></Column>
                <Column header="cobain">
                <template #body>
                    <Button icon="pi pi-eye" @click="ismuncul()"></Button>
                </template>
                </Column>
                <Column expander v-if="muncul" header="berhsil"></Column>
                <template #expansion="slotProps">
                    <h1>muncul coy</h1>
                </template>
            </DataTable>

        </Dialog>
    </Dialog>
</div>
</template>