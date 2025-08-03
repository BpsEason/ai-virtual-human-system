import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Auth from '../views/Auth.vue'
import Dashboard from '../views/Dashboard.vue'
import Characters from '../views/Characters.vue'
import KnowledgeBase from '../views/KnowledgeBase.vue'
import ChatRoom from '../views/ChatRoom.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/auth',
      name: 'auth',
      component: Auth
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: Dashboard,
      meta: { requiresAuth: true }
    },
    {
      path: '/characters',
      name: 'characters',
      component: Characters,
      meta: { requiresAuth: true }
    },
    {
      path: '/knowledge-base',
      name: 'knowledge-base',
      component: KnowledgeBase,
      meta: { requiresAuth: true }
    },
    {
      path: '/chat/:characterId',
      name: 'chat-room',
      component: ChatRoom,
      meta: { requiresAuth: true }
    }
  ]
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  if (to.meta.requiresAuth && !token) {
    next({ name: 'auth' });
  } else {
    next();
  }
})

export default router
