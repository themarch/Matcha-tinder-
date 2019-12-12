<?php require_once(dirname(__DIR__).'/views/header.php'); ?>

<section class="hero blue-grey lighten-5 is-fullheight-with-navbar" id="app">
  <nav-bar></nav-bar>
  <div class="hero-body" style="width:100%;height:100%">
    <!--
    - genre = checkbox
    - orientation sexuelle = options select
    - bio = edit texarea
    - liste interets (tags) = edit tags input
    - Image max 5 = file input
    -->
    <user-profil-edit></user-profil-edit>
  </div>
</section>


<?php include_once(dirname(__DIR__)."/views/footer.php"); ?>
