<template>

  <div class="container" style="width:100%!important;height:100%!important;max-width:100%!important">
    <div v-if="type === 'result' || isSearch === true" class="row" style="width:100%!important">
      <div class="col push-s1 s10 m4 l3">
        <div class="card teal lighten-5 s-responsive-row" style="width:100%!important">
          <div class="card-content" style="padding:1.5em!important">
            <user-filter
            :custom-style="{margin:0,padding:0}"
            :title="{name:'Filtrer'}"
            :range-filter="{age:'Age', popularite:'Popularite'}"
            :sort-filter="{name:'Age', localisatio:'Localisation', popularite:'Popularite', tags:'Tags'}"
            :sort-filter-name="{name:'Trier les résultats'}"
            :action-btn="{name:'Confirmer'}"
            :filter-id="{id:'1'}"
            v-on:sendFilterData="handleData($event)" :refresh="isFilterRefreshed" :key="isFilterRefreshed"></user-filter>
            <user-sort-result v-on:sendSortData="handleSortData($event)" :refresh="isSortRefreshed"></user-sort-result>
            <div class="row" style="margin:0!important;padding:0!important">
              <button @click="sendResultOptions()"
                class="btn-small green waves-effect waves-light basic-txt mr-t-3 right">Confirmer
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="col s12 m8 l8">
      <ul class="collection">
        <li class="row ml-v-align collection-item avatar" v-for="(value, name, index) in matchedResult">
          <div class="col s6 m6 l6" style="text-align:center">
            <load-async-image :user-id="value.id"
            :profil-id="value.id" img-style="center-img responsive-img rounded-img"
            :need-watch="true" @imageLoaded="countLoadedImg" :key="value.id"></load-async-image>
          </div>
          <div class="col s6 m6 l4">
            <div class="row black-text">
              {{value.firstname}}
              <span class="black-text">
                {{value.lastname}}
              </span>
              <span class="black-text">, {{value.age}} ans
              </span> <br>
              <span class="black-text"> {{value.localisation}} </span> <br> <br>
              <span class="black-text" v-if="value.hasOwnProperty('km')">
                <strong>Distance :</strong> {{value.km}}.{{value.meters}} km
              </span> <br>
              <span class="black-text">
                <strong>Score :</strong> {{value.score}}
              </span>
            </div>
            <online-user-info :user-id="value.id"></online-user-info>
          </div>
          <div class="col s12 m7 l7" v-if="value.hasOwnProperty('commonTags') && isSearch === false">
            <div class="chip blue white-text center-align" v-for="(value, name, index) in value.commonTags" v-if="isCommonTags(value)">
              #{{value}}
            </div>
          </div>
          <div class="col s12 m7 l7" v-if="value.hasOwnProperty('commonTags') && isSearch === true">
            <div class="chip blue white-text center-align" v-for="(value, name, index) in value.commonTags">
              #{{value}}
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
  <div v-if="type === 'search' && isSearch === false" class="row valign-wrapper'" style="width:100%!important">
    <div class="col s12 push-m1 m10 l8 push-l1 offset-l1">
      <div class="card teal lighten-5 s-responsive-row" style="width:100%!important">
        <div class="card-content" style="padding:1.5em!important">
          <user-filter
          :custom-style="{margin:0,padding:0}"
          :title="{name:'Rechercher'}"
          :range-filter="{age:'Age', popularite:'Popularite'}"
          :sort-filter="{name:'Age', localisatio:'Localisation', popularite:'Popularite', tags:'Tags'}"
          :sort-filter-name="{name:'Trier les résultats'}"
          :action-btn="{name:'Confirmer'}"
          :filter-id="{id:'1'}"
          v-on:sendFilterData="handleData($event)" :refresh="isFilterRefreshed" :key="isFilterRefreshed"></user-filter>
          <div class="row" style="margin:0!important;padding:0!important">
            <button @click="sendSearch()"
            class="btn-small green waves-effect waves-light basic-txt mr-t-3 right">Confirmer
          </button>
          <div v-if="isResultError">
            <error message="Aucun résultat"></error>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div v-if="windowScroll !== 0" class="fixed-action-btn" style="right:23px;bottom:23px">
  <a class="btn-floating btn-medium red" @click="resetScroll">
    <i class="large material-icons">arrow_upward</i>
  </a>
</div>
</div>
</template>

<script>

import userFilter from './userFilter.vue'
import userSortResult from './userSortResult.vue'

export default {

  props:['type', 'isSearch'],

  components:{
    'user-filter':userFilter,
    'user-sort-result':userSortResult
  },

  mounted(){
    window.addEventListener("scroll", this.onScroll)
  },

  beforeDestroy() {
    window.removeEventListener("scroll", this.onScroll)
  },

  created(){
    !this.isSearch ? this.fetchSugestions() : this.fetchSearchResults();
  },

  data() {
    return {
      windowScroll:0,
      isFilter:'',
      imageLoaded:0,
      pageCounter:1,
      activateTag:false,
      sortResult:{
        tags:[],
        localisation:'',
        type:'',
        name:'sort'
      },
      filterResult:{
        tags:[],
        localisation:'',
        slider:{
          age:'',
          popularite:''
        },
        name:'filter'
      },
      isResultError:false,
      isFilterRefreshed:false,
      isSortRefreshed:false,
      matchedResult:[],
      userTags:[]
    }
  },

  methods: {
    handleData(filterData){
      if (filterData.type == 'tags')
        this.filterResult.tags = filterData.value
      if (filterData.type == 'localisation')
        this.filterResult.localisation = filterData.value
      if (filterData.type == 'slider'){
        this.filterResult.slider[filterData.name] = {minRange:filterData.range[0], maxRange:filterData.range[1]};
      }
      if (filterData == 'resetRefresh'){
        this.isFilterRefreshed = false
      }
    },

    handleSortData(sortData){
      if (sortData.type == 'tags')
        this.sortResult.tags = sortData.value;
      if (sortData.type == 'localisation')
        this.sortResult.localisation = sortData.value;
      if (sortData.type == 'selectedSortType')
        this.sortResult.type = sortData.value
      if (sortData == 'resetRefresh'){
        this.isSortRefreshed = false
      }
    },

    sendResultOptions(){
      let data = {
        filterResult:{},
        sortResult:{}
      };
      if (this.filterResult) {
        this.setData(this.filterResult, data.filterResult)
      }
      if (this.sortResult){
        this.setData(this.sortResult, data.sortResult)
      }
      this.sendData(data)
      this.isSortRefreshed = true
      this.isFilterRefreshed = true
      this.resetData()
    },

    sendSearch(){
      this.$http.post('/search/searchResult', {filterResult:this.filterResult}).then((response) => {
        if (response.data == 'result error'){
          // --> no result founded.
          this.isResultError = true
        }else {
          window.location.href = '/search/result'
        }
      })
    },

    setData(target, data){
      for (let [key, value] of new Map(Object.entries(target))) {
        if(target.hasOwnProperty(key)) {
          if (key == 'slider'){
            const age = value.age
            const popularite = value.popularite
            if (age !== '' && 0 !== (parseInt(age.minRange) + parseInt(age.maxRange))){
              data.age = age
            }
            if (popularite !== '' && 0 !== (parseInt(popularite.minRange) + parseInt(popularite.maxRange))){
              data.popularite = popularite
            }
          }else{
            if (key == 'tags' && value.length > 0){
              data.tags = value
            }
            if (key == 'localisation' && value !== ''){
              data.localisation = value
            }
            if (key == 'type' && value !== ''){
              data.type = value
            }
          }
        }
      }
    },

    sendData(content){
      this.$http.post('/search/manageResult', {content:content, isSearch:this.isSearch}).then((response) => {
        if (response.data && Array.isArray(response.data.sugestions)){
          let countReloadedImg = 0;
          this.pageCounter = 1
          this.matchedResult = response.data.sugestions;
          this.matchedResult['type'] = this.isSearch === true ? 'searchFilter' : 'filter'
        }
      })
    },

    resetData(){
      this.sortResult.tags = []
      this.sortResult.localisation = ''
    },

    fetchSugestions(){
      this.$http.get('/searchSugestions').then((response) => {
        if (response.data !== null && Array.isArray(response.data.sugestions)){
          window.scrollTo(0,0)
          this.matchedResult = response.data.sugestions
          this.matchedResult['type'] = 'sugestion'
          this.userTags = response.data.userTags
        }
      });
    },

    fetchSearchResults(){
      this.$http.get('/search/getResults').then((response) => {
        if (response.data && Array.isArray(response.data.search)){
          this.matchedResult = response.data.search
          this.matchedResult['type'] = 'search'
        }
      });
    },

    isCommonTags(name){
      return ((this.userTags.filter(element => element == name).length) > 0);
    },

    onScroll(e){
      if (window.scrollY > window.innerHeight)
        this.windowScroll = window.scrollY
      if (window.scrollY < window.innerHeight)
        this.windowScroll = 0
      if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight)
      {
        this.pageCounter++;
        this.addResults();
      }
    },

    resetScroll(){
      this.windowScroll = 0
      window.scrollTo(0,0)
    },

    addResults(){
      this.$http.post('/getMoreSugestions', {pageNumber:this.pageCounter, type:this.matchedResult['type']}).then((response) => {
        if (response.data && Array.isArray(response.data.result)){
          this.matchedResult.push(...response.data.result)
        }
      });
    },

    countLoadedImg(){
      this.imageLoaded++;
    }
  }
}

</script>
