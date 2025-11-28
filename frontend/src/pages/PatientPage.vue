<template>
  <q-page padding>
    <q-card class="q-pa-lg" flat bordered>
      <div class="text-h6 q-mb-md">Pendaftaran Pasien</div>

      <q-form @submit.prevent="createPatient">
        <q-input v-model="name" label="Patient Name" filled class="q-mb-md" />

        <q-input v-model="email" label="Email" filled class="q-mb-md" type="email" />

        <q-input v-model="phone" label="Phone Number" filled class="q-mb-md" />

        <q-select
          v-model="gender"
          :options="[
            { label: 'Male', value: 'Male' },
            { label: 'Female', value: 'Female' },
          ]"
          label="Gender"
          filled
          class="q-mb-md"
        />

        <q-input v-model="address" label="Address" filled class="q-mb-md" />

        <q-input filled v-model="birth_date" label="Birth Date" type="date" class="q-mb-lg" />

        <q-btn type="submit" color="primary" label="Simpan" />
      </q-form>
    </q-card>
  </q-page>
</template>
<script setup lang="ts">
import { ref } from 'vue';
import { api } from 'boot/axios';
import { Notify } from 'quasar';
import axios from 'axios';

const name = ref('');
const email = ref('');
const phone = ref('');
const gender = ref<'Male' | 'Female'>('Male');
const address = ref('');
const birth_date = ref('');

function extractErrorMessage(err: unknown): string {
  if (axios.isAxiosError(err)) {
    const data = err.response?.data as { message?: string } | undefined;
    return data?.message || err.message || 'Terjadi kesalahan';
  }

  if (err instanceof Error) {
    return err.message;
  }

  return String(err);
}

const createPatient = async () => {
  try {
    await api.post('/patients', {
      name: name.value,
      email: email.value,
      phone: phone.value,
      gender: gender.value,
      address: address.value,
      birth_date: birth_date.value,
    });

    Notify.create({
      message: 'Pasien berhasil didaftarkan!',
      color: 'positive',
    });

    // reset form
    name.value = '';
    email.value = '';
    phone.value = '';
    gender.value = 'Male';
    address.value = '';
    birth_date.value = '';
  } catch (error: unknown) {
    console.error('createPatient error', error);

    Notify.create({
      message: extractErrorMessage(error),
      color: 'negative',
    });
  }
};
</script>
