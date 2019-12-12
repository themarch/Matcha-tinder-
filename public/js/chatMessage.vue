<template>
  <div :class="hideMessage ? 'hide row' : 'row'" style="width:100%;height:100%">
    <div class="row">
      <div class="col">
        <a @click="showHistoric"><i class="material-icons medium left">keyboard_arrow_left</i>Revenir en arriére</a>
      </div>
    </div>
    <div class="col s12 m10 push-m1 l8 offset-l2" style="height:100%">
      <ul class="collection" style="overflow-y:auto!important;height:calc(100vh - 25rem);border:none;" ref="chat" @mousewheel="stopScroll = true">
        <li class="row collection-item avatar" v-if="Array.isArray(messages) && messages[0].hasOwnProperty('content')">
          <div class="row" style="padding:0!important;margin:0!important" v-for="(value, name, index) in messages">
            <div class="row center" v-if="value.hasOwnProperty('user_msg_id')" style="font-size:12px;margin-top:18px;margin-bottom:18px">
              <span style="color:#bdbdbd">
                {{new Date(value.msg_time).toLocaleDateString("fr-FR", { month: 'long', day: 'numeric', hour: "numeric", minute:"numeric"})}}
              </span>
            </div>
            {{index}}
            <div class="col s12 m8 offset-m2 l8 offset-l2">
                <div :class="value.hasOwnProperty('user_msg_id')
                && value.user_msg_id == user.id ? 'chip blue white-text right' : 'chip grey lighten-2 black-text'" style="max-width:80%;overflow-wrap: break-word;max-height:80%;height: auto;">
                  {{value.content}}
                </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <input-chat-message @addRoomMsg="addMessageToRoom($event)" :room-id="Array.isArray(messages) && messages[0].room_id" :user="user"></input-chat-message>
  </div>
</template>


<script>

export default{

  props:['messages','user'],

  created(){
    if (this.hideMessage !== true){
      this.roomId = this.messages[0].room_id
      this.$http.post('/chat/updateNotification', {userId:this.user.id, roomId:this.roomId});
    }
    this.initalMsgLength = this.messages.length
  },

  mounted(){
    this.scrollBottom()
  },

  updated(){
    if (this.messages.length > this.initalMsgLength && this.hideMessage !== true){
      this.$http.post('/chat/updateNotification', {userId:this.user.id, roomId:this.roomId});
      this.scrollBottom()
      this.initalMsgLength = this.messages.length
    }
  },

  data(){
    return {
      initalMsgLength:0,
      stopScroll:false,
      updatedHeight:'',
      initialHeight:'',
      hideMessage:'',
      showMainChat:'',
      userScroll:false,
      roomId:''
    }
  },

  methods:{

    showHistoric(){
      this.hideMessage = true
      this.showMainChat = true
      this.$emit('showMainChat', this.showMainChat)
    },

    addMessageToRoom(e){
      if (this.messages !== '' && this.messages !== undefined){
        this.messages.push(e)
        this.scrollBottom()
      }
    },

    scrollBottom(){
      const elem = this.$refs.chat
      elem.scrollTop = elem.scrollHeight
    }
  }
}

</script>
