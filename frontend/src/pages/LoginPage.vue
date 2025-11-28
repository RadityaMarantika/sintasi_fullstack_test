<script setup lang="ts">
import { ref } from 'vue';
import { useQuasar } from 'quasar';
import { api } from 'boot/axios';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const router = useRouter();
const $q = useQuasar();

const doLogin = async () => {
  try {
    const res = await api.post('/login', {
      email: email.value,
      password: password.value,
    });

    const token = res.data.access_token;
    localStorage.setItem('token', token);
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    $q.notify({
      type: 'positive',
      message: 'Login berhasil!',
    });

    await router.push('/app/indexpatients');
  } catch (err) {
    console.error(err);
    $q.notify({
      type: 'negative',
      message: 'Gagal login! Periksa email & password.',
    });
  }
};
</script>

<template>
  <q-page class="row no-wrap full-height bg-grey-1">
    <!-- LEFT SIDE: Branding / Welcome -->
    <div class="col-6 flex flex-center bg-primary text-white">
      <div class="column items-center q-pa-xl text-center">
        <img src="#" alt="Logo" class="q-mb-xl" style="width: 120px" />
        <div class="text-h4 q-mb-md">Selamat Datang</div>
        <div class="text-subtitle2">Akses sistem manajemen pasien dengan cepat dan aman.</div>
      </div>
    </div>

    <!-- RIGHT SIDE: Login Form -->
    <div class="col-6 flex flex-center">
      <q-card flat bordered class="q-pa-xl" style="width: 380px; max-width: 90%">
        <div class="text-h5 q-mb-lg text-center">Login</div>

        <q-input
          filled
          v-model="email"
          label="Email"
          type="email"
          dense
          rounded
          clearable
          class="q-mb-md"
          :rules="[(val) => !!val || 'Email wajib diisi']"
        >
          <template v-slot:prepend>
            <q-icon name="email" />
          </template>
        </q-input>

        <q-input
          filled
          v-model="password"
          label="Password"
          type="password"
          dense
          rounded
          clearable
          :rules="[(val) => !!val || 'Password wajib diisi']"
        >
          <template v-slot:prepend>
            <q-icon name="lock" />
          </template>
        </q-input>

        <q-btn
          color="primary"
          label="Login"
          class="q-mt-lg full-width"
          unelevated
          @click="doLogin"
        />

        <div class="text-caption text-center q-mt-md">
          &copy; 2025 Sintasi Test. All Rights Reserved.
        </div>
      </q-card>
    </div>
  </q-page>
</template>

<style scoped>
.q-page {
  min-height: 100vh;
}
</style>
