import Vuex from 'vuex';
import Vue from 'vue';
import cart from './stores/cart';
import lang from './stores/lang';
import user from './stores/user';

Vue.use(Vuex);
const store = new Vuex.Store({
    modules: {
      cart: cart,
      lang: lang,
      user:user
    }
  })
  
  export default store;