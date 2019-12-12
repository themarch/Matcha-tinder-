<?php

trait ProfilTrait
{
    public function getEditType($data)
    {
        $type = '';
        if (array_key_exists('genre', $data) && !empty($data['genre'])) {
            if ($data['genre'] !== ('Homme' || 'Femme')) {
                redirect('/');
            }
            $type = 'genre';
        }
        if (array_key_exists('orientation', $data) && !empty($data['orientation'])) {
            if ($data['orientation'] !== ('Homosexuel' || 'Hétérosexuel' || 'Bisexuel')) {
                redirect('/');
            }
            $type = 'orientation';
        }
        if (array_key_exists('bio', $data) && !empty($data['bio'])) {
            $validate = new Validate($data, ['bio' => 'text|min:5|max:1024'], 'editProfil.php', Message::$userMessages);
            $type = 'bio';
        }
        if (keysExist(['city', 'country'], $data)) {
            $validate = new Validate(
                $data,
                ['city' => 'alpha|min:3|max:100', 'country' => 'alpha|min:3|max:100'],
                'editProfil.php',
                Message::$userMessages
            );
            echo encodeToJs(['city' => $data['city'], 'country' => $data['country']]);
            $type = 'localisation';
        }
        return ($type);
    }

    public function getProfilPicById()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['user_id'], $data)) {
            redirect('/');
        }
        $result = $this->fetch('Profil', ['user_id' => $data['user_id']], PDO::FETCH_ASSOC);
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
