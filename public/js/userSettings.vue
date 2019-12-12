<template>

    <div class="row valign-wrapper" style="width:100%;height:100%">
      <div class="col card teal lighten-5 hoverable s12 m8 pull-m2 l6 pull-l3">
        <div class="card-content" style="width:100%;height:100%">
          <div class="mr-t-3 mr-b-2" v-for="(value, name, index) in data">
            <span class="card-title">{{ title[name] }} </span>
            <div :class="classManager[name] ? offClass : logoClass" v-on:click="activateData(name)">
              <p v-if="name === 'password'" style="width:10%;font-size:1em"> ***** </p>
              <p v-else style="width:10%;font-size:1em"> {{ data[name] }} </p>
              <p class="right-align" style="width:100%;font-size:1vw!important">
                <a class="btn-small green waves-effect waves-light basic-txt" href="#">
                  <i class="material-icons right">edit</i>Modifier</a>
                </p>
              </div>
              <div :class="classManager[name] ? onClass : offClass">
                <label :for="name" style="color:black!important"></label>
                <input :type="getInputType(name)" v-model="data[name]" class="validate" :name="name" :id="name" />
                <div class="card-action right-align valign-wrapper" style="border-top:none!important; width:100%;">
                  <button v-on:click="sendData(name)" class="right-align mr-r-3 basic-txt btn-small green waves-effect waves-light" value="">Confirmer</button>
                  <button v-on:click="cancelData(name)" class="center-align basic-txt btn-small green waves-effect waves-light" value="">Annuler</button>
                </div>
              </div>
            </div>
            <div class="row">
              <article class="message col s12 m12 l12" style="padding:0!important" v-if="message.length > 0 && Array.isArray(message)">
                <div class="message-body">
                  <p v-for="(value, name, index) in message">
                    {{value}}
                  </p>
                </div>
              </article>
           </div>
          </div>
        </div>
      </div>
  </template>

<script>
  export default {
      props:['title'],

      created() {
        this.$http.get('/settings/getUserInfo').then((response) => {
          if (response.data && typeof response.data === 'object')
          {
            this.data = response.data
            this.data.password = '******'
            this.classManager = this.initObject(this.data, false)
            this.tmpData = this.initObject(this.data, '')
          }
        });
      },

      data(){
        return {
          message:'',
          tmpData: '',
          data: '',
          onClass: 'input-field col s12',
          logoClass:'valign-wrapper',
          offClass:'none',
          classManager: ''
        }
      },

      methods:{
        activateData(key){
          this.copyData();
          if (this.classManager.hasOwnProperty(key)){
            this.classManager[key] =  this.classManager[key] ? false : true;
          }
        },

        resetData(key){
          if (this.classManager.hasOwnProperty(key))
            this.classManager[key] = false;
        },
        cancelData(key){
          this.resetData(key);
          if (this.data.hasOwnProperty(key) && this.tmpData.hasOwnProperty(key))
              this.data[key] = this.tmpData[key];
        },
        isEmptyObject(obj){
          return (Object.entries(obj).length === 0 && obj.constructor === Object);
        },
        copyData(){
          this.tmpData = Object.assign({}, this.data)
        },

        initObject(src, type){
          let dst = {};
          Object.entries(src).forEach(
            ([key, value]) => dst[key] = type
          );
          return (dst);
        },

        sendData(name){
          const routeType = name.charAt(0).toUpperCase() + name.substring(1)
          if (!this.data[name])
            return ;
          this.$http.post('/settings/new'+routeType, {[`${name}`]:this.data[name]}).then((response) => {
            if (response.data && response.data.hasOwnProperty('Message') && response.data.Message.length > 0
            && Array.isArray(response.data.Message)){
              this.message = response.data.Message
              this.cancelData(name);
            }else {
              this.message = '';
              this.resetData(name);
            }
          });
        },

        getInputType(name){
          if (name == 'email')
            return ('email');
          if (name == 'password')
            return ('password');
          return ('text');
        }
      }
  }
</script>
