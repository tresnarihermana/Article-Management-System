<template>
  <div class="p-6">
    <Button label="Buka Popover" @click="show($event)" />
    <Popover ref="op">
      <div style="padding: 1rem">
        <h4>Halo!</h4>
        <p>Popover berhasil muncul 🎉</p>
      </div>
    </Popover>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import Button from 'primevue/button';
import Popover from 'primevue/popover';

const op = ref<InstanceType<typeof Popover> | null>(null);

function show(event: MouseEvent) {
  op.value?.show(event);
}
</script>
