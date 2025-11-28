import type { RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    redirect: '/login',
  },

  {
    path: '/login',
    component: () => import('layouts/BlankLayout.vue'),
    children: [{ path: '', component: () => import('pages/LoginPage.vue') }],
  },

  {
    path: '/app',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: 'indexpatients', component: () => import('pages/PatientIndex.vue') },
      { path: 'patients', component: () => import('pages/PatientPage.vue') },
      { path: 'visits', component: () => import('pages/VisitPage.vue') },
    ],
  },

  { path: '/:catchAll(.*)*', component: () => import('pages/ErrorNotFound.vue') },
];

export default routes;
