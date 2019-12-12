<template>

  <div class="container" style="max-width:100%!important;width:100%!important">
    <div :class="isMessageLoaded ? 'row hide' : 'row'">
      <div class="input-field col s12 m10 push-m1 l9 push-l1 grey lighten-2 search-border">
        <i class="material-icons prefix">search</i>
        <input placeholder="Rechercher" id="icon_prefix" @keyup="getMatchedUser" v-model="searchInput" type="text" class="validate"
        style="border-bottom:0!important;box-shadow:0 0px!important;width:calc(78% - 1rem)" required>
        <label for="icon_prefix"></label>
        <a @click="cancelSearch"><i class="material-icons mr-t-1" style="float:right">clear</i></a>
      </div>
    </div>
    <div :class="isSearchActivated && searchResult ? 'row' : 'row hide'">
        <div class="col s12 m10 push-m1 l9 push-l1" style="height:100%">
          <div class="card">
            <div class="card-content">
                <span v-if="searchResult.length === 0"> Aucun résultat </span>
                <a @click="findUserRoom(value.user_profil_id)" class="card-title mr-l-3" style="font-size:18px;" v-for="(value, name, index) in searchResult">
                  {{value.firstname}} {{value.lastname}}
                </a>
            </div>
          </div>
        </div>
    </div>
    <div :class="isMessageLoaded || isSearchActivated ? 'row hide' : 'row'">
      <div class="col s12 m10 push-m1 l9 push-l1">
        <ul class="collection" v-for="(value, name, index) in matchedUserChat">
          <li class="row collection-item avatar">
            <div class="col s7 m4 l4">
                <load-async-image needInfo="1" :user-id="value[0].user_profil_id" :profil-id="value[0].user_profil_id"
                img-style="responsive-img rounded-img" :key="value[0].user_profil_id"></load-async-image>
                <online-user-info :user-id="value[0].user_profil_id"></online-user-info>
            </div>
              <div class="col s4 m8 l6 valign-wrapper" style="height:100%;min-height: 15vh;">
                <a @click="loadMessage(value, name)" style="max-width:80%">
                  <h6 class="truncate">
                    <span v-if="value[value.length - 1].hasOwnProperty('user_msg_id') && value[value.length - 1].user_msg_id == user.id">
                      Vous :
                    </span>
                    <span v-else-if="user !== '' && value[value.length - 1].hasOwnProperty('user_msg_id')">
                      {{value[value.length - 1].firstname}} :
                    </span>
                    <span v-if="value[value.length - 1].hasOwnProperty('content')"> {{value[value.length - 1].content}} </span>
                  </h6>
                </a>
                <div class="row" style="border-radius: 50%;background:#ef5350;color:white;padding: 5px 10px;margin-left:4%;"
                v-if="chatNotification[value[0].room_id]">{{chatNotification[value[0].room_id].countMessage}}</div>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div v-if="isMessageLoaded === true">
      <chat-message @showMainChat="resetloadedMessage($event)" :messages="selectedRoom" :user="user"></chat-message>
    </div>
  </div>

</template>

<script>

export default {
  created(){
    this.fetchAll()
    this.fetchConversation()
    this.$checkIfLogged().then(response => {
      this.user = response ? response : false;
    });
  },

  updated(){

  },

  data(){
      return {
        blockedUser:[],
        currentLastname:'',
        currentFirstname:'',
        currentRoomId:'',
        lastname:[],
        firstname:[],
        dstUser:'',
        selectedRoom:false,
        user:'',
        isMessageLoaded:false,
        isSearchActivated:false,
        matchedUserSearch:'',
        chatNotification:'',
        matchedUserChat:'',
        searchInput:'',
        searchResult:false
      }
  },

  methods:{
    search(){
      this.$http.get('/chat/searchMatchedUser').then((response) => {
        if (response.data){
          this.matchedUserSearch = reponse.data
        }
      });
    },

    compareMsgTime(a, b){

      const msgATime = new Date(a.post_msg_time)
      const msgBTime = new Date(b.post_msg_time)

      let compare = 0;
      if (msgATime > msgBTime){
        compare = 1
      }else if (msgATime < msgBTime){
        compare = -1
      }
      return compare
    },

    sortMsgTime(room){
      room.sort(this.compareMsgTime)
      return ;
    },

    fetchConversation(){
      setInterval(() => {this.$http.get('/chat/fetchMatchedUser').then((response) => {
        if (response.data){
          this.matchedUserChat = response.data.matched
          if (this.matchedUserChat !== undefined && this.matchedUserChat[this.currentRoomId])
            this.selectedRoom = this.matchedUserChat[this.currentRoomId]
          for (const property in this.matchedUserChat){
            if (this.matchedUserChat.hasOwnProperty(property)){
                this.sortMsgTime(this.matchedUserChat[property])
              }
            }
            if (response.data.notifications){
              this.chatNotification = response.data.notifications
            }
          }
        });
      }, 1000)
    },

    getMatchedUser(){
      if (this.searchInput !== ''){
          this.isSearchActivated = true
          this.$http.post('/chat/searchMatchedUser', {search:this.searchInput}).then((response) => {
            if (response.data && response.data.hasOwnProperty('searchMatchedUser')){
              this.searchResult = response.data.searchMatchedUser
            }
          });
      }else {
        this.isSearchActivated = false
      }
    },

    fetchAll(){
      this.$http.get('/chat/fetchMatchedUser').then((response) => {
        if (response.data){
          this.matchedUserChat = response.data.matched
          for (const property in this.matchedUserChat){
            if (this.matchedUserChat.hasOwnProperty(property)){
                this.sortMsgTime(this.matchedUserChat[property])
              }
            }
          }
          if (response.data.notifications){
            this.chatNotification = response.data.notifications
          }
      });
    },

    setProfilName(e, room){
      if (e.firstname && e.lastname && !this.matchedUserChat[room].hasOwnProperty('firstname')){
        this.matchedUserChat[room].firstname = e.firstname
        this.matchedUserChat[room].lastname = e.lastname
      }
      this.isUpdated = 1
    },

    cancelSearch(){
      this.isSearchActivated = false
      this.searchInput = ''
    },

    findUserRoom(userId){
        let roomId = 0;
        this.$http.post('/chat/findUserRoom', {userId:userId}).then((response) => {
          roomId = response.data.room_id
          this.currentRoomId = roomId
          this.selectedRoom = response.data.messageExist ? this.matchedUserChat[roomId] : [{room_id:roomId}]
          this.isMessageLoaded = true
          this.isSearchActivated = false
        })
    },

    loadMessage(roomMsg, name){
      this.isMessageLoaded = true
      this.currentRoomId = name
      this.selectedRoom = roomMsg
    },

    resetloadedMessage(event){
      if (event === true){
        this.isMessageLoaded = false
      }
    }
  }
}

</script>
