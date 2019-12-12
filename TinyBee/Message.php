<?php

/**
 * User information messages
 */
class Message
{
    public static $userMessages = [
        'password_link_info' => "Un lien de réinitialisation du mot de passe à été envoyé sur votre boîte mail.",
        'reset_link_info' =>  "Veuillez confirmer votre inscription en cliquant sur le lien envoyé dans votre boîte mail,<br />
        afin de pouvoir modifier votre mot de passe.",
        'confirm_login_info' => "Veuillez confirmer votre inscription en cliquant sur le lien envoyé dans votre boîte mail",
        'reset_login_info' => "Veuillez confirmer le changement du mot de passe en cliquant sur le lien envoyé dans votre boîte mail.",
        'username' => "Le nom d'utilisateur doit uniquement contenir des caractéres alphanumériques",
        'lastname' => "Le nom doit uniquement contenir des caractéres alphabétiques",
        'firstname' => "Le prénom doit uniquement contenir des caractéres alphabétiques",
        'email' => "Ce mail n'est pas valid.",
        'reset_email' => "Votre mail a été modifié.",
        'password' => "Le mot de passe doit contenir au moins 8 caractéres, une lettre en minuscule, une lettre en majuscule et un chiffre.",
        'bad_credential' => 'Mot de passe ou login incorrecte.',
        'image' => 'Ce format d\'image n est pas supporté.',
        'duplicate_img' => 'Le nom est déjá utilisé par une autre image.',
        'name' => 'Ce tag n\'est pas valid.',
        'age' => 'L\'age n\'est pas valid : il doit être compris entre 18 et 100 ans',
        'localisation' => 'Le format de la localisation est incorrect : [Alpha],[espace][Alpha]',
        'tag' => 'Le tag doit contenir uniquement contenir des caractéres alphabétiques',
        'search' => 'Aucun résultat'
    ];
}
