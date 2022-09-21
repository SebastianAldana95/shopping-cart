<template>
    <div class="w-full">
        <div class="lg:w-2/3 w-full mx-auto mt-8 overflow-auto">
            <table class="table-auto w-full text-left whitespace-nowrap">
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
                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-200">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, index) in cart" :key="item.id">
                    <td class="p-4" v-text="item.name"></td>
                    <td class="p-4" v-text="item.quantity"></td>
                    <td class="p-4" v-text="cartLineTotal(item)"></td>
                    <td class="w-10 text-right">
                        <button
                            class="flex ml-auto text-sm text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded"
                            @click="$store.commit('removeFromCart', index)"
                        >Remove
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="p-4 font-bold">Total Amount</td>
                    <td class="p-4 font-bold" v-text="cartQuantity"></td>
                    <td class="p-4 font-bold" v-text="cartTotal"></td>
                    <td class="w-10 text-right"></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="lg:w-2/3 w-full mx-auto mt-8">
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="leading-7 text-sm text-gray-600 font-bold" for="customer_name">
                        Nombre del cliente
                    </label>
                    <input
                        type="text"
                        id="customer_name"
                        name="customer_name"
                        class="block @error('name') border-red-500 @enderror w-full bg-gray-100 rounded border-gray-300 focus:border-indigo-500 focus:outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                        v-model="customer.name"
                        :disabled="paymentProcessing"
                        required
                    >
                    <p v-if="errors.name" class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span>{{ errors.name[0] }}</p>
                </div>
                <div class="w-full md:w-1/2 px-3">
                    <label for="customer_email" class="leading-7 text-sm text-gray-600 font-bold">Email del
                        cliente</label>
                    <input
                        type="email"
                        id="customer_email"
                        name="customer_email"
                        class="w-full bg-gray-100 rounded border-gray-300 focus:border-indigo-500 focus:outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                        v-model="customer.email"
                        :disabled="paymentProcessing"
                        required
                    >
                    <p v-if="errors.email" class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span>{{ errors.email[0] }}</p>
                </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-2">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label for="customer_mobile" class="leading-7 text-sm text-gray-600 font-bold">Telefono del
                        cliente</label>
                    <input
                        type="number"
                        id="customer_mobile"
                        name="customer_mobile"
                        class="w-full bg-gray-100 rounded border-gray-300 focus:border-indigo-500 focus:outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                        v-model="customer.phone"
                        :disabled="paymentProcessing"
                        required
                    >
                    <p v-if="errors.phone" class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span>{{ errors.phone[0] }}</p>
                </div>
            </div>
        </div>
        <div class="p-2 mb-24 w-full">
            <button
                class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded"
                @click="processPayment"
                :disabled="paymentProcessing"
                v-text="paymentProcessing ? 'procesando' : 'Pagar ahora'"
            ></button>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            customer: {
                name: '',
                email: '',
                phone: '',
                status: '',
                total: '',
                transaction_id: '',
                transaction_url: ''
            },
            errors: {},
            paymentProcessing: false
        }
    },
    methods: {
        cartLineTotal(item) {
            let price = (item.price * item.quantity);
            return price.toLocaleString('es-CO', {style: 'currency', currency: 'COP'});
        },
        async processPayment() {
            this.paymentProcessing = true;

            this.customer.status = 'CREATED';
            this.customer.amount = this.cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
            this.customer.cart = JSON.stringify(this.cart);
            this.customer.total = this.customer.amount;

            await axios.post('/api/purchase', this.customer)
                .then((response) => {
                    this.paymentProcessing = false;
                    window.location.href = response.data.transaction_url;

                    this.$store.commit('updateOrder', response.data);
                    this.$store.dispatch('clearCart');

                    this.$router.push({
                        name: 'order.summary',
                        params: {
                            id: response.data.id
                        }
                    });

                })
                .catch((error) => {
                    if (error.response.data)
                    {
                        this.errors = error.response.data.errors;
                    }
                    this.paymentProcessing = false
                    // alert(error);
                    console.log(error);
                });
        }
    },
    computed: {
        cart() {
            return this.$store.state.cart;
        },
        cartQuantity() {
            return this.cart.reduce((acc, item) => acc + item.quantity, 0);
        },
        cartTotal() {
            let price = this.cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
            // price = (price / 100);
            return price.toLocaleString('es-CO', {style: 'currency', currency: 'COP'});
        }
    }
}
</script>

<style scoped>

</style>
