import Axios from "axios"

export default {
  state:{
    users:[],
    user_messages:[]

  },
  mutations: {
    USER_LSIT(state,data){
        state.users=data
    },
    USER_MESSAGE(state,data){
        state.user_messages=data
    }
   },
  actions: {
    getUserList(contex){
        Axios.get('all_user').then(res=>{
            contex.commit('USER_LSIT',res.data);
        })
    },
    get_user_message(context,id){
        Axios.get('get_user_message/'+id).then(res=>{
            context.commit('USER_MESSAGE',res.data);
        });
    }
   },
  getters: {
        get_users(state){
            return state.users
        },
        get_user_message(state){
            return state.user_messages
        }
   }
}
