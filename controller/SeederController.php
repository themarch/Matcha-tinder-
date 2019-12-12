<?php

class SeederController extends Models
{
    /**
     * Optimized sql insert.
     */
    public function storeSeed()
    {
        $request = new Request();
        $data = $request->toJson();
        // docker / mac config
        // $connection = mysqli_connect("mysql", "root", "rootpass");
        // windows / xamp config
        $connection = mysqli_connect("mysql", "root", "rootpass");
        $db_select = mysqli_select_db($connection, "Matcha");
        $sqlUser = '';
        $sqlProfil = '';
        $sqlTag = '';
        $lastId = mysqli_query($connection, "SELECT id FROM User ORDER BY id DESC LIMIT 1")->fetch_row()[0];
        $currentId = ++$lastId;
        $tags = [];
        foreach ($data as $value) {
            if (isset($value['login'], $value['age'], $value['name'], $value['email'])) {
                $email = mysqli_real_escape_string($connection, $value['email']);
                $sqlUser .= "('".$value['login']['username']."','".password_hash("Test1234", PASSWORD_BCRYPT, ['cost' => 4])."','".$value['name']['first']."','"
                .$value['name']['last']."','".$value['age']."',1,'.$email.'),";
            }
            if (isset($value['location'], $value['gender'], $value['score'], $value['picture'], $value['orientation'])) {
                $genre = $value['gender'] == 'male' ? 'homme' : 'femme';
                $sqlProfil .= "('".$currentId."','".'lorem'."','"
              .$value['score']."','".$genre."','".$value['orientation']."','".strtolower($value['location']['city']).", France','"
              .$value['picture']['large']."','".$value['picture']['name']."','".$value['location']['longitude'].
              "','".$value['location']['latitude']."'),";
            }

            if (isset($value['tags'])) {
                foreach ($value['tags'] as $value) {
                    if (!array_key_exists($value, $tags)) {
                        $tags[$value] = 1;
                    } else {
                        $tags[$value]++;
                    }
                    $sqlTag .= "('".$currentId."','".$value."'),";
                }
            }
            ++$currentId;
        }
        $sqlUserRes = substr($sqlUser, 0, -1);
        $sqlProfilRes = substr($sqlProfil, 0, -1);
        $sqlTagRes = substr($sqlTag, 0, -1);
        mysqli_query($connection, "INSERT INTO User (username,password,firstname,lastname,age,is_confirmed,email) VALUES {$sqlUserRes}");
        mysqli_query($connection, "INSERT INTO Profil (user_id,bio,score,genre,orientation,localisation,profile_pic_path,profile_pic_name,longitude,latitude)
        VALUES {$sqlProfilRes}");
        mysqli_query($connection, "INSERT INTO Tag (user_id, name) VALUES {$sqlTagRes}");
        $sqlTagList = '';
        foreach ($tags as $k => $v) {
            $sqlTagList .= "('".$k."','".$v."'),";
        }
        $sqlTagList = substr($sqlTagList, 0, -1);
        mysqli_query($connection, "INSERT INTO Tag_list (name, count) VALUES {$sqlTagList}");
        mysqli_close($connection);
    }
}
