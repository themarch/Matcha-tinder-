<template>
  <div>
    <nav>
      <div class="nav-wrapper teal lighten-1">
        <a href="/" class="brand-logo"><i class="material-icons">cloud</i>Matcha</a>
        <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
        <ul class="right hide-on-med-and-down">
          <li v-if="isAuth() && this.user !== ''"><a href="/profil"><i class="material-icons left">person</i>Profil</a></li>

          <ul id='dropdown1' class='dropdown-content' v-if="isAuth">
            <li><a href="/settings"><i class="material-icons">settings_applications</i>Compte</a></li>
            <li><a href="/profil/edit"><i class="material-icons">account_circle</i>Profil</a></li>
          </ul>

          <li v-if="isAuth() && this.user !== ''"><a class='dropdown-trigger' data-target='dropdown1'>
              <i class="material-icons left">settings</i>Paramétres</a>
          </li>
          <!-- <li><a href="/settings"><i class="material-icons">settings</i></a></li> -->
          <li>
            <a href="/search"><i class="material-icons left">search</i>
              Rechercher
            </a>
          </li>
          <li v-if="isAuth() && this.user !== ''">
            <a class='dropdown-trigger' data-target='dropdown3'>
              <i class="material-icons left">notifications</i>
              <small v-if="notificationCount !== 0" class="notification-badge">{{notificationCount}}</small>
            </a>
          </li>
          <notifications :is-side-nav="false" notification-id="dropdown3"
          :notification-data="notifications" v-if="isAuth"></notifications>
          <li v-if="isAuth() && this.user !== ''">
            <a href="/chat">
              <i class="material-icons left">chat_bubble</i>
              <small v-if="messageCount !== 0" class="notification-badge" style="margin:0 -1.3em;right:0">{{messageCount}}</small>
            </a>
          </li>
          <li v-if="!isAuth() && this.user !== ''">
            <a href="/login" class="btn green waves-effect waves-light">Se connecter</a>
          </li>
          <li v-if="!isAuth() && this.user !== ''">
            <a href="/register" class="btn green waves-effect waves-light">S'inscrire</a>
          </li>
          <li v-if="isAuth() && this.user !== ''">
            <a href="/logout" class="btn green waves-effect waves-light">Déconnexion</a>
          </li>
        </ul>
      </div>
    </nav>
    <ul class="sidenav" id="mobile-demo">
      <li class="blue mr-b-3">
        <a href="/" class="brand-logo white-text"><i class="material-icons white-text">cloud</i>Matcha</a>
      </li>
      <li v-if="isAuth() && this.user !== ''"><a href="/profil"><i class="material-icons">person</i>Profil</a></li>
      <ul class="collapsible" v-if="isAuth()">
        <li>
          <div class="collapsible-header mr-l-5"><i class="material-icons">settings</i><li class="mr-l-5" style="color:rgba(0,0,0,0.87);font-weight:500;height:48px;font-size:14px">Paramétres</li></div>
          <div class="collapsible-body">
            <li><a href="/settings" class="mr-l-4"><i class="material-icons">settings_applications</i>Compte</a></li>
            <li><a href="/profil/edit" class="mr-l-4"><i class="material-icons">account_circle</i>Profil</a></li>
          </div>
        </li>
      </ul>
      <li v-if="isAuth() && this.user !== ''">
        <a class='dropdown-trigger' data-target='dropdown4'>
          <i class="material-icons">notifications</i>
          Notifications
          <small v-if="notificationCount !== 0" class="notification-badge" style="left:-150px;color:white;background:#ef5350">{{notificationCount}}</small>
        </a>
      </li>
      <notifications :is-side-nav="true" notification-id="dropdown4"
      :notification-data="notifications" v-if="isAuth()"></notifications>
      <li v-if="isAuth() && this.user !== ''">
        <a href="/chat">
          <i class="material-icons">chat_bubble</i>
          Chat
          <small v-if="messageCount !== 0" class="notification-badge" style="left:-100px;color:white;background:#ef5350">{{messageCount}}</small>
        </a>
      </li>
      <li><a href="/search" class="mr-t-3"><i class="material-icons">search</i>Rechercher</a></li>
      <hr>
      <li v-if="!isAuth() && this.user !== ''">
        <a href="/login" class="btn green waves-effect waves-light">
          Se connecter
        </a>
      </li>
      <li v-if="!isAuth() && this.user !== ''">
        <a href="/register" class="btn green waves-effect waves-light">
          S'inscrire
        </a>
      </li>
      <li v-if="isAuth() && this.user !== ''">
        <a href="/reset" class="btn green waves-effect waves-light">Déconnexion</a>
      </li>
    </ul>
  </div>
</template>

<script>

// handle les notifs + le chat + profil user.
export default{

    created(){
      this.getNotification()
      this.$checkIfLogged().then(response => {
        this.user = response ? response : false;
      });
      setInterval(() => this.getNotification(), 1000);
    },

    data(){
        return {
            user:'',
            isDropDownDisplayed:false,
            notifications:'',
            notificationCount:0,
            messageCount:0
        }
    },

    updated(){
      if (this.user.isAuth && this.isDropDownDisplayed === false){
        this.initDropDown();
        this.initCollapse();
        this.isDropDownDisplayed = true;
      }
    },

    methods:{
      initDropDown(){
        var elems = document.querySelectorAll('.dropdown-trigger');
        var instances = M.Dropdown.init(elems, {
          inDuration: 350,
          outDuration: 350,
          coverTrigger: false,
          constrainWidth: false
        });
      },

      initCollapse(){
        var elems = document.querySelectorAll('.collapsible');
        var instances = M.Collapsible.init(elems, {
          inDuration: 350,
          outDuration: 350,
          accordion: false
        });
      },

      getNewChatMessage(){
        // is_read col dans message.
        // new message à chaque fois => fetch les messages des rooms + compte ceux qui ne sont pas lues.
      },

      getNotification(){
        this.$http.get('/notification/get').then((response) => {
          if (response.data && response.data.hasOwnProperty('notifications')){
              this.notifications = response.data.notifications
              this.notificationCount = response.data.notifications.notifCount
              this.messageCount = response.data.notifications.hasOwnProperty('countMessage') ? response.data.notifications.countMessage : 0;
              delete this.notifications.notifCount
              delete this.notifications.countMessage
          }
        });
      },

      isAuth(){
        return (this.user !== false && this.user.isAuth == 1 && this.user.id !== null && this.user !== '')
      }
    }
}

</script>
