<template>
  <q-layout view="lHh Lpr lFf">
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts"></script>

<style scoped>
/* optional styling supaya layout tidak kosong saat debug */
.q-page-container {
  min-height: 100vh;
}
</style>
