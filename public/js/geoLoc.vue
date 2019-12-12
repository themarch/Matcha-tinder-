<template>



</template>


<script>

export default{

  props:['type'],

  mounted(){
    if (this.type == 'getLoc'){
      this.findLoc()
    }
    if (this.type == 'refreshLoc'){
      this.refreshLoc()
    }
  },

  data(){
    return {
      isForceLoc:false
    }
  },

  // format ici + dans le filter + qd on change de loc.

  methods:{
    findLoc(){
      if (!navigator.geolocation){
        this.forceLoc()
      }else {
        navigator.geolocation.getCurrentPosition(this.translateCoordsToCity,
          error => {
          this.$http.post('/setgeoLoc', {
            latitude:geoplugin_latitude(),longitude:geoplugin_longitude(),
            city: this.$utils.formatCity(geoplugin_city()), country:geoplugin_countryName(), code:geoplugin_regionCode()
          })
        });
      }
    },

    forceLoc(){
      if (this.isForceLoc === true)
        return ;
      this.$http.post('/setgeoLoc', {
        latitude:geoplugin_latitude(),longitude:geoplugin_longitude(),
        city: this.$utils.formatCity(geoplugin_city()), country:geoplugin_countryName(), code:geoplugin_regionCode()
      })
      this.isForceLoc = true
    },

    refreshLoc(address, city, code){
      const url = 'http://www.mapquestapi.com/geocoding/v1/address?key=Mn6QAXVv8wYRojewUDGR7819P5YJEGAg&location='+adress+','+city+','+code;
      fetch(url, {method: 'POST',  headers: {'Content-Type': 'application/json'}})
        .then(res => res.json())
        .then(response => {
          const city = this.$utils.formatCity(response.results[0].locations[0].adminArea5);
          const country = response.results[0].locations[0].adminArea1;
          const street = response.results[0].locations[0].street;
          const code = response.results[0].locations[0].postalCode;
          const lat = response.results[0].locations[0].latLng.lat;
          const lng = response.results[0].locations[0].latLng.lng;
          this.$http.post('/setgeoLoc', {latitude:lat, longitude:lng, city: city, country: country, street: street,code: code})
        })
    },

    translateCoordsToCity(pos){
      const lat = pos.coords.latitude
      const lng = pos.coords.longitude
      console.log(pos)
      const url = 'https://www.mapquestapi.com/geocoding/v1/reverse?key=Mn6QAXVv8wYRojewUDGR7819P5YJEGAg&location='+lat+','+lng+'&outFormat=json&thumbMaps=false';
      fetch(url, {method: 'POST',  headers: {'Content-Type': 'application/json'}})
        .then(res => res.json())
        .then(response => {
          const city = this.$utils.formatCity(response.results[0].locations[0].adminArea5);
          const country = response.results[0].locations[0].adminArea1;
          const street = response.results[0].locations[0].street;
          const code = response.results[0].locations[0].postalCode;
          this.$http.post('/setgeoLoc', {latitude:pos.coords.latitude, longitude:pos.coords.longitude, city: city, country: country, street: street,code: code});
        })
    }
  }
}


</script>
