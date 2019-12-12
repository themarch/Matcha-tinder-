<?php require_once(dirname(__DIR__).'/views/header.php'); ?>

<section class="hero blue-grey lighten-5 is-fullheight-with-navbar" id="app">
  <nav-bar></nav-bar>
  <div class="hero-body">
    <?php if (isset($userProfilData, $profilType) && $profilType == 'userProfilOwner'): ?>
      <user-profil :profil-data='<?= $userProfilData; ?>' type="<?= $profilType; ?>"></user-profil>
    <?php endif; ?>
    <?php if (isset($userProfilData, $profilType) && $profilType == 'consultUserProfil'): ?>
      <user-profil :profil-data='<?= $userProfilData; ?>' type="<?= $profilType; ?>"></user-profil>
    <?php endif; ?>
    <?php if (isset($warning)): ?>
      <div class="container" style="max-width:100%;height:100%">
        <div class="row" style="width:100%;height:100%">
          <div class="col l8 offset-l4 m8 offset-m4 s12 offset-s4 card teal center-align" style="width:40%;height:100%">
            <div class="card-content" style="width:100%!important;padding:3%!important">
              <span class="white-text" style="width:100%!important">
                <?= $warning; ?>
              </span>
              <div class="row mr-t-4">
                <a class="mr-t-4 blue waves-effect waves-light btn" href="/profil/edit">Editer</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>


<?php include_once(dirname(__DIR__)."/views/footer.php"); ?>
