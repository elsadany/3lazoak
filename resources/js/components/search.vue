<template>
    <div class="">
        <div class="intro">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="categ-head text-center">
                            {{trans.category.products}}
                        </h1>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="main-category">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-lg-3 mb-3 mb-lg-0 px-0">
                        
                        <form ref="filter">
                            <!-- <div class="range-container">
                                <section class="range-slider">
                                    <span style="padding-right: 20px;" class="rangeValues d-block text-left"> {{min_price}} : {{max_price}}</span>
                                    <input name="price_from" :min="min_price" :max="max_price" step="5" type="range">
                                    <input name="price_to" :min="min_price" :max="max_price" step="5" type="range">
                                </section>
                            </div> -->
                            <div class="form-group">
                              <label>{{trans.category.price}}</label>
                                <div class="range-container">
    <section class="range-slider">
         <span class="rangeValues d-block text-left"></span>
          <input :value="min_price" name="price_from" :min="min_price" :max="max_price" step="5" type="range">
      <input :value="max_price" name="price_to"  :min="min_price" :max="max_price" step="5" type="range">
        </section></div>
                               
                            </div>

                            <hr>
                            <div class="prd-accordion grey-form" id="accordion">
                                <div class="card">
                                    <div class="card-header" id="headingOne" v-if="(brands.length>0)">
                                        <label class="text-left" >{{trans.category.brand}}</label>
                                        <select name="brand_id" class="form-control" >
                                            <option value="">{{trans.category.choose_brand}}</option>
                                            <option v-for="(row,x) in brands" :key="'brand_id'+x" :value="row.id">{{row.name}}</option>
                                        </select>
                                    </div>
                            
                                </div>
                            </div>
                            <div v-for="(row,x) in filters" :key="'filter'+x">
                                <div class="" v-if="row.type!=3" >
                                    <div class="form-group grey-form"   v-if='row.type==1' >
                                        <label>{{row.name}}</label>
                                        <select :name="'value_id['+row.id+']'" class="form-control" >
                                            <option></option>
                                        <option v-for="(one,x) in row.values" :key="'filter-option'+x" :value="one.id">{{one.value}}</option>
                                        </select>
                                        
                                    </div>
                                    <div class="form-group"   v-else-if='row.type==2' >
                                        <label>{{row.name}}</label>
                                        <div  v-for="(one,x) in row.values" :key="'vkfj'+x">
                                            <input type="checkbox" class="check_radio"  :name="'value_id['+row.id+']'" :id="'check'+one.id"  :value="one.id"> <label :for="'check'+one.id">{{one.value}}</label>
                                        </div>
                                        
                                    </div>
                                    <div class="form-group"   v-else-if='row.type==5' >
                                        <label>{{row.name}}</label>
                                        <div  v-for="(one,x) in row.values" :key="'kgjek'+x">
                                            <input type="radio" :name="'value_id['+row.id+']'" class="check_radio"   :id="'radio'+one.id" :value="one.id"> <label :for="'radio'+one.id">{{one.value}}</label>
                                        </div>
                                        
                                    </div>
                                    
                                    
                                </div>
                                <div class="color-selector" v-else-if="row.type==3">
                                <h4 class="y-color">
                                    {{row.name}}
                                </h4>

                                <label class="color-label " style="margin: 5px;" v-for="(color,z) in row.values" :key="'ddffd'+z" :for="'color'+z" >
                                    <input type="radio" :id="'color'+z" :style="'background:'+color.code" :value="color.id" :name="'value_id['+row.id+']'">
                                </label>
                                

                            </div>
                                    
                            </div>
                            <hr>
                            <button type="submit" name="save" class=" btn y-btn  w-25" @click="filterProducts($event)" target="_self">{{trans.category.filter}}</button>
                        </form>
                    
                    </div>
                    <div class="col-lg-9 ">
                        <div class="row mx-0"  >
                            <div class="col-xl-4 col-lg-6 col-md-5 col-sm-6 col-10 mb-4  mb-4" v-for="(row,k) in products " :key="'row'+k">
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
    props:['lang_id','search_key'],
   
    data(){
        return {
            products:[],
            category:{
                name:'',
                image:''
            },
            page:1,
            maxpage:1,
            min_price:0,
            max_price:0,
            brands:[],
            filters:[],
            trans:window.trans
        }
    },
    computed:{
        access_token(){
            return this.$store.state.user.token;
        }
    },
    created(){
      
        this.getData();
        this.getProducts(this.page);
    },
    methods:{
          txt(){
      console.log('texting', this.search_key);
    } ,
        getData(){
           axios.get('/api/categories/show?lang_id='+this.lang_id+'&search='+this.search_key).then(res=>{
                this.category=res.data.category;
                this.brands=res.data.brands;
                this.filters=res.data.data;
                console.log(this.search_key);
            });  
        },
        getProducts(page,params={}){
            let config={};
            config.headers={'Authorization': `Bearer ${this.access_token}`};
            config.params=params;
             axios.get('/api/products?lang_id='+this.lang_id+'&search='+this.search_key,config).then(res=>{
                this.products=res.data.data;
                this.maxpage=res.data.lastpage;
                this.min_price=res.data.min_price;
                this.max_price=res.data.max_price;
            });
        },
        addToWishlist(product){
            let config={};
            config.headers={'Authorization': `Bearer ${this.access_token}`};
            if(this.access_token==null){
                alert('Login First');
                return false;
            }
            axios.get('/api/wishlists/add?product_id='+product.id,config).then(res=>{
                 this.getProducts();
            });
        },
        addToCart(product){
            this.$store.dispatch('add',{product:product,value_id:null});
        },
        deletefromwishlist(product){
             let config={};
            config.headers={'Authorization': `Bearer ${this.access_token}`};
            axios.get('/api/wishlists/delete?product_id='+product.id,config).then(res=>{
                 this.getProducts();
             });
        },
        filterProducts(event){
            event.preventDefault();
            const formData = new FormData(this.$refs['filter']); // reference to form element
            const data = {}; // need to convert it before using not with XMLHttpRequest
            for (let [key, val] of formData.entries()) {
                if(val!='')
                Object.assign(data, { [key]: val })
            }
            console.log(data);

            this.getProducts(this.page,data);
            // let data=new FormData(this.$refs['filter'])
            // console.log(data);
        }
        
        
    }


}
</script>
