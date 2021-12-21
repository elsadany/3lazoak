import axios from "axios";


const cart={
    state:{
        products:[],
        product_ids:[],
        session_id:localStorage.getItem('session_id') || null,
        total:0,
    },
    mutations:{
        add(state,{id,product,value_id,number}){
            state.product_ids.push(product.id);
            state.products.push({id,product,value_id,number});
        },
        
        setSessionId(state,session_id){
            localStorage.setItem('session_id',session_id);
            state.session_id=session_id;
        },
        updateCart(state,{products,product_ids,total}){
            state.product_ids=product_ids;
            state.products=products;
            state.total=total;
        },
        updateProduct(state,{cart_id,cart}){
            for(var i in state.products){
                console.log(state.products[i].id,cart_id);
                if(state.products[i].id==cart_id)
                    state.products[i]=cart;
            }
        },
        removeFromCart(state,cart_id){
            state.products=state.products.filter((cart)=>{
                return cart.id!=cart_id;
            });
        },
        emptyCart(state){
            state.session_id='';
            localStorage.removeItem('session_id');
            state.products=[];
            state.product_ids=[];
            state.total=0;
        }

    },
    actions:{
        assignUsertoCart(context){
            if(!context.rootState.user.token)return false;
            axios.get('api/cart/assign-to-user',
                {
                    params:{session_id:context.state.session_id},
                    headers:{'Authorization': `Bearer ${context.rootState.user.token}`}
                }
            );
        },
        getCart(context){
            if(!context.state.session_id)return false;
            var config={params:{lang_id:context.rootState.lang.active_lang}};
            if(context.rootState.user.token)
                config.headers={'Authorization': `Bearer ${context.rootState.user.token}`};
            else
                config.params.session_id=context.state.session_id;

            axios.get('/api/carts',config).then(res=>{
                var data=res.data.data;
                let products=[];
                let product_ids=[];
                let total=res.data.total;
                for(var i in data){
                    let id=data[i].id;
                    let product=data[i].product;
                    let value_id=data[i].value_id
                    let number=data[i].number;
                    products.push({id,product,value_id,number});
                    product_ids.push(data[i].product.id);
                }
                context.commit('updateCart',{products,product_ids,total});
            });
        },
        add(context,{product,value_id,count=1}){
            axios.post('/api/carts/add',{
                product_id:product.id,
                value_id:value_id,
                number:count,
                session_id:context.state.session_id
            }).then(res=>{
                if(res.data.status==200){
                    context.commit('setSessionId',res.data.session_id);
                    context.commit('add',{product,value_id,count});
                    window.location='/cart';
                }
            });

        },
        updateProductNumber(context,{cart_id,number}){
            axios.post('/api/carts/edit-number?lang_id='+context.rootState.lang.active_lang,
            {
                cart_id:cart_id,
                session_id:context.state.session_id,
                number:number
            }).then(res=>{
                let cart=res.data.data;
                context.commit('updateProduct',{cart_id,cart});
            })
        },
        removeProduct(context,cart_id){
            return new Promise((resolve,reject)=>{
                let config={params:{
                    lang_id:context.rootState.lang.active_lang,
                    cart_id:cart_id,
                    session_id:context.state.session_id,
                }};
                if(context.rootState.user.token)
                    config.headers={'Authorization': `Bearer ${context.rootState.user.token}`};


                axios.get('api/carts/delete',config).then(res=>{
                    if(res.data.state==200){
                        context.commit('removeFromCart',cart_id);
                        resolve();
                    }
                });
            });
        }
    }
}

export default cart;