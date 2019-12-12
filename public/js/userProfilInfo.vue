<template>
  <div class="row">
    <ul class="col push-l1 l8 offset-l1 push-m1 m10 s12 collapsible expandable" style="padding:0!important;margin-right:0!important">
      <li>
        <div class="collapsible-header"><i class="material-icons">thumb_up</i>Utilisateurs qui ont like votre profil
          <span class="new badge" v-if="visiterLikesCount > 0" :data-badge-caption="likesFormat">{{visiterLikesCount}}</span>
        </div>
        <div class="collapsible-body">
          <ul class="collection">
            <li class="row ml-v-align collection-item avatar" v-for="(value, name, index) in visiterLikes">
              <div class="col s6 m4 l4">
                <load-async-image :user-id="value.user_id" :profil-id="value.profil_id" img-style="center-img responsive-img rounded-img"></load-async-image>
              </div>
              <div class="col s12 m7 l7">
                <p class="black-text">
                  {{value.firstname}} {{value.lastname}} <br>
                  <span class="black-text" v-if="value.age">
                    {{value.age}} ans
                  </span>
                  <br>
                  <span class="black-text">
                    {{value.localisation}}
                  </span>
                </p>
                <div class="row mr-t-4" v-if="visiterLikesTags !== ''">
                  <div class="chip blue white-text" v-for="(value, name, index) in visiterLikesTags[value.user_id]">
                    #{{value.name}}
                  </div>
                </div>
                <online-user-info :user-id="value.user_id"></online-user-info>
              </div>
            </li>
          </ul>
        </div>
      </li>
      <li>
        <div class="collapsible-header"><i class="material-icons">remove_red_eye</i>Utilisateurs qui ont visités votre profil
          <span class="new badge" v-if="visiterViewsCount > 0" :data-badge-caption="viewFormat">{{visiterViewsCount}}</span>
        </div>
        <div class="collapsible-body">
          <ul class="collection">
            <li class="row ml-v-align collection-item avatar" v-for="(value, name, index) in visiterViews">
              <div class="col s6 m4 l4">
                <load-async-image :user-id="value.user_id" :profil-id="value.profil_id" img-style="center-img responsive-img rounded-img"></load-async-image>
              </div>
              <div class="col s8 m7 l7">
                <p class="black-text">
                  {{value.firstname}} {{value.lastname}} <br>
                  <span class="black-text" v-if="value.age">
                    {{value.age}} ans
                  </span>
                  <br>
                  <span class="black-text">
                    {{value.localisation}}
                  </span>
                </p>
                <div class="row mr-t-4" v-if="visiterViewsTags !== ''">
                  <div class="chip blue white-text" v-for="(value, name, index) in visiterViewsTags[value.user_id]">
                    #{{value.name}}
                  </div>
                </div>
                <online-user-info :user-id="value.user_id"></online-user-info>
              </div>
            </li>
          </ul>
        </div>
      </li>
      <li>
        <div class="collapsible-header"><i class="material-icons">block</i>Utilisateurs qui ont été bloqués
          <span class="new badge" v-if="visiterLikesCount > 0" :data-badge-caption="blockFormat">{{blockedCount}}</span>
        </div>
        <div class="collapsible-body">
          <ul class="collection">
            <li class="row ml-v-align collection-item avatar" v-for="(value, name, index) in blockedUsers">
              <div class="col s6 m4 l4">
                <load-async-image :user-id="value.user_id" :profil-id="value.profil_id" img-style="center-img responsive-img rounded-img"></load-async-image>
              </div>
              <div class="col s8 m7 l7">
                <a class="btn-floating btn-small red lighten-1 waves-effect waves-light right" @click="unblock(index, value.user_id)"><i class="large material-icons">close</i></a>
                <p class="black-text">
                  {{value.firstname}} {{value.lastname}} <br>
                </p>
                <online-user-info :user-id="value.user_id"></online-user-info>
              </div>
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</template>


<script>

export default {
      created(){
        this.getProfilViews();
        this.getProfilLike();
        this.getBlockedUsers();
      },

      data(){
        return {
          likesFormat:'',
          viewFormat:'',
          visiterViewsTags:'',
          visiterViews:'',
          visiterViewsCount:0,
          visiterLikes:'',
          visiterLikesTags:'',
          visiterLikesCount:0,
          blockedUsers:'',
          blockedCount:0,
          blockFormat:''
        }
      },

      methods:{
        getProfilLike(){
          this.$http.get('/profil/getProfilLikes').then((response) => {
            if (response.data.visiterLikes !== null && response.data.visiterLikes !== undefined){
              this.visiterLikes = response.data.visiterLikes;
              this.visiterLikesTags = response.data.visiterLikes.likesTags;
              delete this.visiterLikes['likesTags'];
              this.visiterLikesCount = Object.keys(this.visiterLikes).length
              this.likesFormat = this.visiterLikesCount > 1 ? 'likes' : 'like'
            }
          });
        },

        getProfilViews(){
          this.$http.get('/profil/getProfilViews').then((response) => {
            if (response.data.visiterViews !== null){
              this.visiterViews = response.data.visiterViews;
              this.visiterViewsTags = response.data.visiterViews.visiterTags;
              delete this.visiterViews['visiterTags'];
              this.visiterViewsCount = Object.keys(this.visiterViews).length
              this.viewFormat = this.visiterViewsCount > 1 ? 'vues' : 'vue'
            }
          });
        },

        getBlockedUsers(){
          this.$http.get('/block/getBlockedUsers').then((response) => {
            if (response.data && response.data.hasOwnProperty('blockedUsers')){
              this.blockedUsers = response.data.blockedUsers;
              this.blockedCount = Object.keys(this.blockedUsers).length
              this.blockFormat = this.blockedCount > 1 ? 'blockés' : 'blocké'
            }
          });
        },

        unblock(index, userId){
          this.blockedUsers.splice(index, 1);
          this.$http.post('/block/unblock', {profilId:userId});
        }
      }
  }
</script>
