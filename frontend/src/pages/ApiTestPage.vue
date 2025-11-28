<template>
  <div class="q-pa-lg">
    <q-card>
      <q-card-section>
        <div class="text-h6">API Test</div>
      </q-card-section>

      <q-card-section>
        <q-btn label="Test API Connection" @click="testApi" color="primary" />
      </q-card-section>

      <q-card-section>
        <div>Result:</div>
        <pre>{{ result }}</pre>
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { api } from 'boot/axios';

const result = ref('Belum dites');

async function testApi() {
  try {
    const res = await api.get('/test-api');
    result.value = JSON.stringify(res.data, null, 2);
  } catch (err) {
    result.value = err instanceof Error ? err.message : String(err);
  }
}
</script>
