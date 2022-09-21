<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tienda App</title>

    @vite('resources/css/app.css')
</head>
<body class="antialiased">
    <div class="container mx-auto">
        <div id="app">
            <header class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
                <router-link class="flex items-center text-gray-900 hover:text-gray-900 focus:text-gray-900 mt-2 lg:mt-0 mr-1"
                    :to="{name: 'products.index'}">
                    <img class="mr-2" src="https://mdbootstrap.com/img/logo/mdb-transaprent-noshadows.png" style="height: 20px" alt="" loading="lazy" />
                    <span class="font-medium">Shopping Cart</span>
                </router-link>
                <nav class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l">
                    <router-link
                        class="mr-5 hover:text-gray-900"
                        :to="{name: 'products.index'}"
                    >
                        Productos
                    </router-link>
                </nav>
                <router-link
                    class="inline-flex items-center bg-gray-200 border-0 py-1 px-3 focus:outline-none hover:bg-gray-300 rounded"
                    :to="{name: 'order.checkout'}"
                >
                    Orden<span class="inline-block ml-1" v-text="'(' + $store.state.cart.length + ' items)'"></span>
                </router-link>

            </header>
            <router-view></router-view>
        </div>
    </div>
    @vite('resources/js/app.js')
</body>
</html>
