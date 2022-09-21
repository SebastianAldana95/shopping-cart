import './bootstrap';

import { createApp } from 'vue/dist/vue.esm-bundler';
import router from "./routes/routes";
import store from "./store/store";


const app = createApp({
    el: '#app',
    created() {
        store.dispatch('getProducts')
            .then(_ => {})
            .catch((error) => console.error(error));
    }
});

app.use(router)
app.use(store)
app.mount("#app")

