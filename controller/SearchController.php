<?php

require_once('SearchAlgoTrait.php');

class SearchController extends Models
{
    use SearchAlgoTrait;

    public function searchSugestions()
    {
        $result = [];
        $usersCollection = $this->search->fetchAllUsersInfo();
        $currentUser = $this->search->fetchCurrentUserInfo($_SESSION['user_id']);
        $commonTags = $this->search->fetchTags($_SESSION['user_id']);
        $blockedUsers = $this->search->fetchBlockedUser($_SESSION['user_id']);
        $currentUserTags = oneDimArray($this->search->fetchUserTags($_SESSION['user_id']));

        foreach ($usersCollection as $user) {
            if ($currentUser->id == $user->id || isset($blockedUsers[$user->id])) {
                continue;
            }
            $targetUserTags = '';
            $score = 0.0;
            $match = 0;
            if ($currentUser->orientation !== null && $user->orientation !== null) {
                if ($currentUser->orientation && !isset($user->orientation) && ($currentUser->orientation == 'bisexuel')) {
                    $score += 10;
                } elseif ($user->orientation && $currentUser->orientation === $user->orientation) {
                    $score += $this->getOrientation($currentUser->orientation, $user->orientation, $currentUser->genre, $user->genre);
                } else {
                    if ($currentUser->genre == $user->genre && $currentUser->orientation == 'homosexuel') {
                        $score += 10;
                    } elseif ($currentUser->orientation == 'bisexuel') {
                        $score += 10;
                    } else {
                        $score += 25;
                    }
                }
                $score > 0.0 ? $match++ : $match;
            }
            $distance = geoCoordsDistance($currentUser->latitude, $currentUser->longitude, $user->latitude, $user->longitude);
            if ($distance !== 0.0) {
                $match++;
                $score += $this->getDistance($distance);
            }
            if ($currentUser->score !== null && $user->score !== null) {
                $match++;
                $score += $this->getPopularity($currentUser->score, $user->score);
            }
            if (isset($commonTags[$user->id])) {
                $match++;
                $targetUserTags = oneDimArray($commonTags[$user->id]);
                $score += $this->getTags(
                    $currentUserTags,
                    $targetUserTags
              );
            } else {
                $score += 5;
            }
            if ($match !== 0 && $score < 40) {
                $formatDist = round($distance, 3);
                $user->computed_score = $score;
                $user->distance = $formatDist;
                $user->km = (int)$formatDist;
                $user->meters = (int)(($formatDist - (int)$formatDist) * 1000);
                $user->commonTags = $targetUserTags;
                $result[] = $user;
            }
        }
        usort($result, function ($a, $b) {
            return $a->computed_score > $b->computed_score;
        });
        $key = 'sugestion:'.$_SESSION['user_id'];
        $this->redis->set($key, encodeToJs($result));
        echo encodeToJs(['sugestions' => array_slice($result, 0, 10), 'userTags' => $currentUserTags]);
    }

    public function paginateSugestion()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['pageNumber', 'type'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
            'type' => 'alpha',
            'pageNumber' => 'digit|max:4'
          ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            redirect('/');
        }
        if ($data['type'] == 'sugestion') {
            $key = 'sugestion:'.$_SESSION['user_id'];
        } elseif ($data['type'] == 'filter') {
            $key = 'filterResult:'.$_SESSION['user_id'];
        } elseif ($data['type'] == 'search') {
            $key = 'search:'.$_SESSION['user_id'];
        } elseif ($data['type'] == 'searchFilter') {
            $key = 'searchFilter:'.$_SESSION['user_id'];
        }
        $result = json_decode($this->redis->get($key));
        $startData = (($data['pageNumber'] - 1) * 10);
        echo encodeToJs(['result' => array_slice($result, $startData, 10)]);
    }

    public function manageResult()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['filterResult', 'sortResult'], $data['content'])) {
            redirect('/');
        }
        if (empty($data['content']['filterResult']) && empty($data['content']['sortResult'])) {
            return ;
        }
        if ($data['isSearch'] === true) {
            $key = 'searchFilter:'.$_SESSION['user_id'];
        } else {
            $key = 'filterResult:'.$_SESSION['user_id'];
        }
        if ($this->redis->exists($key)) {
            $this->redis->del($key);
        }
        if (!empty($data['content']['filterResult'])) {
            $result = $this->filterResult($data['content']['filterResult'], $data['isSearch']);
        }
        if (!empty($data['content']['sortResult'])) {
            $result = $this->sortResult($data['content']['sortResult'], $data['isSearch']);
        }
        echo encodeToJs($result);
    }

    public function searchResult()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['filterResult'], $data)) {
            redirect('/');
        }

        // si il est bloque => nop...
        $currentUser = $this->search->fetchCurrentUserInfo($_SESSION['user_id']);
        $result = $this->search->findResult($data['filterResult']);
        if (!$result || empty($result)) {
            die('result error');
        }
        $data = [];
        if (isset($data['filterResult']['tags'])) {
            $count = count($data['filterResult']['tags']);
            foreach ($result as $id => $user) {
                $tags = $this->formatTags($user);
                $info = $user[0];
                $distance = geoCoordsDistance($currentUser->latitude, $currentUser->longitude, $info->latitude, $info->longitude);
                $formatDist = round($distance, 3);
                if (count(array_intersect($tags, $data['filterResult']['tags'])) == $count) {
                    $data[] = [
                      'id' => $id, 'age' => $info->age, 'score' => $info->score, 'commonTags' => $tags,
                      'lastname' => $info->lastname, 'firstname' => $info->firstname, 'localisation' => $info->localisation, 'distance' => $distance,
                      'km' => (int)$formatDist, 'meters' => (int)(($formatDist - (int)$formatDist) * 1000), 'distance' => $formatDist
                  ];
                }
            }
        } else {
            foreach ($result as $id => $user) {
                $tags = $this->formatTags($user);
                $info = $user[0];
                $distance = geoCoordsDistance($currentUser->latitude, $currentUser->longitude, $info->latitude, $info->longitude);
                $formatDist = round($distance, 3);
                $data[] = [
                  'id' => $id, 'age' => $info->age, 'score' => $info->score, 'commonTags' => $tags,
                  'lastname' => $info->lastname, 'firstname' => $info->firstname, 'localisation' => $info->localisation, 'km' => (int)$formatDist,
                  'meters' => (int)(($formatDist - (int)$formatDist) * 1000), 'distance' => $formatDist
                ];
            }
        }
        $key = 'search:'.$_SESSION['user_id'];
        $this->redis->set($key, encodeToJs($data));
    }

    public function getResults()
    {
        $key = 'search:'.$_SESSION['user_id'];
        if ($this->redis->exists($key)) {
            $data = json_decode($this->redis->get($key));
            echo encodeToJs(['search' => array_slice($data, 0, 10)]);
        }
    }

    public function loadSugestions()
    {
        if (!isAuth()) {
            view('sugestions.php', ['info' => 'loadPic']);
        }
        $needGeoLoc = false;
        if (isset($_SESSION['needGeoLoc']) && $_SESSION['needGeoLoc'] === true) {
            $needGeoLoc = true;
        }
        $profilInfo = $this->fetch('Profil', ['user_id' => $_SESSION['user_id']], PDO::FETCH_ASSOC);
        if (!$profilInfo || in_array(0, $profilInfo, true) || in_array(null, $profilInfo, true) || in_array('', $profilInfo, true)) {
            view('sugestions.php', ['info' => 'profilInfo', 'geoLoc' => $needGeoLoc]);
        }
        $tags = $this->fetchAll('Tag', ['user_id' => $_SESSION['user_id']]);
        if (!$tags) {
            view('sugestions.php', ['info' => 'profilInfo', 'geoLoc' => $needGeoLoc]);
        }
        view('sugestions.php', ['geoLoc' => $needGeoLoc]);
    }
}
