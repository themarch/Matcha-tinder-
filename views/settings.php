<?php require_once(dirname(__DIR__).'/views/header.php'); ?>


<section class="hero blue-grey lighten-5 is-fullheight-with-navbar" id="app">
  <nav-bar></nav-bar>
  <div class="hero-body" style="width:100%;height:100%">
    <user-settings :title="{firstname:'Nom', lastname:'Prenom',age:'Age',email:'Email', password:'Mot de passe'}"></user-settings>
  </div>
</section>

<?php include_once(dirname(__DIR__)."/views/footer.php"); ?>
