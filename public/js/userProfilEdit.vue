<template>
  <div class="row valign-wrapper" style="width:100%;height:100%">
    <div id="modal1" class="teal lighten-5 modal modal-fixed-footer">
      <div class="modal-content" style="margin:0!important">
        <div class="row" v-if="fetchedImg.length > 0">
          <div class="col s12 m4 l4 mr-t-1" v-for="(value, name, index) in fetchedImg">
            <div class="image is-3by2">
              <img :src="'data:image/png;base64,'+value.path" alt="" class="responsive-img" :name="value.name">
              <a class="tag cyan cyan lighten-4 delete-overlay"
              style="border-radius:0!important;text-decoration:none;margin:0!important;padding:0!important">
                <label class="mr-l-4">
                  <!-- v-if : si image de profil => checked -->
                  <input type="checkbox" :checked="1" @click="addProfilPic" :name="value.name" v-if="value.name === profilPicName"></input>
                  <input type="checkbox" :checked="0" @click="addProfilPic" :name="value.name" v-else="value.name !== profilPicName"></input>
                  <span></span>
                </label>
              </a>
            </div>
          </div>
        </div>
        <div class="row" v-if="fetchedImg.length === 0" style="text-align: center;margin:0!important;padding:3%!important">
          <div class="card teal">
            <div class="card-content" style="width:100%">
              <span class="white-text">
                Veuillez ajouter des images pour changer la photo de profil.
              </span>
            </div>
            <div class="card-action center-align">
              <button type="submit" class="btn green waves-effect waves-light" @click="closeModal">
                Fermer</button>
            </div>
          </div>
        </div>
      </div>
      <!---<div class="modal-footer" style="text-align:center">
        <a href="#!" @click="closeModal" class="waves-effect waves-green btn blue">Annuler</a>
      </div> -->
    </div>
    <div class="col card teal lighten-5 hoverable s12 m12 l8 pull-l2">
      <div class="card-content" style="width:100%;height:100%">
        <span class="card-title">Genre</span>
        <p>
          <label class="mr-r-1">
            <input type="checkbox" :checked="isGenreSelected === true && genreName =='homme'" @click="handleBox" value="homme"/>
            <span>Homme</span>
          </label>
          <label class="mr-r-1">
            <input type="checkbox" :checked="isGenreSelected === true && genreName =='femme'" @click="handleBox" value="femme"/>
            <span>Femme</span>
          </label>
        </p>
        <span class="card-title mr-t-2">Orientation sexuelle</span>
        <p>
          <label class="mr-r-1">
            <input type="checkbox" :checked="isOrientationSelected === true && orientationName == 'homosexuel'" @click="handleBox" value="homosexuel"/>
            <span>Homosexuel</span>
          </label>
          <label class="mr-r-1">
            <input type="checkbox" :checked="isOrientationSelected === true && orientationName == 'heterosexuel'" @click="handleBox" value="heterosexuel"/>
            <span>Hétérosexuel</span>
          </label>
          <label class="mr-r-1">
            <input type="checkbox" :checked="isOrientationSelected === true && orientationName == 'bisexuel'" @click="handleBox" value="bisexuel"/>
            <span>Bisexuel</span>
          </label>
        </p>

        <span class="card-title mr-t-2">Localisation</span>
        <div class="col s12 m12 l12" v-if="isModifLocalisation === true" style="padding:0!important">
          <span>Adresse</span>
          <input type="text" v-model="address" class="validate" name="localisation" id="localisation" required/>
          <span>Ville</span>
          <input type="text" v-model="city" class="validate" name="localisation" id="localisation" required/>
          <span>Code postal</span>
          <input type="text" v-model="code" class="validate" name="localisation" id="localisation" required/>
          <button class="btn btn-small green waves-effect waves-light" @click="modifLocalisation">Confirmer</button>
          <button class="btn btn-small green waves-effect waves-light" @click="isModifLocalisation = false">Annuler</button>
        </div>

        <div class="col s12" v-if="isModifLocalisation === false" style="padding:0!important">
          <p style="font-size:1em"> {{city && country ? city+', '+country : ''}}
            <button class="right btn btn-small green waves-effect waves-light" @click="isModifLocalisation = true"><i class="material-icons right">edit</i>Editer</button>
          </p>
        </div>
        <span class="card-title mr-t-2">Biographie</span>
        <div class="row" style="margin-bottom:0!important">
          <div class="col s12 m12 l12 mr-t-1" v-if="isModifBio">
            <textarea @change="resizeTextarea" class="cyan lighten-4 materialize-textarea" :style="{height:textAreaHeight, background:'white'}" v-model="bio"></textarea>
            <button type="submit" class="btn btn-small green waves-effect waves-light" @click="modifBio">Confirmer</button>
            <button type="submit" class="btn btn-small green waves-effect waves-light" @click="resetBio">Annuler</button>
          </div>
          <div class="input-field col s12" v-if="isModifBio === false">
            <p class="mr-b-2 p-wbreak black-text"> {{bio}}
              <button type="submit" class="btn btn-small green right waves-effect waves-light" @click="showModifBio"><i class="material-icons right">edit</i>Editer</button>
            </p>
          </div>
        </div>
        <span class="card-title">Tags</span>
        <div class="chips chips-autocomplete mr-b-1" id="chips-filter" style="margin-top:0!important"></div>
        <span class="card-title">Photo de profil</span>
        <div class="row">
          <div class="col s12 m4 l4 mr-t-1">
            <div class="image is-3by2" v-if="profilPicSrc">
              <img :src="'data:image/png;base64,'+profilPicSrc" alt="" class="responsive-img" :name="profilPicName">
              <a class="tag is-info text-img" style="border-radius:0!important;text-decoration:none" @click="openModal">
                <i class="material-icons" style="color:black;float:right">edit</i>
                Editer
              </a>
            </div>
          </div>
        </div>

        <span class="card-title">Images</span>
        <div class="row">
          <div class="col s12 m4 l4 mr-t-1" v-for="(value, name, index) in fetchedImg">
            <div class="image is-3by2">
              <img :src="'data:image/png;base64,'+value.path" alt="" class="responsive-img" :name="value.name">
              <a class="tag is-info delete-overlay" style="border-radius:0!important;text-decoration:none" @click="deleteImg" :name="value.name">
                <i class="material-icons" style="color:black">delete</i>
                Supprimer
              </a>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="file-field input-field mr-t-1 mr-l-2">
            <div class="btn">
              <span>Ajouter</span>
              <input type="file" ref="fileInput" accept=".png, .jpg, .jpeg" @change="previewImage"/>
            </div>
            <div class="file-path-wrapper">
              <input class="file-path validate" type="text">
            </div>
          </div>
        </div>
        <div class="row" v-show="isPreview">
          <div class="col s12 m4 l4 mr-t-1">
            <img :src="imageData" alt="" class="responsive-img">
          </div>
          <div class="card-action right-align">
            <button type="submit" class="mr-t-2 btn green waves-effect waves-light" @click="addImg">
              Confirmer</button>
            </div>
          </div>
          <error :message="error"></error>
      </div>
    </div>
  </div>
</template>

<script>
  export default{

    created(){
      this.$http.get('/profil/edit/getProfilData').then((response) => {
        this.profilData = response.data
        if (this.profilData.hasOwnProperty('genre')){
          this.isGenreSelected = true
          this.genreName = this.profilData.genre
        }
        if (this.profilData.hasOwnProperty('orientation') && this.profilData.orientation){
          this.isOrientationSelected = true
          this.orientationName = this.profilData.orientation
        }
        if (this.profilData.hasOwnProperty('bio') && this.profilData.bio){
          this.bio = this.profilData.bio
        }
        if (this.profilData.hasOwnProperty('localisation') && this.profilData.localisation){
          this.city = this.profilData.localisation.split(',')[0]
          this.country = this.profilData.localisation.split(',')[1].trim()
        }
      });
      this.$http.get('/profil/edit/getTag').then((response) => {
        if (response.data){
          this.initChips(this.createInitalTags(response.data))
        }
      });
      this.$http.get('/profil/edit/getImg').then((response) => {
        if (response.data && Array.isArray(response.data)){
          this.fetchedImg = response.data;
        }
      });
      this.$http.get('/profil/edit/getProfilPic').then((response) => {
        if (response.data && response.data.name !== null && response.data.path !== null){
          this.profilPicName = response.data.name;
          this.profilPicSrc = response.data.path;
        }
      });

    },

    mounted(){
      this.initChips()
    },

    data(){
      return {
        city:'',
        country:'',
        fetchedImg:[],
        isProfilPicSelected:false,
        profilPicSrc:'',
        profilPicName:'',
        profilData:'',
        instance:'',
        input:'',
        isPreview:false,
        imageData: '',
        imageName:'',
        imgSize:'',
        tags:[],
        bio:'',
        tmpBio:'',
        textAreaHeight:'',
        isModifBio:false,
        genreName:'',
        orientationName:'',
        isGenreSelected:false,
        isOrientationSelected:false,
        isModifLocalisation:false,
        haveBio:false,
        error:'',
        address:'',
        code:''
      }
    },

    methods:{
      async initChips(data){
        var elems = document.getElementById('chips-filter')
        var vm = this, src = data;
        const tags = await this.getMostUsedtags()
        let tagList = {};
        if (!(tags === undefined || tags === null || !Array.isArray(tags) || tags.length === 0)){
          tags.forEach((value) => {
            tagList[value.name] = null
          })
        }
        var options = {
          data: src ? src : [],
          placeholder: 'Entrer un tag',
          secondaryPlaceholder: '+Tag',
          autocompleteOptions: {
            data: tagList,
            limit: 10
          },
          onChipAdd(e, data){ vm.chipAdded(e, data); },
          onChipDelete(e, data) { vm.chipDelete(e, data); }
        };
        var instances = M.Chips.init(elems, options);
      },

      chipAdded(e, data){
        this.$http.post('/profil/edit/addTag', {name:data.childNodes[0].textContent});
      },

      chipDelete(e, data){
        this.$http.post('/profil/edit/deleteTag', {name:data.childNodes[0].textContent});
      },

      addProfilPic(e){
        if (!e.target.checked && this.profilPicName === e.target.name)
          this.profilPicName = '';
        if (e.target.checked){
          this.instance.close();
          this.profilPicName = e.target.name
          this.$http.post('/profil/edit/addProfilImg', {name:this.profilPicName}).then((response) => {
            if (response.data.path)
              this.profilPicSrc = response.data.path
          });
        }
      },

      createInitalTags(data){
        let dst = [];
        for (let i = 0; i < data.length; i++){
          if (data[i].hasOwnProperty('name'))
            dst = [...dst, {tag:data[i].name}];
        }
        return (dst);
      },

      handleBox(event){
        if (event.target.checked === true){
          if (event.target.value == "homme" || event.target.value == "femme")
          {
            this.isGenreSelected = true;
            this.genreName = event.target.value;
            this.$http.post('/profil/edit/modif', {genre:event.target.value});
          }
          if (event.target.value == "homosexuel" || event.target.value == "bisexuel" || event.target.value == "heterosexuel")
          {
            this.isOrientationSelected = true;
            this.orientationName = event.target.value;
            this.$http.post('/profil/edit/modif', {orientation:event.target.value});
          }
        }
        if (event.target.checked === false){
          if (event.target.value == "homme" || event.target.value == "femme")
            this.isGenreSelected = false;
          if (event.target.value == "homosexuel" || event.target.value == "bisexuel" || event.target.value == "heterosexuel")
            this.isOrientationSelected = false;
        }
      },

      showModifBio(){
        this.isModifBio = true;
        this.tmpBio = this.bio;
      },

      resetBio(){
        this.isModifBio = false;
        this.bio = this.tmpBio;
      },

      modifBio(e){
        this.$http.post('/profil/edit/modif', {bio:this.bio});
        this.isModifBio = false;
      },

      modifLocalisation(){
        // --> message, si pas de champ.
        if (this.adress == '' || this.city == '' || this.code == '')
          return ;
        const url = 'http://www.mapquestapi.com/geocoding/v1/address?key=Mn6QAXVv8wYRojewUDGR7819P5YJEGAg&location='+this.adress+','+this.city+','+this.code;
        fetch(url, {method: 'POST',  headers: {'Content-Type': 'application/json'}})
          .then(res => res.json())
          .then(response => {
            const city = response.results[0].locations[0].adminArea5;
            const country = response.results[0].locations[0].adminArea1;
            const street = response.results[0].locations[0].street;
            const code = response.results[0].locations[0].postalCode;
            const lat = response.results[0].locations[0].latLng.lat;
            const lng = response.results[0].locations[0].latLng.lng;
            this.city = city
            this.country = country
            this.isModifLocalisation = false
            this.$http.post('/setgeoLoc', {latitude:lat, longitude:lng, city: this.$utils.formatCity(city), country: country, street: street,code: code})
          })
      },

      resizeTextarea(e){
        this.textAreaHeight = e.target.style.height;
      },

      deleteImg(e){
        if (e.target.name == this.profilPicName){
          this.$http.post('/profil/edit/deleteImg', {name:e.target.parentElement.children[0].name, isProfilPic:1});
        }else {
          this.$http.post('/profil/edit/deleteImg', {name:e.target.parentElement.children[0].name});
        }
        this.fetchedImg = this.fetchedImg.filter((value) => {
            return (value.name !== e.target.parentElement.children[0].name);
        })
      },

      previewImage(){
           const input = event.target;
           if (input.files && input.files[0]) {
               const reader = new FileReader();
               this.imageName = input.files[0].name;
               this.imgSize = Math.round(input.files[0].size / 1024);
               reader.onload = (e) => {
                   this.imageData = e.target.result;
                   this.isPreview = true;
               }
               reader.readAsDataURL(input.files[0]);
           }
      },

      checkPic(el){
        if (!el.checked && el.name === this.profilPicName){
          this.isProfilPicSelected = true;
          return (true);
        }
        return (false);
      },

      addImg(){
          this.isPreview = false;
          if (this.imgSize > 7000){
            return ;
          }
          this.$http.post('/profil/edit/addImg', {image:this.imageData, name:this.imageName}).then((response) => {
            if (response.data.path){
              this.fetchedImg.push({name:this.imageName, path:response.data.path});
            }
            if (response.data.error){
              this.error = response.data.error
            }else {
              this.error = ''
            }
          });
          this.imageData = '';
          this.$refs.fileInput.value = '';
      },

      openModal(){
        const elem = document.querySelector('.modal');
        this.instance = M.Modal.init(elem, {inDuration:300, outDuration:400});
        this.instance.open();
      },

      closeModal(){
        this.instance.close();
      },

      getMostUsedtags(){
        return new Promise((resolve, reject) => {
          this.$http.get('/tagList/get').then((response) => {
            resolve(response.data)
          });
        })
      }
    }
  }

</script>
