<template>


  <!-- Dropdown Structure -->
<ul :id="notificationId" ref="dropdown" class='dropdown-content' style="height:400px!important">
    <div v-for="(value, name, index) in notificationData">
      <li v-if="value.type == 'like'" style="line-height:0!important;">
        <a :href="/profil/+value.id" @click="updateNotif">
          <span v-if="value.isSeen === '0' && isSideNav === false" class="new badge" data-badge-caption="NEW"></span>
          <i class="material-icons" style="color:#2196f3">thumb_up</i>
          <span v-if="isSideNav === true">Like : </span>
          <strong>{{value.firstname}}, {{value.lastname}}</strong>
            <span v-if="isSideNav === false">vous a like</span>
        </a>
      </li>
      <li v-if="value.type == 'like'" class="divider" tabindex="-1"></li>
      <li v-if="value.type == 'visiter'">
        <a :href="/profil/+value.id" @click="updateNotif">
          <i class="material-icons" style="color:#4caf50">visibility</i>
          <span v-if="value.isSeen === '0' && isSideNav === false" class="new badge" data-badge-caption="NEW"></span>
          <span v-if="isSideNav === true">Vue : </span>
          <strong>{{value.firstname}}, {{value.lastname}}</strong>
          <span v-if="isSideNav === false">a visite votre profil</span>
        </a>
      </li>
      <li v-if="value.type == 'visiter'" class="divider" tabindex="-1"></li>
      <li v-if="value.type == 'newMessage'" style="line-height:0!important;">
        <a :href="/profil/+value.id" @click="updateNotif">
          <i class="material-icons">message</i>
          <span v-if="isSideNav === true">Message : </span>
          <strong>{{value.firstname}}, {{value.lastname}}</strong>
          <span v-if="isSideNav === false">vous a envoye um message</span>
        </a>
      </li>
      <li v-if="value.type == 'newMessage'" class="divider" tabindex="-1"></li>
      <li v-if="value.type == 'match'" style="line-height:0!important;">
        <a :href="/profil/+value.id" @click="updateNotif">
          <span v-if="value.isSeen === '0' && isSideNav === false" class="new badge" data-badge-caption="NEW"></span>
          <i class="material-icons" style="color:#e53935">favorite</i>
          <span v-if="isSideNav === true">Match : </span>
          <strong>{{value.firstname}}, {{value.lastname}}</strong>
          <span v-if="isSideNav === false">vous a envoye un like en retour</span>
        </a>
      </li>
      <li v-if="value.type == 'match'" class="divider" tabindex="-1"></li>
      <li v-if="value.type == 'unmatch'">
        <a :href="/profil/+value.id" @click="updateNotif">
          <span v-if="value.isSeen === '0' && isSideNav === false" class="new badge" data-badge-caption="NEW"></span>
          <i class="material-icons" style="color:#2196f3">thumb_down</i>
          <span v-if="isSideNav === true">Dislike : </span>
          <strong>{{value.firstname}}, {{value.lastname}}</strong>
          <span v-if="isSideNav === false">matche ne vous like plu</span>
        </a>
      </li>
      <li v-if="value.type == 'unmatch'" class="divider" tabindex="-1"></li>
    </div>
</ul>

</template>


<script>

// closeOnClick : false
// qd on a une notif recalcul le drop down: instance.recalculateDimensions();
// requete toute les 1 secondes.
  export default{
      props:['notificationId', 'notificationData', 'isSideNav'],


      data(){
        return {
          isDropOpen:false
        }
      },

      watch:{
        notificationData(){
          if (this.$refs.dropdown.style.display == 'block'){
            this.isDropOpen = true
          }
          if (this.isDropOpen && this.$refs.dropdown.style.display !== 'block'){
            this.$http.post('/notification/set', {notification:this.notificationData});
            this.isDropOpen = false
          }
        }
      },

      data(){
        return {

        }
      },

      methods:{
        // ----> update juste cette notif ou toute les notifs ? 
        updateNotif(){
          this.$http.post('/notification/set', {notification:this.notificationData});
        }
      }
  }

</script>
