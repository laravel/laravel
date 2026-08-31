import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('../pages/Login.vue'),
      meta: { requiresAuth: false }
    },
    {
      path: '/',
      redirect: '/dashboard'
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../pages/Dashboard.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('../pages/AdminDashboard.vue'),
      meta: { requiresAuth: true, requiresRole: 'admin' }
    },
    {
      path: '/security',
      name: 'security',
      component: () => import('../pages/SecurityDashboard.vue'),
      meta: { requiresAuth: true, requiresRole: 'security' }
    },
    {
      path: '/responder',
      name: 'responder',
      component: () => import('../pages/ResponderDashboard.vue'),
      meta: { requiresAuth: true, requiresRole: 'responder' }
    },
  ]
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login');
  } else if (to.meta.requiresRole && authStore.user?.role !== to.meta.requiresRole) {
    next('/dashboard');
  } else if (to.path === '/login' && authStore.isAuthenticated) {
    next('/dashboard');
  } else {
    next();
  }
});

export default router;
