<template>
  <q-page padding>
    <q-card class="q-pa-lg" flat bordered>
      <div class="text-h6 q-mb-md">Buat Kunjungan</div>

      <q-form @submit.prevent="createVisit">
        <!-- PILIH MRN -->
        <q-select
          v-model="selectedPatientId"
          :options="patientsOptions"
          option-label="medical_record_number"
          option-value="id"
          emit-value
          map-options
          label="Pilih No. Rekam Medis"
          filled
          class="q-mb-md"
          @update:model-value="onPatientSelected"
        />

        <!-- DETAIL PASIEN -->
        <q-card v-if="selectedPatient" flat bordered class="q-pa-md bg-grey-1 q-mb-md">
          <div class="text-subtitle2 q-mb-sm">Detail Pasien</div>

          <div><b>Nama:</b> {{ selectedPatient.name }}</div>
          <div><b>MRN:</b> {{ selectedPatient.medical_record_number }}</div>
          <div><b>Tanggal Lahir:</b> {{ selectedPatient.birth_date || '-' }}</div>
          <div><b>Alamat:</b> {{ selectedPatient.address || '-' }}</div>
          <div><b>No. HP:</b> {{ selectedPatient.phone || '-' }}</div>
        </q-card>

        <!-- FORM VISIT -->
        <q-input
          v-model="visitAt"
          type="datetime-local"
          label="Tanggal & Waktu Kunjungan"
          filled
          class="q-mb-md"
        />

        <q-input v-model="visitType" label="Jenis Kunjungan / Dokter" filled class="q-mb-md" />

        <q-input v-model="notes" label="Catatan Kunjungan" autogrow filled class="q-mb-lg" />

        <q-btn type="submit" color="primary" label="Simpan" />
      </q-form>
    </q-card>
  </q-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { api } from 'boot/axios';
import { Notify } from 'quasar';
import axios from 'axios';

/* ========================= TYPE ========================= */
type Patient = {
  id: number;
  name: string;
  medical_record_number: string;
  birth_date?: string;
  address?: string;
  phone?: string;
};

/* ========================= REFS ========================= */
const selectedPatientId = ref<number | null>(null);
const selectedPatient = ref<Patient | null>(null);

const visitAt = ref('');
const visitType = ref('');
const notes = ref('');

const patients = ref<Patient[]>([]);
const patientsOptions = ref<Patient[]>([]);

// Error extractor
function extractErrorMessage(err: unknown): string {
  if (axios.isAxiosError(err)) {
    const data = err.response?.data as { message?: string } | undefined;
    return data?.message || err.message || 'Terjadi kesalahan';
  }
  if (err instanceof Error) return err.message;
  return String(err);
}

/* ========================= FETCH PATIENTS ========================= */
onMounted(async () => {
  try {
    const res = await api.get('/patients');
    const data = Array.isArray(res.data) ? res.data : res.data.data;

    patients.value = data;
    patientsOptions.value = data;
  } catch (err) {
    Notify.create({ message: extractErrorMessage(err), color: 'negative' });
  }
});

// HANDLE SELECT PATIENT

const onPatientSelected = () => {
  selectedPatient.value = patients.value.find((p) => p.id === selectedPatientId.value) || null;
};

/* ========================= CREATE VISIT ========================= */
const createVisit = async () => {
  try {
    await api.post('/visits', {
      patient_id: selectedPatientId.value,
      visit_at: visitAt.value,
      visit_type: visitType.value,
      notes: notes.value,
    });

    Notify.create({ message: 'Kunjungan berhasil dibuat!', color: 'positive' });

    // reset
    selectedPatientId.value = null;
    selectedPatient.value = null;
    visitAt.value = '';
    visitType.value = '';
    notes.value = '';
  } catch (err) {
    Notify.create({ message: extractErrorMessage(err), color: 'negative' });
  }
};
</script>
