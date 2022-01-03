<template>
    <div id="chat">
        <div class="container">
    <div class="container-chat clearfix">
        <div class="people-list" id="people-list">
          <div class="search">
            <input type="text" placeholder="search" />
            <i class="fa fa-search"></i>
          </div>
          <ul class="list">


            <li @click="selectUser(user.id)" class="clearfix" v-for="user in users" :key="`${user.id}`">
              <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg" alt="avatar" />
              <div class="about">
                <div class="name">{{user.name}}</div>

                <div class="status" v-if="onlineuser(user.id) || online.id==user.id">
                  <i  class="fa fa-circle online"></i> online
                </div>
                <div class="status" v-else>
                  <i  class="fa fa-circle online"></i> offline
                </div>

              </div>
            </li>



          </ul>
        </div>

        <div class="chat">
          <div class="chat-header clearfix">
            <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01_green.jpg" alt="avatar" />

            <div class="chat-about">
              <div class="chat-with" v-if="get_user_message.user">{{get_user_message.user.name}}</div>
              <div class="chat-num-messages">already 1 902 messages <span @click="deleteAllMessage" class="badge badge-danger float-right" style="cursor:pointer">Delete All Message</span></div>
            </div>
            <i class="fa fa-star"></i>
          </div> <!-- end chat-header -->

          <div class="chat-history" v-chat-scroll>
            <ul>

              <li class="clearfix" v-for="message in get_user_message.messages" :key="`${message.id}`">
                <div class="message-data align-right">
                  <span class="message-data-time" ><span @click="deleteSingleMessage(message.id)" class="badge badge-danger mr-4" style="cursor:pointer">Delete</span>{{message.created_at}}</span> &nbsp; &nbsp;
                  <span class="message-data-name" >{{message.user.id===get_user_message.user.id ? message.user.name : 'You'}}</span> <i class="fa fa-circle me"></i>

                </div>
                <div :class="`message ${message.user.id===get_user_message.user.id ? 'other-message float-left' : 'my-message float-right' } `">
                    {{message.message}}
                </div>
              </li>

            </ul>

          </div> <!-- end chat-history -->

          <div class="chat-message clearfix">
          <span v-if="typing"><small>{{typing}} typing....</small></span>
            <textarea v-if="text_area" @keydown.enter="sendMessage" @keydown="typingEvent(get_user_message.user.id)" v-model="message" id="message-to-send" placeholder="Type your message" rows="3"></textarea>

            <i class="fa fa-file-o"></i> &nbsp;&nbsp;&nbsp;
            <i class="fa fa-file-image-o"></i>

            <button>Send</button>

          </div> <!-- end chat-message -->

        </div> <!-- end chat -->

      </div> <!-- end container -->

    <script id="message-template" type="text/x-handlebars-template">
      <li class="clearfix">
        <div class="message-data align-right">
          <span class="message-data-time" > Today</span> &nbsp; &nbsp;
          <span class="message-data-name" >Olia</span> <i class="fa fa-circle me"></i>
        </div>
        <div class="message other-message float-right">

        </div>
      </li>
    </script>

    <script id="message-response-template" type="text/x-handlebars-template">
      <li>
        <div class="message-data">
          <span class="message-data-name"><i class="fa fa-circle online"></i> Vincent</span>
          <span class="message-data-time"> Today</span>
        </div>
        <div class="message my-message">

        </div>
      </li>
    </script>

</div>
    </div>
</template>

<script>
import _ from "lodash"

export default({
    data() {
        return{
            message:"",
            text_area:false,
            typing:null,
            allusers:[],
            online:""
        }
    },

    computed:{
        users(){

          return this.$store.getters.get_users
        },

        get_user_message(){

          return this.$store.getters.get_user_message
        }


    },

    mounted(){

     Echo.private(`chat.${authuser.id}`)
      .listen('MessageSend', (e) => {

          this.selectUser(e.message.from)
       });
       this.get_users();

       Echo.private('typingevent')
         .listenForWhisper('typing', (e) => {
             console.log(e.user.name);
             console.log(authuser.name);
            if (e.user.id==this.get_user_message.user.id && authuser.id==e.user_id) {
                this.typing=e.user.name
            }

            setTimeout(() => {
                this.typing=null
            }, 1000);
         })

          Echo.join('liveuser')
        .here((users) => {
            this.allusers=users
        })
        .joining((user) => {

            this.online=user
        })
        .leaving((user) => {
            console.log(user.name);
        });




    },

    created(){

    },
    methods:{
        get_users(){

            this.$store.dispatch('getUserList')
        },

        selectUser(id){
            this.text_area=true
            this.$store.dispatch('get_user_message',id)
        },

        sendMessage(e){
             e.preventDefault();
              let data={
                  message:this.message,
                  user_id:this.get_user_message.user.id
              }

              if(this.message !==""){
                   axios.post('send_message',data).then(response=>{
                      this.selectUser(this.get_user_message.user.id);
                   })
                   this.message="";
              }
        },

        deleteSingleMessage(id){
            axios.get(`delete_message/${id}`).then(res=>{
                this.selectUser(this.get_user_message.user.id);
            })
        },


        deleteAllMessage(){
            axios.get(`delete_all_message/${this.get_user_message.user.id}`).then(res=>{
                this.selectUser(this.get_user_message.user.id);
            })
        },

        typingEvent(user_id){
            Echo.private('typingevent')
            .whisper('typing', {
                'user': authuser,
                'typing': this.message,
                'user_id':user_id
            });
        },
        onlineuser(userid){
            return _.find(this.allusers,{'id':userid});
        }

    }
})
</script>

<style scoped>
    .people-list ul{
        overflow: scroll;
    }
</style>

