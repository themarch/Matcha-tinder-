<?php

require_once('UserGeoLocTrait.php');

class UserController extends Models
{
    //use UserGeoLocTrait;

    public function create()
    {
        $request = new Request();
        $data = $request->get();
        if (!keysExist(['username', 'password', 'email', 'lastname', 'firstname', 'age'], $data)) {
            view("user_register_forms.php", array("warning" => "Champs invalides.", "registerType" => "register"));
        }
        if ($this->fetch('User', ['username' => $data['username']], PDO::FETCH_ASSOC)) {
            view("user_register_forms.php", array("warning" => "Ce nom d'utilisateur existe déjà.", "registerType" => "register"));
        }

        if ($this->fetch('User', ['email' => $data['email']], PDO::FETCH_ASSOC)) {
            view("user_register_forms.php", array("warning" => "Cette email existe déjà.", "registerType" => "register"));
        }

        $validate = new Validate(
            $data,
            [
              'username' => 'alphanum|min:3|max:30',
              'password' => 'password|max:256|min:8',
              'email' => 'email|max:50|min:3',
              'lastname' => 'name|min:3|max:30',
              'firstname' => 'name|min:3|max:30',
              'age' => 'digit|min:2|max:3'
            ],
            'user_register_forms.php',
            Message::$userMessages,
            ['registerType' => 'register']
        );

        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $_SESSION['token'] = $hash;
        $_SESSION['needGeoLoc'] = true;
        $link = randomPassword();
        $this->insert(
            'User',
            [
              'email'=> $data['email'],
              'password' => $hash,
              'username' => $data['username'],
              'lastname' => $data['lastname'],
              'firstname' => $data['firstname'],
              'age' => $data['age'],
              'confirmation_link' => $link
            ]
        );
        $_SESSION['user_id'] = $this->fetch('User', ['username' => $data['username']], PDO::FETCH_OBJ)->id;
        sendHtmlMail(
            $data['email'],
            $data['firstname'],
            "<a href=http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].'/confirm/'.$link."> Confirmer votre compte</a>",
            "Mail de confirmation"
        );
        view("user_register_forms.php", array("registerType" => "login"));
    }

    public function confirm($userLink)
    {
        $query = $this->fetch('User', ['id' => $_SESSION['user_id'], 'confirmation_link' => $userLink], PDO::FETCH_OBJ);
        if (!$query) {
            redirect('/');
        }
        if (hash_equals($query->password, $_SESSION['token']) && $query->confirmation_link == $userLink) {
            $_SESSION['needGeoLoc'] = true;
            $profil = $this->fetch('Profil', ['user_id' => $_SESSION['user_id']]);
            if (!isset($profil['user_id'])) {
                $this->insert('Profil', ['user_id' => $_SESSION['user_id'], 'orientation' => 'bisexuel']);
            }
            $this->update('User', ['is_confirmed' => 1, 'confirmation_link' => ''], ['id' => $_SESSION['user_id']]);
        }
        redirect('/');
    }

    public function login()
    {
        $request = new Request();
        $data = $request->get();
        if (!keysExist(['username', 'password'], $data) || isAuth()) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
              'username' => 'alphanum|min:3|max:30',
              'password' => 'password|max:256|min:8'
            ],
            'user_register_forms.php',
            Message::$userMessages,
            ['registerType' => 'login']
        );
        $query = $this->fetch('User', ['username' => $data['username']], PDO::FETCH_OBJ);
        if (!$query) {
            view('user_register_forms.php', ['registerType' => 'login']);
        }
        if (!$query->is_confirmed) {
            view(
                'user_register_forms.php',
                array('warning' =>
            Message::$userMessages['confirm_login_info'],
            'registerType' => 'login')
          );
        }
        if (password_verify($data['password'], $query->password)) {
            $_SESSION['token'] = $query->password;
            $_SESSION['user_id'] = $query->id;
            $_SESSION['needGeoLoc'] = true;
            redirect('/');
        } else {
            view(
                'user_register_forms.php',
                array('warning' =>
            Message::$userMessages['bad_credential'],
            'registerType' => 'login')
          );
        }
        redirect('/');
    }

    public function logout()
    {
        if (isset($_SESSION) && keysExist(['user_id', 'token'], $_SESSION) && isAuth()) {
            unset($_SESSION['user_id']);
            unset($_SESSION['token']);
            unset($_SESSION['needGeoLoc']);
        }
        redirect('/');
    }

    public function sendResetLink()
    {
        $request = new Request();
        $data = $request->get();

        if (!keysExist(['email'], $data)) {
            redirect('/');
        }

        $validate = new Validate(
            $data,
            [
            'email' => 'email|max:50|min:3'
          ],
            'user_register_forms.php',
            Message::$userMessages,
            ['registerType' => 'reset']
      );
        $query = $this->fetch('User', ['email' => $data['email']], PDO::FETCH_OBJ);
        if (!$query) {
            redirect('/');
        }
        if (!$query->is_confirmed) {
            view(
                'user_register_forms.php',
                array('warning' =>
            Message::$userMessages['reset_link_info'],
            'registerType' => 'reset')
          );
        }
        $link = randomPassword();
        $this->update('User', ['reset_link' => $link], ['email' => $data['email']]);
        $_SESSION['user_id'] = $query->id;
        sendHtmlMail(
            $data['email'],
            $query->firstname,
            "<a href=http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].'/reset/view/'.$link.">
          Confirmer la réinitialisation du mot de passe </a>",
            "Réinitialisation du mot de passe"
        );
        view(
            'user_register_forms.php',
            array('warning' =>
        Message::$userMessages['password_link_info'],
        'registerType' => 'reset')
      );
    }

    public function resetLink($link)
    {
        if (!keysExist(['user_id'], $_SESSION)) {
            redirect('/');
        }
        $validate = new Validate(
            ['link' => $link],
            [
              'link' => 'alphanum|min:32'
            ],
            'user_register_forms.php',
            Message::$userMessages,
            ['registerType' => 'login']
        );
        $query = $this->fetch('User', ['id' => $_SESSION['user_id']], PDO::FETCH_OBJ);
        if (!$query) {
            redirect('/');
        }
        if ($query->reset_link == $link) {
            $this->update('User', ['is_reset' => 1], ['id' => $_SESSION['user_id']]);
            view(
                'user_register_forms.php',
                array('registerType' => 'resetLink')
          );
        } else {
            redirect('/');
        }
    }

    public function confirmReset()
    {
        $request = new Request();
        $data = $request->get();

        if (!keysExist(['password'], $data) || !isset($_SESSION['user_id']) || isAuth()) {
            redirect('/');
        }

        $validate = new Validate(
            $data,
            [
            'password' => 'password|max:256|min:8'
          ],
            'user_register_forms.php',
            Message::$userMessages,
            ['registerType' => 'resetLink']
        );
        $query = $this->fetch('User', ['id' => $_SESSION['user_id']], PDO::FETCH_OBJ);
        if (!$query || !$query->is_reset) {
            redirect('/');
        }
        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $this->update('User', ['is_reset' => 0, 'reset_link' => '', 'password' => $hash], ['id' => $_SESSION['user_id']]);
        $_SESSION['token'] = $hash;
        redirect('/');
    }
}
