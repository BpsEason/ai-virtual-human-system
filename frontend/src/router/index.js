import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Auth from '../views/Auth.vue'
import AdminDashboard from '../views/Admin/AdminDashboard.vue'
import UserManagement from '../views/Admin/UserManagement.vue'
import Characters from '../views/Characters.vue'
import KnowledgeBase from '../views/KnowledgeBase.vue'
import ChatRoom from '../views/ChatRoom.vue'
import ConversationList from '../views/ConversationList.vue'
import { useAuth } from '../composables/useAuth';

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
      path: '/admin/dashboard',
      name: 'admin-dashboard',
      component: AdminDashboard,
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/users',
      name: 'user-management',
      component: UserManagement,
      meta: { requiresAuth: true, requiresAdmin: true }
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
      path: '/conversations',
      name: 'conversation-list',
      component: ConversationList,
      meta: { requiresAuth: true }
    },
    {
      path: '/chat/:conversationId',
      name: 'chat-room',
      component: ChatRoom,
      meta: { requiresAuth: true }
    }
  ]
})

router.beforeEach(async (to, from, next) => {
  const { isAuthenticated, isAdmin, fetchUser } = useAuth();
  
  // 檢查是否有 token，但 user 資訊尚未載入
  if (localStorage.getItem('token') && !isAuthenticated()) {
    await fetchUser();
  }

  if (to.meta.requiresAuth && !isAuthenticated()) {
    next({ name: 'auth' });
  } else if (to.meta.requiresAdmin && !isAdmin()) {
    // 沒有管理員權限則導向首頁
    next({ name: 'home' }); 
  } else {
    next();
  }
})

export default router
