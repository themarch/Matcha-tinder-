<?php

class SignalUserController extends Models
{
    // reporter = 1 fois -- admin qui decide de delete le compte ou pas |
    // block ou unblock le user => liste de user blockées (sur le profil... menu déroulant + nom / prénom / lien vers le profil pour debloquer).
    public function reportUser()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['profilId'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
              'profilId' => 'digit|max:11'
            ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            redirect('/');
        }
        $this->insert('Reported', ['user_id' => $_SESSION['user_id'], 'reported_user' => $data['profilId']]);
    }

    public function blockUser()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['profilId'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
              'profilId' => 'digit|max:11'
            ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            redirect('/');
        }
        $this->insert('Blocked', ['user_id' => $_SESSION['user_id'], 'blocked_user' => $data['profilId']]);
    }

    public function unblock()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['profilId'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
              'profilId' => 'digit|max:11'
            ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            redirect('/');
        }
        $this->delete('Blocked', ['user_id' => $_SESSION['user_id'], 'blocked_user' => $data['profilId']]);
    }

    public function isReported()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['profilId'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
              'profilId' => 'digit|max:11'
            ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            redirect('/');
        }
        $isReported = 0;
        $result = $this->fetch('Reported', ['user_id' => $_SESSION['user_id'], 'reported_user' => $data['profilId']]);
        if ($result) {
            $isReported = 1;
        }
        echo encodeToJs(['isReported' => $isReported]);
    }

    public function isBlocked()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['profilId'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
              'profilId' => 'digit|max:11'
            ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            redirect('/');
        }
        $isblocked = 0;
        $result = $this->fetch('Blocked', ['user_id' => $_SESSION['user_id'], 'blocked_user' => $data['profilId']]);
        if ($result) {
            $isblocked = 1;
        }
        echo encodeToJs(['isBlocked' => $isblocked]);
    }

    public function getBlockedUsers()
    {
        // nom + prénom | lien vers le profil | croix pour unblock.
        $result = $this->signal->fetchBlockedUser($_SESSION['user_id']);
        if ($result) {
            echo encodeToJs(['blockedUsers' => $result]);
        }
    }
}
