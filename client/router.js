import Vue from 'vue'
import Router from 'vue-router'
import { scrollBehavior } from '~/utils'

Vue.use(Router)

const page = path => () => import(`~/pages/${path}`).then(m => m.default || m)

const routes = [
  { path: '/', name: 'index', component: page('Index.vue') },
  { path: '/login', name: 'login', component: page('auth/Login.vue') },
  { path: '/password/reset', name: 'password.request', component: page('auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('auth/verification/resend.vue') },
  { path: '/activate', name: 'activate', component: page('auth/Activate.vue') },

  { path: '/dashboard', name: 'dashboard', component: page('Dashboard.vue') },
  { path: '/transactions', name: 'transaction', component: page('transaction/Transaction.vue') },
  { path: '/transactions/approved', name: 'transaction_approved', component: page('transaction/Transaction.vue') },
  { path: '/transaction/create', name: 'transaction.create', component: page('transaction/CreateTransaction.vue') },
  { path: '/daily_transactions', name: 'daily_transaction', component: page('transaction/DailyTransaction.vue') },
  { path: '/companies', name: 'company', component: page('Company.vue') },
  { path: '/categories', name: 'category', component: page('Category.vue') },
  { path: '/accounts', name: 'account', component: page('Account.vue') },
  { path: '/users', name: 'user', component: page('User.vue') },
  { path: '/advanced_delete', name: 'advanced_delete', component: page('AdvancedDelete.vue') },
  { path: '/audit', name: 'audit', component: page('Audit.vue') },
  { path: '/site_status', name: 'site_status', component: page('SiteStatus.vue') },
  {
    path: '/settings',
    component: page('settings/Index.vue'),
    children: [
      { path: '', redirect: { name: 'settings.profile' } },
      { path: 'profile', name: 'settings.profile', component: page('settings/Profile.vue') },
      { path: 'password', name: 'settings.password', component: page('settings/Password.vue') },
      { path: 'google_2fa', name: 'settings.google_2fa', component: page('settings/Google2FA.vue') }
    ]
  }
]

export function createRouter () {
  return new Router({
    routes,
    scrollBehavior,
    mode: 'history'
  })
}
