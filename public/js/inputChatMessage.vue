<template>

  <div class="input-field col s12 m6 offset-m3 l6 offset-l3 grey lighten-2 search-border">
    <i class="material-icons prefix" style="top:10%!important">message</i>
    <textarea placeholder="Écrivez un message" id="textarea1" v-model="message" class="materialize-textarea" style="border-bottom:0!important;box-shadow:0 0px!important" required></textarea>
    <a class="right mr-r-3" @click="sendMessage"><i class="material-icons small center" style="float:right">navigate_next</i>Envoyer</a>
  </div>


</template>

<script>

export default{
  props:['roomId', 'userId', 'user'],

  data(){
    return {
      message:''
    }
  },

  methods:{
    sendMessage(){
      if (this.message == '')
        return ;
      const time = this.getDate()
      this.$http.post('/chat/addMessage', {roomId:this.roomId, message:this.message, time:time}).then((response) => {
          //console.log(response.data)
          //
      });
      this.$emit('addRoomMsg', {roomId:this.roomId, content:this.message, msg_time:time, user_msg_id:this.user.id});
      this.message = '';
    },

    getDate(){
      const currentdate = new Date();
      const datetime = currentdate.getFullYear() + "/"
                + (currentdate.getMonth()+1)  + "/"
                + currentdate.getDate() + " "
                + currentdate.getHours() + ":"
                + currentdate.getMinutes() + ":"
                + currentdate.getSeconds();
      return (datetime);
    }
  }
}

</script>
