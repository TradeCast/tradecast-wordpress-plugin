import { createRouter, createWebHashHistory, RouteRecordRaw } from 'vue-router';
import Galleries from '@/views/Galleries.vue';
import AddGalleryModal from '@/components/modals/galleries/AddGalleryModal.vue';
import AddCategoriesGalleryContent from '@/components/modals/galleries/content/AddCategoriesGalleryContent.vue';
import AddFeaturedGalleryContent from '@/components/modals/galleries/content/AddFeaturedGalleryContent.vue';
import AddLatestGalleryContent from '@/components/modals/galleries/content/AddLatestGalleryContent.vue';
import AddInterestsGalleryContent from '@/components/modals/galleries/content/AddInterestsGalleryContent.vue';
import AddPopularGalleryContent from '@/components/modals/galleries/content/AddPopularGalleryContent.vue';

/**
 * Array containing routes for the galleries modal.
 */
const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    component: Galleries,
    children: [
      {
        name: 'add-gallery',
        path: 'add',
        component: AddGalleryModal,
        children: [
          {
            name: 'add-categories-gallery',
            path: '',
            component: AddCategoriesGalleryContent,
          },
          {
            name: 'add-featured-gallery',
            path: 'featured',
            component: AddFeaturedGalleryContent,
          },
          {
            name: 'add-latest-gallery',
            path: 'latest',
            component: AddLatestGalleryContent,
          },
          {
            name: 'add-interests-gallery',
            path: 'interests',
            component: AddInterestsGalleryContent,
          },
          {
            name: 'add-popular-gallery',
            path: 'popular',
            component: AddPopularGalleryContent,
          },
        ],
      },
    ],
  },
];

const router = createRouter({
  history: createWebHashHistory(),
  routes,
});

export default router;
