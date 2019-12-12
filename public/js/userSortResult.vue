<template>

  <div class="mr-t-6">
    <p class="black-text center-align mr-b-1"><strong>Trier</strong></p>
    <hr style="margin-bottom:5%!important;margin-top:1%!important">
    <div class="row">
      <div class="input-field col s12">
        <select class="icons" id="type" @change="getSortValue">
          <option value="begin" disabled selected>Trier par</option>
          <option value="user-Age">Age</option>
          <option value="score">Score</option>
          <option value="localisation">Localisation</option>
          <option value="tags">Tags</option>
        </select>
        <label class="mr-b-1">Séléctionner un type</label>
      </div>
    </div>
  </div>
</div>
</template>


<script>
  export default {

    props:['refresh'],

    mounted(){
      // this.initChips()
      const elems = document.getElementById('type')
      M.FormSelect.init(elems, {
        dropdownOptions: {
          inDuration: 350,
          outDuration: 350,
          coverTrigger: false,
          constrainWidth: true
        }
      });
    },

    watch:{
      refresh(value){
        if (value === true){
            //this.initChips()
            //this.tags = []
            //this.localisation = ''
            //this.selectedType = ''
            this.$emit('sendSortData', 'resetRefresh')
        }
      }
    },

    data(){
      return {
        tags:[],
        localisation:'',
        selectedType:'',
        chipsInstance:''
      }
    },

    methods:{
      getSortValue(e){
        if (e.target.options.selectedIndex > -1){
          this.selectedType = e.target.options[e.target.options.selectedIndex].value
          this.$emit('sendSortData', {type:'selectedSortType', value:this.selectedType, data:this});
        }
      },

      /*initChips(){
        var elems = document.getElementById('sort-chips');
        var vm = this
        var options = {
          placeholder: 'Entrer un tag',
          secondaryPlaceholder: '+Tag',
          autocompleteOptions: {
            data: {
              'Php': null,
              'Java': null,
              'Js': null,
              'Music': null,
              'Film': null,
              'Google': null,
              'Microsoft': null,
              'Ola': null
            },
            limit: Infinity
          },
          onChipAdd(e, data){ vm.chipAdded(e, data); },
          onChipDelete(e, data) { vm.chipDelete(e, data); }
        };
        M.Chips.init(elems, options)
        this.chipsInstance = M.Chips.getInstance(elems)
      },

      chipAdded(e, data){
        if (data === (null || undefined))
          return ;
        this.tags = [...this.tags, data.childNodes[0].textContent.toLowerCase()]
        this.$emit('sendSortData', {type:'tags', value:this.tags, data:this})
      },

      chipDelete(e, data){
        if (data === (null || undefined))
          return ;
        const index = this.tags.indexOf(data.childNodes[0].textContent)
        if (index !== -1)
          this.tags.splice(index, 1);
        this.$emit('sendSortData', {type:'tags', value:this.tags, data:this})
      },*/

      sendInput(value){
        this.$emit('sendSortData', {type:'localisation',
        value:value.charAt(0).toUpperCase() + value.slice(1).toLowerCase(), data:this})
      }
    }

  }
</script>
