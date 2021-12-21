import axios from "axios";

const lang={
    state:{
        active_lang:1,
        langs:[]
    },
    mutations:{
        changeActive(state,lang_id){
            state.active_lang=lang_id;
        },
        updateLangs(state,langs){
            state.langs=langs;
        }
    },
    actions:{
        getLangs(context){
            axios.get('/api/languages').then(res=>{
                context.commit('updateLangs',res.data.data);
            })
        },
        getCurrent(context){
            axios.get('get-active').then(res=>{
                context.commit('changeActive',res.data.data);
            })
        }
    }
}

export default lang;