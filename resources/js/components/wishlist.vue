<template>
    <div class="">
           <div class="intro">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="categ-head text-center">
                        {{trans.home.wishlist}}
                    </h1>
                </div>
                
            </div>
        </div>
    </div>
     <div class="main-category">
        <div class="container">
            <div class="row mb-5">
            
                <div class="col-lg-12 ">
                    <div class="row mx-0"  >
                    <div class=" col-lg-3 col-md-5 col-sm-6 col-12 mb-4  mb-4" v-for="(row,k) in products " :key="'row'+k">
                    
                     <div class="g-card">
    <div class="card ">
        <div class="img-wrap">
            <img :src="row.image" alt="">
        </div>
        <p class="description">
       <a
                  :href="'/product/' + row.id"
                 class="y_color"
                  target="_self"
                  > {{row.name}}</a>
        </p>
        <span class="new">
            {{row.brand}}
        </span>
         <span class="discount" v-if="(row.discount_precent>0)">
            {{row.discount_precent}}% off
        </span>
    </div>
    <div class="action">
        <p class="price m-0">
            {{row.price_after_discount}} <span>{{trans.category.currency}}</span>
        </p>
        <a href="javascript:;" target="_self" v-if="row.in_wishlist==0" @click="addToWishlist(row)" ><i class="y-color favorite_icon fa fa-star-o"></i></a>
                                 <a href="javascript:;" target="_self"  v-if="row.in_wishlist==1" @click="deletefromwishlist(row)" ><i class="y-color favorite_icon fa fa-star"></i></a>
                             
        <a href="" class="y-btn" v-if="row.has_options==false">{{trans.home.buy_now}}</a>
                                <a :href="'/product/'+row.id" target="_self" class="y-btn" v-else-if="row.has_options==true">{{trans.home.choose}}</a>
    </div>
</div>
                    </div>                    </div>
                </div>
            </div>
            <!-- <div class="g-pgination co-xl-5 col-lg-6 col-md-7 ml-auto px-0 px-md-2" v-if='maxpage>1'>
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item arrow"  v-if="page != 1">
                            <a class="page-link" @click="page--;getProducts(page);" aria-label="Previous">
                                <span aria-hidden="true">
                                    </span>< <span class="sr-only">Previous
                                </span>
                            </a>
                        </li>
                        <li class="page-item" v-for="k in maxpage" :key="'row'+k"><a  @click="page=k;getProducts(k);"  v-bind:class="[(k==page)? 'active':'','page-link ']"    >{{k}}</a></li>
                      
                        
                        <li class="page-item arrow "  v-if="page != maxpage">
                            <a class="page-link"  @click="page++;getProducts(page);" aria-label="Next">
                                <span aria-hidden="true">></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div> -->
        </div>
    </div>
    </div>
</template>
<style>    
.white-form input, .white-form select, .grey-form input, .grey-form select {
    height: 45px;
}
.check_radio{
    height: auto;
}
</style>
<script>
    export default {
    props:['lang_id'],
   
    data(){
        return {
            products:[],
           
trans:window.trans
        }
    },
    computed:{
        access_token(){
            return this.$store.state.user.token;
        }
    },
    created(){
    
        this.getProducts();
    },
    
    methods:{
      
        getProducts(){
             let config={};
            config.headers={'Authorization': `Bearer ${this.access_token}`};
            console.log(this.access_token);
             axios.get('/api/wishlists?lang_id='+this.lang_id,config).then(res=>{
                this.products=res.data.data;
              
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
