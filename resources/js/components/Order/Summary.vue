<template>
    <div class="w-full">
        <div class="lg:w-2/3 w-full mx-auto mt-8 overflow-auto">
            <h2
                class="text-sm title-font text-gray-500 tracking-widest"
                v-text="'Transaction ID: ' + order.transaction_id"
            ></h2>
            <h3
                class="text-sm title-font text-gray-500 tracking-widest"
                v-text="'Transaction Status: ' + orderStatus"
            ></h3>
            <h1 v-if="orderStatus === 'PAGADA' || orderStatus === 'PENDIENTE'"
                class="text-center text-gray-900 text-3xl title-font font-medium mb-4"
                v-text="order.customer_name + ' Gracias por su compra'"></h1>
            <h1 v-else-if="orderStatus !== 'PAGADA' || orderStatus !== 'PENDIENTE'"
                class="text-center text-gray-900 text-3xl title-font font-medium mb-4">Transacción no completada</h1>
            <button
                v-if="orderStatus !== 'PAGADA'"
                class="my-2 float-right text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded"
                @click="processPayment(order)"
                :disabled="paymentProcessing"
                v-text="paymentProcessing ? 'procesando' : 'Reintentar pago'"
            ></button>
            <table class="my-2 table-auto w-full text-left whitespace-nowrap">
                <thead>
                <tr>
                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-200 rounded-tl rounded-bl">
                        Item
                    </th>
                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-200">
                        Quantity
                    </th>
                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-200">
                        Price
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in order.products" :key="item.id">
                    <td class="p-4" v-text="item.name"></td>
                    <td class="p-4" v-text="item.pivot.quantity"></td>
                    <td class="p-4" v-text="orderLineTotal(item)"></td>
                </tr>
                <tr>
                    <td class="p-4 font-bold">Total Amount</td>
                    <td class="p-4 font-bold" v-text="orderQuantity"></td>
                    <td class="p-4 font-bold" v-text="orderTotal"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            order: {},
            paymentProcessing: false
        }
    },
    created() {
        this.getOrder();
    },
    methods: {
        getOrder() {
            axios.get(`/api/summary/` + this.$route.params.id).then(response => {
                this.order = response.data;
                this.$store.commit('updateOrder', response.data);
            }).catch(error => {
                console.log(error);
            });
        },
        orderLineTotal(item) {
            let price = (item.price * item.pivot.quantity);
            // price = (price / 100);

            return price.toLocaleString('es-CO', {style: 'currency', currency: 'COP'});
        },
        async processPayment(order) {
            this.paymentProcessing = true;

            await axios.get(`/api/payment/` + order.id).then((response) => {
                this.paymentProcessing = false;
                window.location.href = response.data.transaction_url;

                this.$router.push({
                    name: 'order.summary',
                    params: {
                        id: response.data.id
                    }
                });
            })
                .catch((error) => {
                    this.paymentProcessing = false
                    alert(error);
                    console.log(error);
                });
        }
    },
    computed: {
        /*order() {
            return this.$store.state.order;
        },*/
        orderStatus() {
            if (this.order.status === 'APPROVED') return "PAGADA";
            if (this.order.status === 'CREATED') return "CREADA";
            if (this.order.status === 'REJECTED') return "RECHAZADA";
            if (this.order.status === 'PENDING') return "PENDIENTE";
            if (this.order.status === 'FAILED') return "FALLIDA";
            return 'SIN ESTADO';
        },
        orderQuantity() {
            return this.order.products.reduce((acc, item) => acc + item['pivot'].quantity, 0);
        },
        orderTotal() {
            let price = this.order.products.reduce((acc, item) => acc + (item['price'] * item['pivot'].quantity), 0);
            return price.toLocaleString('es-CO', {style: 'currency', currency: 'COP'});
        }
    }
}
</script>

<style scoped>

</style>
