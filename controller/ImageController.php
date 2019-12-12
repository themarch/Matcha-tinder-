<?php

class ImageController extends Models
{
    public function addProfilImg()
    {
        $request = new Request();
        $data = $request->toJson();
        if (!keysExist(['name'], $data) || strlen($data['name']) > 255) {
            redirect('/');
        }
        $data['name'] = filter_var($data['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $path = dirname(__DIR__).'/ressources/images/'.$_SESSION['user_id'].'/'.sha1($data['name']).'.png';
        if (file_exists($path)) {
            $this->update('Profil', ['profile_pic_path' => $path, 'profile_pic_name' => $data['name']], ['user_id' => $_SESSION['user_id']]);
            echo encodeToJs(['path' => base64_encode(file_get_contents($path))]);
        }
    }

    public function addImg()
    {
        // nombres images inferieur a 5.
        $request = new Request();
        $data = $request->toJson();
        // regex pour les names des images ---
        if (!keysExist(['image', 'name'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
              'image' => 'image'
          ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            redirect('/');
        }
        if (($allImg = $this->fetchAll('Image', ['user_id' => $_SESSION['user_id']]))) {
            if (count($allImg) == 5) {
                outputJsError('Nombre maximun d\'image : 5');
            }
        }
        if ($this->fetch('Image', ['user_id' => $_SESSION['user_id'], 'name' => $data['name']])) {
            outputJsError("Le nom est déjà utilise par une autre image");
        }
        if (!file_exists(dirname(__DIR__).'/ressources/images/'.$_SESSION['user_id'])) {
            mkdir(dirname(__DIR__).'/ressources/images/'.$_SESSION['user_id']);
        }
        $path = dirname(__DIR__).'/ressources/images/'.$_SESSION['user_id'].'/'.sha1($data['name']).'.png';
        $image = imagecreatefromstring(extract_base64(trim($data['image'])));
        imagepng($image, $path);
        $this->insert('Image', ['name' => $data['name'], 'user_id' => $_SESSION['user_id'], 'path' => $path]);
        echo encodeToJs(['path' => base64_encode(file_get_contents($path))]);
    }

    public function deleteImg()
    {
        $request = new Request();
        $data = $request->toJson();
        // validate
        if (!keysExist(['name'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
              'name' => 'alphanum'
          ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            redirect('/');
        }
        $path = dirname(__DIR__).'/ressources/images/'.$_SESSION['user_id'].'/'.sha1($data['name']).'.png';
        if (file_exists($path)) {
            unlink($path);
        }
        if (isset($data['isProfilPic']) && $data['isProfilPic'] === 1) {
            $this->delete('Image', ['name' => $data['name'], 'user_id' => $_SESSION['user_id']]);
            $this->update('Profil', ['profile_pic_name' => '', 'profile_pic_path' => ''], ['user_id' => $_SESSION['user_id']]);
        } else {
            $this->delete('Image', ['name' => $data['name'], 'user_id' => $_SESSION['user_id']]);
        }
    }

    public function getImg()
    {
        $result = $this->fetchAll('Image', ['user_id' => $_SESSION['user_id']], PDO::FETCH_ASSOC);
        if (!$result) {
            redirect('/');
        }
        foreach ($result as &$value) {
            $name = $value['name'];
            if (isset($name)) {
                $path = dirname(__DIR__).'/ressources/images/'.$_SESSION['user_id'].'/'.sha1($name).'.png';
                $im = file_get_contents($path);
                $value['path'] = base64_encode($im);
            }
        }
        echo encodeToJs($result);
    }

    public function getProfilPic()
    {
        $result = $this->fetch('Profil', ['user_id' => $_SESSION['user_id']], PDO::FETCH_ASSOC);
        if (!$result || empty($result['profile_pic_path'])) {
            $defaultImg = dirname(__DIR__).'/ressources/images/default-profile.png';
            $path = base64_encode(file_get_contents($defaultImg));
            echo encodeToJs(['path' => $path, 'name' => 'defaultProfil']);
        } else {
            $path = base64_encode(file_get_contents($result['profile_pic_path']));
            $name = $result['profile_pic_name'];
            echo encodeToJs(['path' => $path ?? null, 'name' => $name ?? null]);
        }
    }

    public function getConsultedProfilPic()
    {
        $request = new Request();
        $data = $request->toJson();
        // validate
        if (!keysExist(['userId'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
              'userId' => 'digit|max:11'
          ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            redirect('/');
        }
        $result = $this->fetch('Profil', ['user_id' => $data['userId']], PDO::FETCH_ASSOC);
        if (!$result || empty($result['profile_pic_path'])) {
            $defaultImg = dirname(__DIR__).'/ressources/images/default-profile.png';
            $path = base64_encode(file_get_contents($defaultImg));
            echo encodeToJs(['path' => $path, 'name' => 'defaultProfil']);
        } else {
            $path = base64_encode(file_get_contents($result['profile_pic_path']));
            $name = $result['profile_pic_name'];
            echo encodeToJs(['path' => $path ?? null, 'name' => $name ?? null]);
        }
    }
}
