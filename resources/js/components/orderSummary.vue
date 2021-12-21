<template>
    <div class="g-padding order-summary">
    <div class="container">
        <h1 class="text-center text-capitalize mb-4">
            {{trans.cart.order_summary}}
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
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row,i) in products" :key="'pro'+i">
                            <th class="w-25">
                                <div class="d-flex justify-content-center align-items-center">
                                    <img :src="row.product.image" class="product">
                                </div>
                            </th>
                            <td class="w-25">
                                <p class="description text-capitalize">{{row.product.name}}</p>
                            </td>
                            <td>
                                <p class="price text-uppercase">{{row.product.price_after_discount}}</p>
                            </td>
                            <td>
                                <p class="p-2 fi-quntity">{{row.number}}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- table in small screen -->
        <div class="d-block d-md-none">
             <div class="shoping-table pt-2 mb-4">
                <table v-for="(row,x) in products" :key="'s-pro-'+x" class="table table-striped border">
                    <thead>
                        <tr>
                            <th scope="col">{{trans.cart.image}}</th>
                            <th class="w-75">
                                <div class="d-flex justify-content-around align-items-center">
                                    <img :src="row.product.image" alt="" class="product">
                                          <a v-if="row.product.in_wishlist==0" @click="addToWishlist(row.product)" ><i class="y-color favorite_icon fa fa-star-o"></i></a>
                                 <a v-if="row.product.in_wishlist==1" @click="deletefromwishlist(row.product)" ><i class="y-color favorite_icon fa fa-star"></i></a>
                             
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td scope="col">{{trans.cart.product}}</td>
                            <td class="w-75">
                            <p class="description text-capitalize"><a
                  :href="'/product/' + row.product.id"
                 class="y_color"
                  target="_self"
                  >{{row.product.name}}</a></p>
                        </td>
                        </tr>
                        <tr>
                            <td scope="col">{{trans.cart.price}}</td>
                            <td class="w-75">
                                <p class="price text-uppercase">{{row.product.price_after_discount}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td scope="col">{{trans.cart.quantity}}</td>
                            <td class="w-75">
                                <div class="">
                                    <p class="p-2 fi-quntity">{{row.number}}</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="address">
            <h4 class="y-color text-capitalize">
                {{trans.cart.address}}
            </h4>
            <p class="location pay-method-wraper">
                <label v-for="(row,i) in addresses" :key="'add'+i" for="pay-method-01" class="d-block text-capitalize mb-3 pay-method">
                     <input type="radio" id="pay-method-01" name="pay-method" class="mr-3" v-model="address" :value="row">
                     {{row.name}}
                    <span class="d-block">
                        {{row.cityname}}','{{row.district}}','{{row.address}}
                    <!-- Cairo,Nasr city, 12 makram ebied,alamal tours... -->
                    </span>
                </label>
                <br>
                <button type="button" class="btn btn-sm btn-outline y-btn" data-toggle="modal" data-target=".address-modal">{{trans.cart.add_address}}</button>   
                
            </p>
            <div class="modal fade address-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="form-two" style="padding:15px">
                            <form class="grey-form">
                                <div class="form-group">
                                    <input type="text" v-model="edit_address.name" class="form-control" :placeholder="trans.cart.address_name">
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <select v-model="edit_address.city_id" class="form-control" id="exampleFormControlSelect1">
                                                <option selected="" disabled="">--{{trans.cart.city}}--</option>
                                                <option v-for="(city,i) in cities" :key="'city'+i" :value="city.id">{{city.name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" v-model="edit_address.district" class="form-control" :placeholder="trans.cart.district">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" v-model="edit_address.address" class="form-control" :placeholder="trans.cart.address">
                                </div>
                                    <textarea v-model="edit_address.note" class="mb-4" :placeholder="trans.cart.note"></textarea>
                                <div class="text-right">
                                <button type="button" @click="addAddress()" class="btn y-btn">{{trans.cart.add_address}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="promocode row">
                <div class="form-group col-md-4">
                    <label>{{trans.cart.promocode}}</label>
                    <input type="text" v-model="promo_code" class="form-control" :disabled="promocode" />
                    <span v-if="promocode" class="text-success">{{trans.cart.discount}}: {{promocode.discount_precent}}%</span>
                    <span v-if="!promo_check" class="text-warning">{{trans.cart.invalide_promo}}</span>
                </div>
                <div class="col-md-2 mt-4">
                    <button class="btn btn-sm btn-outline y-btn" @click="checkPromo()">{{trans.cart.check}}</button>
                </div>
            </div>
            <hr v-if="false">
            <p v-if="false">Select payment method</p>
            <form v-if="false" action="" class="pay-method-wraper">
                <label for="pay-method-01" class="d-block text-capitalize mb-3 pay-method">
                     <input type="radio" id="pay-method-01" name="pay-method" class="mr-3" checked>
                     <img src="web/images/icons/cash.png" alt="">
                     cash
                </label>
                <label for="pay-method-02" class="d-block text-capitalize pay-method">
                     <input type="radio" id="pay-method-02" name="pay-method" class="mr-3">
                     <img src="web/images/icons/visa.png" alt="">
                     visa
                </label>
            </form>
            <hr>
            <div class="row">
                <!-- product total -->
                <div class="col-6 col-md-3 text-capitalize mb-3">
                    {{trans.cart.subtotal}} : 
                </div>
                <div class="col-6 col-md-9 text-uppercase mb-3">{{$store.state.cart.total}}</div>
                
                <!-- promocode discount -->
                <div v-if="promocode" class="col-6 col-md-3 text-capitalize mb-3">
                    {{trans.cart.discount}} : 
                </div>
                <div v-if="promocode" class="col-6 col-md-9 text-uppercase mb-3">- {{Math.ceil($store.state.cart.total*promocode.discount_precent/100)}}</div>
                
                <!-- shipping fee -->
                <div class="col-6 col-md-3 text-capitalize mb-3">
                    {{trans.cart.delivery_coast}} : 
                </div>
                <div class="col-6 col-md-9 text-uppercase mb-3">{{shipping}}</div>
            </div>
            <div class="total">
                <p class="text-uppercase">
                    {{trans.cart.total}}: 
                    <span class="y-color ">{{total_price}}</span>  {{trans.home.currency}}
                </p>
                <a v-if="!checkout_loading" @click="checkout($event)" style="cursor: pointer;" class="y-btn w-25 text-capitalize text-center">
                    {{trans.cart.checkout}}
                </a>
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
            addresses:[],
            address:null,
            edit_address:{
                name:null,
                city_id:null,
                district:null,
                address:null,
                note:null
            },
            cities:[],
            checkout_loading:false,
            promocode:null,
            promo_code:'',
            promo_check:true
        }
    },
    created(){
        this.getAddresses();
        this.getCities();
    },
    watch:{
        access_token:function(){
            this.getAddresses();
        }
    },
    computed:{
        products(){
             //console.log(this.$store.state.user.token);
            return this.$store.state.cart.products
        },
        access_token(){
            return this.$store.state.user.token;
        },
        shipping(){
            if(!this.address)return 0;
            return this.address.city.shipping;
        },
        total_price(){
            let total=this.$store.state.cart.total+this.shipping;
            if(this.promocode){

                let discount=Math.ceil((this.promocode.discount_precent*this.$store.state.cart.total)/100);
                total=total-discount;
            }
            return total;
        }

    },
    methods:{
        getAddresses(){
            axios.get('/api/address/get',{headers:{'Authorization': `Bearer ${this.access_token}`}}).then(res=>{
                this.addresses=res.data.data;
            });
        },
        getCities(){
            axios.get('/api/cities',{params:{'lang_id':this.$store.state.lang.active_lang}}).then(res=>{
                this.cities=res.data.data;
            });
        },
        addAddress(){
            if(!this.validateAddress())return false;
            axios.post('/api/address/add',this.edit_address,
            {headers:{'Authorization': `Bearer ${this.access_token}`}}).then(res=>{
                if(res.data.status==200){
                    this.addresses=res.data.data;
                    $(".address-modal").modal('toggle');
                    $('.modal-backdrop').hide();
                }else{
                     Swal.fire(
  'Alert!',res.data.er
  ,
  'danger'
)
                }
                this.emptyAddress();
                
            });
        },
        validateAddress(){
            if(this.edit_address.name==''){alert(this.trans.cart.validate.name);return false}
            if(this.edit_address.city_id==''){alert(this.trans.cart.validate.city_id);return false}
            if(this.edit_address.district==''){alert(this.trans.cart.validate.district);return false}
            if(this.edit_address.address==''){alert(this.trans.cart.validate.address);return false}
            return true;
        },
        emptyAddress(){
            this.edit_address={
                name:null,
                city_id:null,
                district:null,
                address:null,
                note:null
            };
        },
        checkout(e){
            e.preventDefault();
            if(!this.address){
                alert(this.trans.cart.validate_address);
                return false;
            }
            this.checkout_loading=true;
            let data={lang_id:this.$store.state.lang.active_lang,address_id:this.address.id};
           
         
            if(this.promocode)
                data.promo_id=this.promocode.id;
                console.log(data);
                // return false;
            axios.post('/api/checkout',
                data,
                {headers:{'Authorization': `Bearer ${this.access_token}`}}
            ).then(res=>{
                if(res.data.status==200)
                console.log(res.data);
               
                    window.location='/order-details/'+res.data.data.id;
                this.checkout_loading=false;
            });
        },
        addToWishlist(product){
            let config={};
            config.headers={'Authorization': `Bearer ${this.access_token}`};
            console.log(this.access_token);
            if(this.access_token=='null'){
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
        },
        checkPromo(){
            axios.post('/api/promo/check',{promocode:this.promo_code}).then(res=>{
                if(res.data.status==200){
                    this.promocode=res.data.data;
                    this.promo_check=true;
                }else{
                    this.promo_check=false;
                }
            })
        }
    }
}
</script>