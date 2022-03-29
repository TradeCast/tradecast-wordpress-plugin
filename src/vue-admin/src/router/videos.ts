import { createRouter, createWebHashHistory, RouteRecordRaw } from 'vue-router';
import Videos from '@/views/Videos.vue';
import AddVideoModal from '@/components/modals/videos/AddVideoModal.vue';

/**
 * Array containing routes for the videos modal.
 */
const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    component: Videos,
    children: [
      {
        path: '/add',
        component: AddVideoModal,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHashHistory(),
  routes,
});

export default router;
