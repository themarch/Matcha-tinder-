import Vue from 'vue'
import {
  utils
} from './utils.js'
import navBar from './navBar.vue'
import searchMatch from './searchMatch.vue'
import userRegister from './userRegister.vue'
import userSettings from './userSettings.vue'
import userProfil from './userProfil.vue'
import userProfilInfo from './userProfilInfo.vue'
import userProfilEdit from './userProfilEdit.vue'
import notifications from './notifications.vue'
import loadAsyncImage from './loadAsyncImage.vue'
import onlineUserInfo from './onlineUserInfo.vue'
import chatMessage from './chatMessage.vue'
import inputChatMessage from './inputChatMessage.vue'
import getUserProfilInfo from './getUserProfilInfo.vue'
import geoLoc from './geoLoc.vue'
import error from './error.vue'
import chat from './chat.vue'
import seeder from './seeder.vue'
import axios from 'axios'
import {
  setupAxios
} from './setupAxios'

Vue.prototype.$noUiSlider = noUiSlider;

setupAxios(axios);
Vue.prototype.$utils = utils
Vue.prototype.$http = axios;
Vue.prototype.$checkIfLogged = function() {
  return new Promise((resolve, reject) => {
    axios.get('/isAuth')
      .then(response => {
        resolve(response.data.user);
      })
      .catch(error => {
        reject(error.response.data);
      });
  })
};

Vue.prototype.$isAuth = function() {
  this.$checkIfLogged().then(response => {
    this.isAuth = response ? response : false;
  })
};

Vue.component('nav-bar', navBar);
Vue.component('geo-loc', geoLoc);
Vue.component('search-match', searchMatch);
Vue.component('user-register', userRegister);
Vue.component('user-settings', userSettings);
Vue.component('user-profil', userProfil);
Vue.component('user-profil-info', userProfilInfo);
Vue.component('user-profil-edit', userProfilEdit);
Vue.component('notifications', notifications);
Vue.component('seeder', seeder);
Vue.component('load-async-image', loadAsyncImage);
Vue.component('online-user-info', onlineUserInfo);
Vue.component('chat', chat);
Vue.component('chat-message', chatMessage);
Vue.component('input-chat-message', inputChatMessage);
Vue.component('get-user-profil-info', getUserProfilInfo);
Vue.component('error', error);

new Vue({
  el: '#app'
  //render: h => h(App)
})