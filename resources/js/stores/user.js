import axios from 'axios';
const user={
    state:{
        user:localStorage.getItem('my_user') || null,
        token:localStorage.getItem('token') || null
    },
    mutations:{
        assignUser(state,{user,token}){
            state.user=user;
            localStorage.setItem('my_user',JSON.stringify(user));
            state.token=token;
            localStorage.setItem('token',token);
        }
    },
    actions:{
        checkuser(context,redirect=false){
            return new Promise((resolve, reject) => {
                axios.get('/check-login').then(res=>{
                    if(!context.state.user){
                        if(res.data.status==500&&redirect) window.location='/login';
                        if(res.data.status==200){
                            let user=res.data.data.user;
                            let token=res.data.data.access_token;
                            console.log({user,token});
                            context.commit('assignUser',{user,token});
                        }
                    }
                    console.log('resolve');
                    resolve();
                });
            }, error => {
                reject(error);
            })
        }
    }

}
export default user;