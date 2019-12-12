<template>
  <div class="row">
    <span v-if="last_visited !== false && elaspedTimeLastVisited <= 45 && last_visited !== ''"><i class="material-icons" style="color:#4caf50;">lens</i>
      En ligne
    </span>
    <span v-if="last_visited === false || elaspedTimeLastVisited > 45 && last_visited !== ''">
      <i class="material-icons" style="color:#dc4c46;">lens</i>Hors ligne
      <div class="col s12 m12 l12" v-if="elaspedTimeLastVisited !== false">
        <span style=""></i>Derniére connexion {{new Date(last_visited).toLocaleDateString("fr-FR", { month: 'long', day: 'numeric', hour: "numeric", minute:"numeric"})}}</span>
      </div>
    </span>
  </div>
</template>


<script>
export default {
  props:['userId'],

  created(){
    this.isOnline()
  },

  data(){
    return {
      last_visited:'',
      elaspedTimeLastVisited:''
    }
  },

  methods:{
    isOnline(){
      this.$http.post('/profil/isOnline', {profilId:this.userId}).then((response) => {
        if (response.data && response.data.last_visited !== null){
          this.last_visited = response.data.last_visited
          this.elaspedTimeLastVisited = response.data.minDiff
        }else {
          this.last_visited = false
          this.elaspedTimeLastVisited = false
        }
      })
    }
  }
}

</script>
