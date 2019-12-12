<?php


class SettingsController extends Models
{
    public function getUserInfo()
    {
        $result = $this->fetch('User', ['id' => $_SESSION['user_id']], PDO::FETCH_ASSOC);
        if (!$result) {
            redirect('/');
        }
        echo encodeToJs(
            [
            'firstname' => $result['firstname'],
            'lastname' => $result['lastname'],
            'age' => $result['age'],
            'email' => $result['email']
          ]
        );
    }

    public function newEmail()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['email'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
            'email' => 'email|max:50|min:3'
          ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            echo encodeToJs(['Message' => $validate->loadedMessage]);
        } else {
            $this->update('User', ['email' => $data['email']], ['id' => $_SESSION['user_id']]);
        }
    }

    public function newAge()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['age'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
            'age' => 'digit|min:2|max:2'
          ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            echo encodeToJs(['Message' => $validate->loadedMessage]);
        } else {
            $this->update('User', ['age' => $data['age']], ['id' => $_SESSION['user_id']]);
        }
    }

    public function newLastname()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['lastname'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
            'lastname' => 'name|min:3|max:30'
          ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            echo encodeToJs(['Message' => $validate->loadedMessage]);
        } else {
            $this->update('User', ['lastname' => $data['lastname']], ['id' => $_SESSION['user_id']]);
        }
    }

    public function newFirstname()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['firstname'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
            'firstname' => 'name|min:3|max:30'
          ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            echo encodeToJs(['Message' => $validate->loadedMessage]);
        } else {
            $this->update('User', ['firstname' => $data['firstname']], ['id' => $_SESSION['user_id']]);
        }
    }

    public function newPassword()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['password'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
            'password' => 'password|max:256|min:8'
          ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            echo encodeToJs(['Message' => $validate->loadedMessage]);
        } else {
            $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            $this->update('User', ['password' => $hash], ['id' => $_SESSION['user_id']]);
            $_SESSION['token'] = $hash;
        }
    }
}
