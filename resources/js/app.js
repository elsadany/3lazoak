require('./bootstrap');
window.Vue = require("vue");

// import Vuex from 'vuex';
import store from './store';


const baseUrl = window.location.origin;
Vue.component("index", require("./components/index.vue").default);
Vue.component("category", require("./components/category.vue").default);
Vue.component("products", require("./components/products.vue").default);
Vue.component("search", require("./components/search.vue").default);
Vue.component("product", require("./components/product.vue").default);
Vue.component("cart", require("./components/cart.vue").default);
Vue.component("wishlist", require("./components/wishlist.vue").default);
Vue.component("order-summary", require("./components/orderSummary.vue").default);

const app = new Vue({
    el: "#app",
    store:store,
    created(){
        this.$store.dispatch('getLangs');
        this.$store.dispatch('getCurrent');
        this.$store.dispatch('checkuser').then(function() {
            this.$store.dispatch('assignUsertoCart');
            this.$store.dispatch('getCart');
        }.bind(this));
        
    }
});