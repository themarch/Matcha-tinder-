<?php require_once(dirname(__DIR__).'/views/header.php'); ?>


<section class="hero blue-grey lighten-5 is-fullheight-with-navbar" id="app">
  <nav-bar></nav-bar>
  <?php if (isAuth() && isset($_SESSION['needGeoLoc']) && $_SESSION['needGeoLoc'] === true): ?>
    <geo-loc type="getLoc"></geo-loc>
    <?php unset($_SESSION['needGeoLoc']);?>
  <?php endif; ?>
  <?php if (isAuth() && !isset($info)): ?>
    <div class="hero-body" style="display:block!important;padding:3%!important">
      <search-match type="result" :is-search="false"></search-match>
    </div>
  <?php elseif (isset($info) && $info == 'profilInfo'): ?>
    <div class="hero-body" id="body">
      <div class="container">
        <div class="row">
          <div class="col">
            <article class="message is-success mr-b-4">
              <div class="message-body">
                <p class="flow-text">
                  Matcha est un site de rencontre destiné à trouver la personne qui correspond à vos besoins, vous n'arrivez pas à trouver une relation durable ou
                  vous chercher une aventure ?
                  <br>
                  <a href="/profil/edit"> Éditer votre profil </a> dès maintenant et trouver la relation qui vous correspond.
                </p>
              </div>
            </article>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="hero-body" id="body">
      <div class="container">
        <div class="row">
          <div class="col">
            <article class="message is-success mr-b-4">
              <div class="message-body">
                <p class="flow-text">
                  Matcha est un site de rencontre destiné à trouver la personne qui correspond à vos besoins, vous n'arrivez pas à trouver une relation durable ou
                  vous chercher une aventure ?
                  <br>
                  <a href="/register"> Creér un compte </a> dès maintenant et trouver la relation qui vous correspond.
                </p>
              </div>
            </article>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</section>

<?php if (isset($info) && ($info == 'profilInfo' || $info == 'loadPic')): ?>
<script>
window.onload = function (){
    var elem = document.getElementById('body');
    if (window.innerWidth > 1900){
      elem.style.background = 'url(heart.jpg)';
      elem.style.backgroundSize = '100% auto';
      elem.style.backgroundRepeat = 'no-repeat';
      elem.style.backgroundPosition = 'center';
    }
}
</script>
<?php endif; ?>

<?php include_once(dirname(__DIR__)."/views/footer.php"); ?>
