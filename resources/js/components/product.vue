<template>
    <div class="">
    <div class="container">
        <div class="g-crumb ">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item pr-2"><a href="./">{{trans.product.home}}</a></li>
                    <li class="breadcrumb-item  pr-2" aria-current="page">{{trans.product.product}}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="intro bg-white pt-4">
        <div class="container">
            <div class="row ">
                <div class="col-lg-7" style=" margin-bottom: 30">
                    
                    <!-- <carousel v-if="images.length" :items="1" :loop="true" :autoplay="true" :nav="true" :dots="true"> -->
                    <VueSlickCarousel v-if="images.length" :focusOnSelect="true" :autoplay="true" :arrows="true" :dots="true">
                        <img v-for="(row,i) in images" :key="'image'+i" class="" :src="row" alt="" >
                    </VueSlickCarousel>
                    <!-- </carousel> -->
                </div>
                <div class="col-lg-5" v-if="product">
                    <h3 class="prd-name ">{{product.name}}</h3>
                    <p class="price text-uppercase">{{product.price_after_discount}} {{trans.category.currency}} <span class="old-num" v-if="product.discount>0" > {{product.price}} {{trans.category.currency}}</span> </p>
                    <table class="table">
                        <thead>
                            <tr>
                                <td colspan="2">
                                    <h4 class="table-heading">{{trans.product.description}}</h4>
                                    <p>{{product.description}}</p>
                                </td>
                            </tr>
                        </thead>
                        <tbody v-if="product">
                            <tr v-for="(row,i) in product.properties" :key="'option'+i">
                                <td>
                                    <h4 class="table-heading">{{row.option}}</h4>
                                </td>
                                <td>
                                    <p>{{row.value}}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="product.has_options==true" >
                        
                    <div class="color-selector" v-if="product.options.type_id==3">
                        <h4 class="y-color">
                            {{product.options.option}}
                        </h4>

                              <label class="color-label " style="margin: 5px;" v-for="(color,z) in product.options.values" :key="'icolor'+z" :for="'color'+z" >
                                  <input type="radio" :id="'color'+z" :style="'background:'+color.code" :value="color.id"  name="value_id">
                        </label>
                        

                    </div>
                        <div class="form-group grey-form"   v-else-if='product.options.type_id!=3' >
                                <label> {{product.options.option}}</label>
                                <select name="value_id" v-model="value_id" class="form-control" >
                                  
                                   <option v-for="(one,x) in product.options.values" :key="'option-v'+x" :value="one.value_id">{{one.value}}</option>
                                </select>
                                
                            </div>
                    
                    </div>
                    <div class="">
                        <small class="text-capitalize d-block">Quantity</small>

                        <div class="g-pgination px-0 pt-2  d-inline-block mr-3">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination">
                                    <li class="page-item arrow mx-0">
                                        <a class="page-link" href="javascript:void(0)" aria-label="Previous" @click="(e)=>{e.preventDefault();item_count++;}">
                                            <span aria-hidden="true">+</span>
                                        </a>
                                    </li>
                                    <li class="page-item mx-0">
                                        <input type="text" v-model="item_count" disabled class="page-link text-small" href="javascript:void(0)" style="width: 43px;height: 43px;background-color: white;color: black;"/>
                                    </li>
                                    <li class="page-item arrow mx-0">
                                        <a class="page-link" href="javascript:void(0)" aria-label="Next" @click="(e)=>{e.preventDefault();if(item_count>1)item_count--;}">
                                            <span aria-hidden="true">-</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <button href="javascript:void(0)" @click="addToCart()" class="y-btn rounded-0">add to card</button>
<a href="javascript:;" target="_self" v-if="product.in_wishlist==0" @click="addToWishlist(product)" ><i class="y-color favorite_icon fa fa-star-o"></i></a>
                                 <a href="javascript:;" target="_self"  v-if="product.in_wishlist==1" @click="deletefromwishlist(product)" ><i class="y-color favorite_icon fa fa-star"></i></a>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="related-product">
        <div class="container">
            <h2 class="text-capitalize mb-4">
                Related Products
            </h2>
            <div class="re-product">
               <div class="content p-1" v-for="(row,k) in products " :key="'row'+k">
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
                                                            
                                        <button type="button" @click="addToCart(row)" class="y-btn" v-if="row.has_options==false">{{trans.home.buy_now}}</button>
                                        <a :href="'/product/'+row.id" target="_self" class="y-btn" v-else-if="row.has_options==true">{{trans.home.choose}}</a>
                                    </div>
                                </div>
                            </div>   
            </div>
        </div>
    </div>
</div>
</template>
<script>
import carousel from 'v-owl-carousel';
import VueSlickCarousel from 'vue-slick-carousel'
import 'vue-slick-carousel/dist/vue-slick-carousel.css'
// optional style for arrows & dots
import 'vue-slick-carousel/dist/vue-slick-carousel-theme.css'

export default {
    components:{carousel,VueSlickCarousel},
    props:['lang_id','product_id'],
    data(){
        return {
            products:[],
            trans:window.trans,
            product:null,
            images:[],
            trans:window.trans,
            item_count:1,
            value_id:null
        }
    },
    created(){
        this.getProduct();
    },
    computed:{
        access_token(){
            return this.$store.state.user.token;
        }
    },
    methods:{
        getProduct(){
              let config={};
            config.headers={'Authorization': `Bearer ${this.access_token}`};
            axios.get('/api/products/show?lang_id='+this.lang_id+'&product_id='+this.product_id,config).then(res=>{
                this.product=res.data.data;
                this.images=res.data.data.images;
                this.products=res.data.related;
                this.images.unshift(res.data.data.image);
            });
        },
        addToCart(){
            this.$store.dispatch('add',{product:this.product,value_id:this.value_id,count:this.item_count});
        },
        addToWishlist(product){
  let config={};
            config.headers={'Authorization': `Bearer ${this.access_token}`};
            console.log(this.access_token);
            if(this.access_token==null){
                alert('Login First');
            return false;
            }
           
             axios.get('/api/wishlists/add?product_id='+product.id,config).then(res=>{
                 this.getProduct();
             });
        },
        deletefromwishlist(product){
             let config={};
            config.headers={'Authorization': `Bearer ${this.access_token}`};
 axios.get('/api/wishlists/delete?product_id='+product.id,config).then(res=>{
                 this.getProduct();
             });
        }
        
        
    
 
    }
}
</script>