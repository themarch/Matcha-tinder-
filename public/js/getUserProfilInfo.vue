<template>

  <div class="col s12 m5 l4">
    {{firstname}} {{lastname}}
  </div>

</template>


<script>

export default{
  props:['profilId', 'firstname', 'lastname'],

  created(){

  },

  data(){
    return {

    }
  }
}


</script>
