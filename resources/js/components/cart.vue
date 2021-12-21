<template>
<div class="g-padding shoping-cart">
    <div class="container">
        <h1 class="text-center text-capitalize mb-4">
         {{trans.home.mycart}}
        </h1>
        <!-- table in medium and large screen -->
        <div class="d-none d-md-block">
            <div class="shoping-table pt-2 mb-4">
                <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">{{trans.cart.image}}</th>
                        <th scope="col">{{trans.cart.product}}</th>
                        <th scope="col">{{trans.cart.price}}</th>
                        <th scope="col">{{trans.cart.quantity}}</th>
                        <th scope="col">{{trans.cart.total}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row,i) in products" :key="'cart'+i">
                            <th class="w-25">
                                <div class="d-flex justify-content-around align-items-center">
                                    <span class="remove">
                                        <img src="web/images/icons/remove.png" alt="remove from cart" style="cursor: pointer;" @click="remove(i,row)">
                                    </span>
                                    <img :src="row.product.image" alt="" class="product">
                                     <a href="javascript:;" target="_self"  v-if="row.product.in_wishlist==0" @click="addToWishlist(row.product)" ><i class="y-color favorite_icon fa fa-star-o"></i></a>
                                 <a href="javascript:;" target="_self"  v-if="row.product.in_wishlist==1" @click="deletefromwishlist(row.product)" ><i class="y-color favorite_icon fa fa-star"></i></a>
                             
                                </div>
                            </th>
                            <td class="w-25">
                                <p class="description text-capitalize"><a
                  :href="'/product/' + row.product.id"
                 class="y_color"
                  target="_self"
                  >{{row.product.name}}</a></p>
                            </td>
                            <td>
                                <p class="price text-uppercase">{{row.product.price}}</p>
                            </td>
                            <td>
                                <div class="">
                                    <div class="g-pgination px-0 pt-2  d-inline-block mr-3">
                                        <nav aria-label="Page navigation example">
                                            <ul class="pagination">
                                                <li class="page-item arrow mx-0">
                                                    <a class="page-link" href="javascript:void(0)" aria-label="Previous" @click="increaseCartNumber($event,row)">
                                                        <span aria-hidden="true">+</span>
                                                    </a>
                                                </li>
                                                <li class="page-item mx-0">
                                                    <input type="text" disabled class="page-link text-small" :value="row.number"  href="javascript:void(0)" style="width: 43px;height: 43px;background-color: white;color: black;"/>
                                                </li>
                                                <li class="page-item arrow mx-0">
                                                    <a class="page-link" href="javascript:void(0)" aria-label="Next" @click="decreaseCartNumber($event,row)">
                                                        <span aria-hidden="true">-</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="total-price text-uppercase">{{row.product.price_after_discount*row.number}}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- table in small screen -->
        <div class="d-block d-md-none">
             <div class="shoping-table pt-2 mb-4" v-for="(row,i) in products" :key="'cart'+i">
                <table class="table table-striped border">
                    <thead>
                        <tr >
                            <th scope="col">{{trans.cart.image}}</th>
                            <th class="w-75">
                                <div class="d-flex justify-content-around align-items-center">
                                    <span class="remove">
                                        <img src="web/images/icons/remove.png" alt="remove from cart" style="cursor: pointer;" @click="remove(i,row)">
                                    </span>
                                    <img :src="row.product.image" alt="" class="product">
                                     <a href="javascript:;" target="_self"  v-if="row.product.in_wishlist==0" @click="addToWishlist(row.product)" ><i class="y-color favorite_icon fa fa-star-o"></i></a>
                                 <a href="javascript:;" target="_self"  v-if="row.product.in_wishlist==1" @click="deletefromwishlist(row.product)" ><i class="y-color favorite_icon fa fa-star"></i></a>
                             
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td scope="col">{{trans.cart.product}}</td>
                            <td class="w-75">
                            <p class="description text-capitalize">
                          <p class="description text-capitalize">{{row.product.name}}</p>
                            </p>
                        </td>
                        </tr>
                        <tr>
                            <td scope="col">{{trans.cart.price}}</td>
                            <td class="w-75">
                                <p class="price text-uppercase">
                                 <p class="price text-uppercase">{{row.product.price}}</p>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td scope="col">{{trans.cart.quantity}}</td>
                            <td class="w-75">
                               <div class="">
                                    <div class="g-pgination px-0 pt-2  d-inline-block mr-3">
                                        <nav aria-label="Page navigation example">
                                            <ul class="pagination">
                                                <li class="page-item arrow mx-0">
                                                    <a class="page-link" href="javascript:void(0)" aria-label="Previous" @click="increaseCartNumber($event,row)">
                                                        <span aria-hidden="true">+</span>
                                                    </a>
                                                </li>
                                                <li class="page-item mx-0">
                                                    <input type="text" disabled class="page-link text-small" :value="row.number"  href="javascript:void(0)" style="width: 43px;height: 43px;background-color: white;color: black;"/>
                                                </li>
                                                <li class="page-item arrow mx-0">
                                                    <a class="page-link" href="javascript:void(0)" aria-label="Next" @click="decreaseCartNumber($event,row)">
                                                        <span aria-hidden="true">-</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td scope="col">{{trans.cart.total}}</td>
                            <td class="w-75">
                                <p class="total-price text-uppercase">
                                   {{row.product.price_after_discount*row.number}}
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 ">
                <div class="rounded subtotal  p-4 mb-2">
                        <!-- <div class="d-flex align-items-center justify-content-between">
                            <p class=" text-capitalize">
                                subtotal :
                            </p>
                            <p class=" text-uppercase">{{total}} {{trans.home.currency}}</p>
                        </div> -->
                        <!-- <div class="d-flex align-items-center justify-content-between">
                            <p class=" text-capitalize">
                                delivery charges : 
                            </p>
                            <p class=" text-uppercase">
                                300 le
                            </p>
                        </div> -->
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="total-price text-capitalize">
                                {{trans.cart.total_price}} :
                            </p>
                            <p class="total-sum y-color  text-uppercase">
                                {{total}} {{trans.home.currency}}
                            </p>
                        </div>
                </div>
            </div>
            <div class="col-lg-8 col-lg-6">
                <div class="actio h-100 d-flex align-items-end justify-content-md-end">
                        <a v-if="$store.state.user.token" class="y-btn text-capitalize text-white mx-2" href="/order-summary" target="_self">{{trans.cart.confirm_order}}</a>
                        <a v-else href="./login" class="y-btn text-capitalize text-white mx-2" target="_self">{{trans.cart.sign_in_to_continue}}</a>
                        <a href="./" class="reverse-y-btn text-capitalize mx-2">{{trans.cart.continue_shpping}}</a>
                </div>
            </div>
        </div>
    </div>
</div>  
</template>
<script>
export default {
    data(){
        return{
            trans:window.trans,
        }
    },
    computed:{
        products(){
            return this.$store.state.cart.products
        },
        total:function (){
            let total=0;
            let products=this.products;
            for(var i in products){
                total+=products[i].product.price_after_discount*products[i].number;
            }
            return total;
        }
    },
    methods:{
        increaseCartNumber(e,cart){
            e.preventDefault();
            let number=cart.number+1;
            this.updateProduct(cart.id,number);
            cart.number++;
        },
        decreaseCartNumber(e,cart){
            e.preventDefault();
            if(cart.number==1)return false;
            let number=cart.number-1;
            this.updateProduct(cart.id,number);
            cart.number--;
        },
        updateProduct(cart_id,number){
            this.$store.dispatch('updateProductNumber',{cart_id,number});
        },
        remove(key,cart_item){
            this.products.splice(key,1);
            this.$store.dispatch('removeProduct',cart_item.id);
        },   addToWishlist(product){
  let config={};
            config.headers={'Authorization': `Bearer ${this.access_token}`};
            console.log(this.access_token);
            if(this.access_token==''){
                alert('Login First');
            return false;
            }
           
             axios.get('/api/wishlists/add?product_id='+product.id,config).then(res=>{
           
                 this.getProducts();
             });
        },
        deletefromwishlist(product){
             let config={};
            config.headers={'Authorization': `Bearer ${this.access_token}`};
 axios.get('/api/wishlists/delete?product_id='+product.id,config).then(res=>{
                 
                 this.getProducts();
             });
        }
    }
}
</script>