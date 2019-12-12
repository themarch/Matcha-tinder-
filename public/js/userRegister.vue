<template>
      <div class="hero-body">
              <div class="valign-wrapper row login-box" style="width:100%;height:100%">
                  <div class="col card teal lighten-5 hoverable s10 pull-s1 m7 pull-m2 l6 pull-l3">
                      <form method="POST" :action="action">
                          <div class="card-content">
                              <span class="card-title">{{ title }}</span>
                              <div v-for="(value, name, index) in inputs" class="row">
                                  <div class="input-field col s12">
                                      <label :for="name" style="color:black!important">{{ value }}</label>
                                      <input :type="isTypeDefined(index) ? type[index] : ''" class="validate" :name="name" :id="name" required/>
                                  </div>
                              </div>
                              <a href='/reset' class="" v-if="submit.value === 'login'">
                                <div class="mr-l-1 mr-b-6 under-black is-size-7 has-text-weight-normal is-family-primary field">
                                      Mot de passe oublie ?
                                </div>
                              </a>
                          </div>
                          <div class="card-action right-align">
                              <button type="submit" class="btn green waves-effect waves-light" :value="submit.value">
                                {{ submit.name }}</button>
                          </div>
                          <article class="message" v-if="message.length !== 0">
                                    <div class="message-body">
                                        {{message}}
                                    </div>
                          </article>
                          <input type="hidden" name="csrf_token" :value="getCsrfToken()">
                      </form>
                  </div>
              </div>
      </div>
</template>

<script>
  export default {
    props:['action', 'title', 'inputs','submit', 'type', 'message'],
    data() {
      return {

      }
    },

    methods:{
      isTypeDefined(index){
        return (typeof this.type[index] !== 'undefined');
      },

      getCsrfToken(){
        return (document.querySelector("meta[name='csrf-token']").getAttribute("content"));
      }
    }
  }
</script>
