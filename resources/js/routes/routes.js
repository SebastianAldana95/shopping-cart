import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: '/',
        name: 'products.index',
        component: () => import('../components/Products/Index.vue')
    },
    {
        path: '/product/:slug',
        name: 'products.show',
        component: () => import('../components/Products/Show.vue')
    },
    {
        path: '/checkout',
        name: 'order.checkout',
        component: () => import('../components/Order/Checkout.vue')
    },
    {
        path: '/summary/:id',
        name: 'order.summary',
        component: () => import('../components/Order/Summary.vue')
    },
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

export default router
