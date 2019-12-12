<template>
      <a :href="/profil/+profilId" target="_blank"><img :src="link" alt="" :class="imgStyle"/>
        <div v-if="needInfo">
          {{lastname}} {{firstname}}
        </div>
      </a>
</template>


<script>
export default {

  props:['profilId', 'userId', 'imgStyle', 'needInfo', 'needWatch'],

  created(){
    this.loadPic(this.profilId)
  },

  /*mounted(){
    if (this.needWatch === true){
      this.$watch('profilId', () => {
        this.loadPic(this.profilId)
      }, {immediate:true});
    }
  },*/

  data(){
    return {
      link:'',
      lastname:'',
      firstname:''
    }
  },

  methods:{
    getProfilPicById(id){
      return new Promise((resolve, reject) => {
        this.$http.post('/profil/getProfilPicById', {user_id:id}).then((response) => {
          resolve(response.data)
        });
      })
    },

    async fetchImgPic(id){
      return await this.getProfilPicById(id)
    },

    loadPic(id){
      this.fetchImgPic(id).then((response) => {
        if (response!== null && response!== undefined){
          this.$emit('imageLoaded')
          this.link = 'data:image/png;base64,'+response.path;
          this.lastname = response.lastname
          this.firstname = response.firstname
        }
      });
    },

    destroyed(){

    }
  }
}
</script>
