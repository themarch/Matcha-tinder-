<template>
  <div class="row">
    <article class="message col s12 m12 l12" style="padding:0!important" v-if="message && message.length > 0">
      <div class="message-body">
        <p>
          {{message}}
        </p>
      </div>
    </article>
  </div>
</template>


<script>

export default {
    props:['message'],

    data(){
      return {

      }
    }
}
</script>
