<?php

$route = new Route();

/**
 *   --------- Register forms ---------
 */
$route->add('/login', 'get', function () {
    view('user_register_forms.php', ['registerType' => 'login']);
})->addMiddleware(function () {
    if (isAuth()) {
        redirect('/');
    }
});

$route->add('/register', 'get', function () {
    view('user_register_forms.php', ['registerType' => 'register']);
})->addMiddleware(function () {
    if (isAuth()) {
        redirect('/');
    }
});

$route->add('/reset', 'get', function () {
    view('user_register_forms.php', ['registerType' => 'reset']);
});

$route->add('/chat', 'get', function () {
    view('chat.php');
});

/**
 *  --------- Authentification ---------
 */
$route->add('/doLogin', 'post', 'UserController@login');
$route->add('/create', 'post', 'UserController@create');
$route->add('/confirm/:alphanum', 'get', 'UserController@confirm');
$route->add('/reset/view/:alphanum', 'get', 'UserController@resetLink');
$route->add('/reset/doReset', 'post', 'UserController@confirmReset');
$route->add('/reset/sendResetLink', 'post', 'UserController@sendResetLink');
$route->add('/logout', 'get', 'UserController@logout');
$route->add('/isAuth', 'get', function () {
    echo json_encode(['user' => ['isAuth' => isAuth(), 'id' => $_SESSION['user_id'] ?? null]]);
});


/**
 *  --------- Public search routes ---------
 */
$route->add('/search', 'get', function () {
    view('search.php', ['type' => 'search']);
});
$route->add('/search/result', 'get', function () {
    view('search.php', ['type' => 'result']);
});

/**
 *  --------- Authenticated users routes ---------
 */

/*$route->add('/', 'get', function () {
    view('sugestions.php');
})->addMiddleware(function () {
    if (isAuth()) {
        setLastVisited();
    }
});*/

$route->add('/', 'get', 'SearchController@loadSugestions')->addMiddleware(function () {
    if (isAuth()) {
        setLastVisited();
    }
});

/*
   - MiddlewareStack pour les requêtes post en js et les requetes post des formulaires.
 */

$route->addMiddlewareStack(
    [
      '/like/setLike',
      '/like/getLikeByUser',
      '/like/setDisLike',
      '/settings/newFirstName',
      '/settings/newLastname',
      '/settings/newAge',
      '/settings/newEmail',
      '/settings/newPassword',
      '/profil/getProfilPicById',
      '/profil/edit/modif',
      '/profil/edit/addTag',
      '/profil/edit/deleteTag',
      '/profil/edit/addImg',
      '/profil/edit/deleteImg',
      '/profil/edit/addProfilImg',
      '/profil/visit/getConsultedProfilPic',
      '/report/add',
      '/block/add',
      '/report/isReported',
      '/block/isBlocked',
      '/block/unblock',
      '/settings/newFirstname',
      '/settings/newLastname',
      '/settings/newAge',
      '/settings/newEmail',
      '/settings/newPassword',
      '/doLogin',
      '/create',
      '/reset/doReset',
      '/chat/addMessage',
      '/chat/findUserRoom',
      '/getMoreSugestions',
      '/search/manageResult',
      '/search/searchResult',
      '/chat/updateNotification',
      '/setgeoLoc',
      'callback' => function () use ($csrf) {
          if ($csrf->check() === false) {
              redirect('/');
          }
      }
  ]
);

$route->addMiddlewareStack(
    [
      '/like/setLike',
      '/like/getLikeByUser',
      '/like/setDisLike',
      '/like/isLikedByUser',
      '/settings',
      '/settings/newFirstName',
      '/settings/newLastname',
      '/settings/newAge',
      '/settings/newEmail',
      '/settings/newPassword',
      '/profil',
      '/profil/:digits',
      '/profil/isOnline',
      '/profil/getProfilViews',
      '/profil/getProfilPicById',
      '/profil/getProfilLikes',
      '/profil/edit',
      '/profil/edit/modif',
      '/profil/edit/getProfilData',
      '/profil/edit/addTag',
      '/profil/edit/getImg',
      '/profil/edit/deleteTag',
      '/profil/edit/getTag',
      '/profil/edit/addImg',
      '/profil/edit/deleteImg',
      '/profil/edit/addProfilImg',
      '/profil/edit/getProfilPic',
      '/profil/visit/getConsultedProfilPic',
      '/report/add',
      '/block/add',
      '/block/getBlockedUsers',
      '/report/isReported',
      '/block/isBlocked',
      '/block/unblock',
      '/settings/newFirstname',
      '/settings/newLastname',
      '/settings/newAge',
      '/settings/newEmail',
      '/settings/newPassword',
      '/chat/fetchMatchedUser',
      '/chat/addMessage',
      '/chat/searchMatchedUser',
      '/chat/findUserRoom',
      '/settings/getUserInfo',
      '/searchSugestions',
      '/getMoreSugestions',
      '/search/manageResult',
      '/search/searchResult',
      '/tagList/get',
      '/chat/updateNotification',
      '/notification/get',
      '/notification/set',
      '/setgeoLoc',
      '/search',
      '/search/result',
      'callback' => function () {
          if (!isAuth()) {
              redirect('/');
          }
          computeScore();
          setLastVisited();
      }]
);

$route->add('/settings', 'get', function () {
    view('settings.php');
});

$route->add('/profil/edit', 'get', function () {
    view('editProfil.php');
});

$route->add('/setgeoLoc', 'post', 'ProfilController@setGeoLoc');

$route->add('/searchSugestions', 'get', 'SearchController@searchSugestions');
$route->add('/getMoreSugestions', 'post', 'SearchController@paginateSugestion');
$route->add('/search/manageResult', 'post', 'SearchController@manageResult');
$route->add('/search/searchResult', 'post', 'SearchController@searchResult');
$route->add('/search/getResults', 'get', 'SearchController@getResults');

$route->add('/profil/edit/modif', 'post', 'ProfilController@editProfil');
$route->add('/profil/edit/getProfilData', 'get', 'ProfilController@getData');
$route->add('/profil/edit/addTag', 'post', 'TagController@addTag');
$route->add('/profil/edit/getImg', 'get', 'ImageController@getImg');
$route->add('/profil/edit/deleteTag', 'post', 'TagController@deleteTag');
$route->add('/profil/edit/getTag', 'get', 'TagController@getTag');
$route->add('/profil/edit/addImg', 'post', 'ImageController@addImg');
$route->add('/profil/edit/deleteImg', 'post', 'ImageController@deleteImg');
$route->add('/profil/edit/addProfilImg', 'post', 'ImageController@addProfilImg');
$route->add('/profil/edit/getProfilPic', 'get', 'ImageController@getProfilPic');

$route->add('/profil', 'get', 'ProfilController@getUserProfil');
$route->add('/profil/:digits', 'get', 'ProfilController@getVisitedProfil');
$route->add('/profil/isOnline', 'post', 'ProfilController@isOnline');

$route->add('/profil/getProfilViews', 'get', 'ProfilController@getProfilViews');
$route->add('/profil/getProfilLikes', 'get', 'ProfilController@getProfilLikes');

$route->add('/profil/getProfilPicById', 'post', 'ProfilController@getProfilPicById');
$route->add('/profil/visit/getConsultedProfilPic', 'post', 'ImageController@getConsultedProfilPic');

$route->add('/like/isLikedByUser', 'post', 'LikeController@isLikedByUser');
$route->add('/like/setLike', 'post', 'LikeController@setLike');
$route->add('/like/setDisLike', 'post', 'LikeController@setDisLike');
$route->add('/like/getLikeByUser', 'post', 'LikeController@getLikeByUser');

$route->add('/report/add', 'post', 'SignalUserController@reportUser');
$route->add('/block/add', 'post', 'SignalUserController@blockUser');
$route->add('/report/isReported', 'post', 'SignalUserController@isReported');
$route->add('/block/isBlocked', 'post', 'SignalUserController@isBlocked');
$route->add('/block/unblock', 'post', 'SignalUserController@unblock');
$route->add('/block/getBlockedUsers', 'get', 'SignalUserController@getBlockedUsers');

$route->add('/settings/getUserInfo', 'get', 'SettingsController@getUserInfo');

$route->add('/settings/newFirstname', 'post', 'SettingsController@newFirstname');
$route->add('/settings/newLastname', 'post', 'SettingsController@newLastname');
$route->add('/settings/newAge', 'post', 'SettingsController@newAge');
$route->add('/settings/newEmail', 'post', 'SettingsController@newEmail');
$route->add('/settings/newPassword', 'post', 'SettingsController@newPassword');

$route->add('/chat/addMessage', 'post', 'ChatController@addMessage');
$route->add('/chat/fetchMatchedUser', 'get', 'ChatController@fetchMatchedUser');
$route->add('/chat/searchMatchedUser', 'post', 'ChatController@searchMatchedUser');
$route->add('/chat/findUserRoom', 'post', 'ChatController@findUserRoom');
$route->add('/chat/updateNotification', 'post', 'ChatController@updateNotification');


$route->add('/tagList/get', 'get', 'TagController@getTagList');
$route->add('/notification/get', 'get', 'NotificationController@getAll');
$route->add('/notification/set', 'post', 'NotificationController@setSeenNotification');

/**
 * Seeder Route --- DEV ONLY.
 */
$route->add('/seeder', 'post', 'SeederController@storeSeed')->addMiddleware(function () {
    if (SEEDER === null) {
        redirect('/');
    }
});

 $route->add('/seeder/create', 'get', function () {
     view('seeder.php');
 })->addMiddleware(function () {
     if (SEEDER === null) {
         redirect('/');
     }
 });

$route->loadRoutes();
