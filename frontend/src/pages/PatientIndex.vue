<template>
  <q-page padding>
    <!-- HEADER -->
    <div class="row justify-between items-center q-mb-md">
      <div class="text-h6">Daftar Pasien</div>

      <div class="row q-gutter-sm">
        <q-btn color="primary" label="Tambah Pasien" @click="showAddPatient = true" />
        <q-btn
          color="accent"
          label="Buat Kunjungan"
          :disable="!selectedPatient"
          @click="showAddVisit = true"
        />
      </div>
    </div>

    <!-- TABLE -->
    <q-card flat bordered>
      <q-table
        :columns="columns"
        :rows="patients"
        row-key="id"
        selection="single"
        :loading="loading"
        v-model:selected="selectedRows"
        @row-click="selectPatient"
      >
        <template v-slot:loading>
          <q-inner-loading showing>
            <q-spinner size="50px" />
          </q-inner-loading>
        </template>
      </q-table>
    </q-card>

    <!-- ============ MODAL TAMBAH PASIEN ======================== -->

    <q-dialog v-model="showAddPatient" persistent>
      <q-card style="min-width: 450px">
        <q-card-section>
          <div class="text-h6">Tambah Pasien</div>
        </q-card-section>

        <q-card-section class="q-gutter-y-sm">
          <q-input v-model="formPatient.name" label="Nama" filled />
          <q-input v-model="formPatient.phone" label="No. HP" filled />
          <q-input v-model="formPatient.email" label="Email" filled />
          <q-input v-model="formPatient.address" label="Alamat" type="textarea" filled />

          <q-input v-model="formPatient.birth_date" type="date" label="Tanggal Lahir" filled />

          <q-select
            v-model="formPatient.gender"
            :options="['Male', 'Female']"
            label="Jenis Kelamin"
            filled
          />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Batal" color="negative" @click="showAddPatient = false" />
          <q-btn flat label="Simpan" color="positive" @click="submitPatient" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- ============ MODAL BUAT KUNJUNGAN ======================= -->

    <q-dialog v-model="showAddVisit" persistent>
      <q-card style="min-width: 480px">
        <q-card-section>
          <div class="text-h6">Buat Kunjungan</div>
        </q-card-section>

        <q-card-section class="q-gutter-y-sm">
          <!-- DETAIL PASIEN -->
          <q-card flat bordered class="q-pa-md bg-grey-1">
            <div><b>MRN:</b> {{ selectedPatient?.medical_record_number }}</div>
            <div><b>Name:</b> {{ selectedPatient?.name }}</div>
            <div><b>Birth Date:</b> {{ selectedPatient?.birth_date ?? '-' }}</div>
            <div><b>Phone:</b> {{ selectedPatient?.phone ?? '-' }}</div>
            <div><b>Gender:</b> {{ selectedPatient?.gender ?? '-' }}</div>
            <div><b>Address:</b> {{ selectedPatient?.address ?? '-' }}</div>
          </q-card>

          <q-input
            type="datetime-local"
            v-model="visitForm.visit_at"
            label="Tanggal Kunjungan"
            filled
          />

          <q-select
            v-model="visitForm.visit_type"
            :options="['General Check', 'Urgent Check']"
            label="Jenis Kunjungan"
            filled
          />

          <q-input v-model="visitForm.notes" type="textarea" label="Catatan" autogrow filled />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Batal" color="negative" @click="showAddVisit = false" />
          <q-btn flat label="Simpan" color="positive" @click="submitVisit" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { api } from 'boot/axios';
import { Notify, type QTableColumn } from 'quasar';
import axios from 'axios';

/* ========================= TYPES ========================= */
interface Patient {
  id: number;
  name: string;
  phone?: string | null;
  email?: string | null;
  birth_date?: string | null;
  gender?: string | null;
  address?: string | null;
  medical_record_number: string;
  visits_count: number;
}

/* ========================= TABLE ========================= */
const columns: QTableColumn[] = [
  { name: 'medical_record_number', label: 'MRN', field: 'medical_record_number', align: 'left' },
  { name: 'name', label: 'Nama', field: 'name', align: 'left' },
  { name: 'phone', label: 'No. HP', field: (row) => row.phone ?? '-', align: 'left' },
  {
    name: 'visits_count',
    label: 'Kunjungan',
    field: 'visits_count',
    align: 'center',
    sortable: true,
  },
];

/* ========================= STATE ========================= */
const patients = ref<Patient[]>([]);
const loading = ref(true);

const selectedRows = ref<Patient[]>([]);
const selectedPatient = computed(() => selectedRows.value[0] ?? null);

const showAddPatient = ref(false);
const showAddVisit = ref(false);

/* ========================= FORMS ========================= */
const formPatient = ref({
  name: '',
  phone: '',
  email: '',
  birth_date: '',
  gender: '',
  address: '',
});

const visitForm = ref({
  visit_at: '',
  visit_type: '',
  notes: '',
});

/* ========================= HELPERS ========================= */
function extractErrorMessage(err: unknown): string {
  if (axios.isAxiosError(err)) {
    return err.response?.data?.message || err.message;
  }
  return String(err);
}

/* ========================= LOAD PATIENTS ========================= */
interface RawPatient {
  id: number;
  name: string;
  phone?: string | null;
  email?: string | null;
  birth_date?: string | null;
  gender?: string | null;
  address?: string | null;
  medical_record_number: string;
  visits_count?: number | null;
}

async function loadPatients() {
  loading.value = true;
  try {
    const res = await api.get<RawPatient[] | { data: RawPatient[] }>('/patients');

    const raw: RawPatient[] = Array.isArray(res.data) ? res.data : res.data.data;

    patients.value = raw.map((p) => ({ ...p, visits_count: p.visits_count ?? 0 }));
  } catch (err: unknown) {
    Notify.create({ message: extractErrorMessage(err), color: 'negative' });
  } finally {
    loading.value = false;
  }
}

function selectPatient(evt: Event, row: unknown) {
  selectedRows.value = [row as Patient];
}

/* ========================= SUBMIT PATIENT ========================= */
async function submitPatient() {
  try {
    await api.post('/patients', formPatient.value);

    Notify.create({ message: 'Pasien berhasil ditambahkan!', color: 'positive' });
    showAddPatient.value = false;
    await loadPatients();

    formPatient.value = { name: '', phone: '', email: '', birth_date: '', gender: '', address: '' };
  } catch (err) {
    Notify.create({ message: extractErrorMessage(err), color: 'negative' });
  }
}

/* ========================= SUBMIT VISIT ========================= */
async function submitVisit() {
  if (!selectedPatient.value) return;

  try {
    await api.post('/visits', {
      patient_id: selectedPatient.value.id,
      ...visitForm.value,
    });

    Notify.create({ message: 'Kunjungan berhasil ditambahkan!', color: 'positive' });

    showAddVisit.value = false;
    await loadPatients();

    visitForm.value = { visit_at: '', visit_type: '', notes: '' };
  } catch (err) {
    Notify.create({ message: extractErrorMessage(err), color: 'negative' });
  }
}

onMounted(loadPatients);
</script>
