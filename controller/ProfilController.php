<?php

require_once("ProfilTrait.php");

class ProfilController extends Models
{
    use ProfilTrait;

    public function getUserProfil()
    {
        if (isAuth()) {
            if (!$this->fetch('Profil', ['user_id' => $_SESSION['user_id']])) {
                view('profil.php', ['warning' => 'Veuillez complété votre profil']);
            }
            $query = encodeToJs($this->profil->fetchUserProfilData($_SESSION['user_id']));
            view('profil.php', ['userProfilData' => $query, 'profilType' => 'userProfilOwner']);
        } else {
            redirect('/');
        }
    }

    public function editProfil()
    {
        $request = new Request();
        $isNewProfil = false;
        $data = $request->toJson();
        $type = $this->getEditType($data);
        if ($this->fetch('Profil', ['user_id' => $_SESSION['user_id']])) {
            $isNewProfil = true;
        }
        if (empty($type)) {
            redirect('/');
        }
        if (!$isNewProfil) {
            $this->insert(
                'Profil',
                ['user_id' => $_SESSION['user_id']],
                [$type => ($type == 'localisation') ? $data['city'].', '.$data['country'] : $data[$type]]
            );
        } else {
            $this->update(
                'Profil',
                [$type => ($type == 'localisation') ? $data['city'].', '.$data['country'] : $data[$type]],
                ['user_id' => $_SESSION['user_id']]
            );
        }
    }

    public function getData()
    {
        $result = $this->fetch('Profil', ['user_id' => $_SESSION['user_id']], PDO::FETCH_ASSOC);
        echo encodeToJs($result);
    }

    public function getVisitedProfil($userId)
    {
        if (isset($userId)) {
            $result = $this->profil->fetchUserProfilData($userId);
            if (!$result || !isset($result['user_id'])) {
                // si il a pas complete le profil => afficher juste lastname + firstname + photo par defaut.
                view('page_404.php');
            } else {
                if ($result['user_id'] !== $_SESSION['user_id']) {
                    if (!$this->fetch('Visite', ['user_id' => $userId, 'visiter_id' => $_SESSION['user_id']])) {
                        $this->insert('Visite', ['user_id' => $userId, 'visiter_id' => $_SESSION['user_id']]);
                        $this->insert('Notification', ['user_id' => $userId, 'visiter_id' => $_SESSION['user_id'], 'name' => 'visiter']);
                    }
                    $result['visitedUserId'] = $userId;
                    view('profil.php', ['userProfilData' => encodeToJs($result), 'profilType' => 'consultUserProfil']);
                } else {
                    $this->getUserProfil();
                }
            }
        }
    }

    public function isOnline()
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
        $result = $this->user->getOnlineUser($data['profilId']);
        if (!$result) {
            redirect('/');
        }
        $minuteDiff = round(abs(strtotime($result['last_visited']) - strtotime(date("Y-m-d H:i:s"))) / 60, 2);
        if (isset($result['last_visited'])) {
            echo encodeToJs(['last_visited' => $result['last_visited'], 'minDiff' => $minuteDiff]);
        }
    }

    public function getProfilViews()
    {
        $result = $this->profil->fetchProfilViews($_SESSION['user_id']);
        $result['visiterTags'] = group_by('visiter_id', $result['visiterTags']);
        if ($result) {
            echo encodeToJs(['visiterViews' => $result]);
        }
    }

    public function getProfilLikes()
    {
        $result = $this->profil->fetchProfilLikes($_SESSION['user_id']);
        $result['likesTags'] = group_by('liked_by', $result['likesTags']);
        if ($result) {
            echo encodeToJs(['visiterLikes' => $result]);
        }
    }

    public function getProfilPicById()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['user_id'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
            'user_id' => 'digit|max:11'
          ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            redirect('/');
        }
        $result = $this->fetch('Profil', ['user_id' => $data['user_id']], PDO::FETCH_ASSOC);
        $info = $this->fetch('User', ['id' => $data['user_id']], PDO::FETCH_ASSOC);
        if (!$result || empty($result['profile_pic_path'])) {
            $defaultImg = dirname(__DIR__).'/ressources/images/default-profile.png';
            $path = base64_encode(file_get_contents($defaultImg));
            echo encodeToJs(['path' => $path, 'name' => 'defaultProfil', 'firstname' => $info['firstname'], 'lastname' => $info['lastname']]);
        } else {
            $path = base64_encode(file_get_contents($result['profile_pic_path']));
            $name = $result['profile_pic_name'];
            echo encodeToJs(['path' => $path ?? null, 'name' => $name ?? null, 'firstname' => $info['firstname'], 'lastname' => $info['lastname']]);
        }
    }

    public function setGeoLoc()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['latitude', 'longitude', 'city', 'country', 'code'], $data)) {
            redirect('/');
        }
        if (strlen($data['city']) > 70 || strlen($data['country']) > 50 || (isset($data['street']) && strlen($data['street']) > 255)) {
            redirect('/');
        }
        $city = filter_var($data['city'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $country = filter_var($data['country'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (isset($data['street'])) {
            $street = filter_var($data['street'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        if (!is_float($data['latitude']) || !is_float($data['longitude'])) {
            $convLatitude = (float)($data['latitude']);
            $convLongitude = (float)($data['longitude']);
            if (!is_float($convLatitude) || !is_float($convLongitude)) {
                redirect('/');
            }
        }
        $validate = new Validate(
            $data,
            [
            'code' => 'digit|min:2|max:6'
          ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            redirect('/');
        }
        if (!$this->fetch('Profil', ['user_id' => $_SESSION['user_id']])) {
            $this->insert('Profil', [
            'user_id' => $_SESSION['user_id'],
            'localisation' => $data['city'].', '.$data['country'],
            'longitude' => $convLongitude ?? $data['longitude'],
            'latitude' => $convLatitude ?? $data['latitude'],
          ]);
        } else {
            $this->update('Profil', [
            'localisation' => $data['city'].', '.$data['country'],
            'longitude' => $convLongitude ?? $data['longitude'],
            'latitude' => $convLatitude ?? $data['latitude']
          ], ['user_id' => $_SESSION['user_id']]);
        }
    }
}
