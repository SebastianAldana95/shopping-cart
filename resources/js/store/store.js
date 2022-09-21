import { createStore } from "vuex";
import axios from "axios";

const store = createStore({
    state: {
        products: [],
        cart: [],
        order: {}
    },
    mutations: {
        updateProducts(state, products) {
            state.products = products;
        },
        addToCart(state, product) {
            let productInCartIndex = state.cart.findIndex(item => item.slug === product.slug);
            if (productInCartIndex !== -1) {
                state.cart[productInCartIndex].quantity++;
                return;
            }
            product.quantity = 1;
            state.cart.push(product);
        },
        removeFromCart(state, index) {
            state.cart.splice(index, 1);
        },
        updateOrder(state, order) {
            state.order = order;
        },
        updateCart(state, cart) {
            state.cart = cart;
        }
    },
    actions: {
        getProducts({ commit }) {
            axios.get('/api/products')
                .then((response) => {
                    commit('updateProducts', response.data);
                })
                .catch((error) => console.error(error));
        },
        clearCart({ commit }) {
            commit('updateCart', [])
        },
        getOrder({ commit }) {
            axios.get('/api/summary/')
                .then(response => {
                    commit('updateOrder', response.data);
            }).catch((error) => console.error(error));
        }
    }
});

export default store;
