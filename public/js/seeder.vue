<template>

  <div class="row">
      <div class="card teal center-align">
        <span class="mr-t-1 white-text">
          Seed activated
        </span>
        <div class="card-content" style="width:100%">
          <span class="white-text">
          Options [nombre de profil = {{number}}, nationalité = {{nationalite}}]
          </span>
        </div>
      </div>
  </div>
</template>


<script>
  // http://extreme-ip-lookup.com/json/?callback=getIP => city + lat + long.
  /*
   ----- Distance a vole d'oiseau entre 2 points.
  http://www.geoplugin.net/javascript.gp = arrondisment pres.
  function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
  var R = 6371; // Radius of the earth in km
  var dLat = deg2rad(lat2-lat1);  // deg2rad below
  var dLon = deg2rad(lon2-lon1);
  var a =
  Math.sin(dLat/2) * Math.sin(dLat/2) +
  Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
  Math.sin(dLon/2) * Math.sin(dLon/2)
  ;
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  var d = R * c; // Distance in km
  return d;
}

function deg2rad(deg) {
return deg * (Math.PI/180)
}

var lat1 = 45.747761;
var long1 = 4.843295;

var lat2 = 45.753204;
var long2 = 4.912422;


console.log(getDistanceFromLatLonInKm(lat1, long1, lat2, long2));

  */
  import axios from 'axios'

  export default{
    props:['number', 'nationalite'],

    created(){
      this.generateRandomCityCoord()
      var vm = this
      axios.get('https://randomuser.me/api/?results='+this.number+'&?gender=female&?gender=male?nat='+this.nationalite)
      .then(function(response){
        vm.fillData(response.data.results)
      });
    },

    data(){
      return {
        userSettings:'',
        randLocation:'',
        lat:false,
        long:false,
        score:false,
        bio:'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Prioris generis est docilitas, memoria Atqui reperies, inquit, in hoc quidem pertinacem; Idem iste, inquam, de voluptate quid sentit? Si longus, levis.'
      }
    },

    methods:{
      randomLocation(){
        let location = {};
        let cityLocations = [];
        const city = ['Paris', 'Lyon', 'Toulouse', 'Marseille']
        const randomCity = city[Math.floor(Math.random()*city.length)]

        if (randomCity == 'Paris'){
          cityLocations = this.randLocation.Paris
          location = cityLocations[Math.floor(Math.random()*cityLocations.length)]
        }
        if (randomCity == 'Lyon'){
          cityLocations = this.randLocation.Lyon
          location = cityLocations[Math.floor(Math.random()*cityLocations.length)]
        }
        if (randomCity == 'Toulouse'){
          cityLocations = this.randLocation.Toulouse
          location = cityLocations[Math.floor(Math.random()*cityLocations.length)]
        }
        if (randomCity == 'Marseille'){
          cityLocations = this.randLocation.Marseille
          location = cityLocations[Math.floor(Math.random()*cityLocations.length)]
        }
        location.city = randomCity
        return (location)
      },

      randomTags(){
        let randomTag = []
        const tags = [
          'php', 'java',
          'music', 'video', 'film', 'sport', 'cuisine',
          'jeux-video', 'pc', 'gaming', 'cinema', 'fete', 'soirée'
        ];
        const randomTagsNumber = this.randomNumberRange(13, 1)

        for (let i = 0; i < randomTagsNumber; i++){
          const randTagVal = tags[Math.floor(Math.random()*tags.length)]
          if (!randomTag.includes(randTagVal))
            randomTag = [...randomTag, randTagVal]
        }
        return (randomTag)
      },

      randomScore(){
        return (this.randomNumberRange(100, 0))
      },

      randomAge(){
        return (this.randomNumberRange(90, 18))
      },

      randomNumberRange(max, min){
        return (Math.floor(Math.random() * (max - min + 1)) + min)
      },

      randomFloat(maxValue,minValue,precision){
        if(typeof(precision) == 'undefined'){
          precision = 2;
        }
        return Math.min(minValue + (Math.random() * (maxValue - minValue)),maxValue).toFixed(precision) * 1;
      },

      randomOrientation(){
        const orientation = ['homosexuel', 'bisexuel', 'heterosexuel'];
        return (orientation[Math.floor(Math.random()*orientation.length)]);
      },

      fillData(result){
        for (let i = 0; i < result.length; i++){
          result[i].score = this.randomScore()
          result[i].age = this.randomAge()
          result[i].tags = this.randomTags()
          result[i].location = this.randomLocation()
          result[i].picture.name = result[i].picture.large.split('/').splice(-2).join('-')
          result[i].orientation = this.randomOrientation()
        }
        // todo : modifier le genre et l'orientation.
        this.sendData(result)
        // ---- console.log(result)
      },

      sendData(result){
        axios.post('/seeder', result).then(function (response) {
        })
        .catch(function (error) {
          // --- console.log(error);
        });
      },

      generateRandomCityCoord(){
        this.randLocation = {
          Marseille: this.generateRandomPoints(43.297682, 5.405594, 3000, this.number / 2),
          Lyon: this.generateRandomPoints(45.754594, 4.833241, 3000, this.number / 2),
          Toulouse: this.generateRandomPoints(43.602121, 1.437812, 3000, this.number / 2),
          Paris: this.generateRandomPoints(48.856613, 2.352222, 3000, this.number / 2)
        }
      },

      generateRandomPoints(latitude, longitude, radius, count) {
        let points = [];
        for (var i = 0; i < count; i++) {
          points = [...points, this.generateRandomPoint(latitude, longitude, radius)]
        }
        return points;
      },

      generateRandomPoint(latitude, longitude, radius) {
        const x0 = longitude;
        const y0 = latitude;
        const rd = radius/111300;

        const u = Math.random();
        const v = Math.random();

        const w = rd * Math.sqrt(u);
        const t = 2 * Math.PI * v;
        const x = w * Math.cos(t);
        const y = w * Math.sin(t);

        const xp = x/Math.cos(y0);

        return {latitude: y + y0, longitude: xp + x0};
      }
    }
  }


</script>
