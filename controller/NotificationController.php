<?php


class NotificationController extends Models
{
    public function getAll()
    {
        if (($notifications = $this->notification->getAllNotif($_SESSION['user_id']))) {
            $result = [];
            $userId = $_SESSION['user_id'];
            $countNotSeen = 0;
            foreach ($notifications as $value) {
                if ($value['name'] === null) {
                    continue;
                }
                if ($value['name'] == 'like' && !$this->fetch('Blocked', ['user_id' => $userId, 'blocked_user' => $value['liked_by']])) {
                    $result[] = $this->notification->getUserInfo($value['liked_by'], $value['name'], $value['is_seen']);
                    $countNotSeen += $value['is_seen'] == 0 ?  1 : 0;
                }
                if ($value['name'] == 'unmatch' && !$this->fetch('Blocked', ['user_id' => $userId, 'blocked_user' => $value['unmatched_by']])) {
                    $result[] = $this->notification->getUserInfo($value['unmatched_by'], $value['name'], $value['is_seen']);
                    $countNotSeen += $value['is_seen'] == 0 ?  1 : 0;
                }
                if ($value['name'] == 'match' && !$this->fetch('Blocked', ['user_id' => $userId, 'blocked_user' => $value['liked_by']])) {
                    $result[] = $this->notification->getUserInfo($value['liked_by'], $value['name'], $value['is_seen']);
                    $countNotSeen += $value['is_seen'] == 0 ?  1 : 0;
                }
                if ($value['name'] == 'visiter' && !$this->fetch('Blocked', ['user_id' => $userId, 'blocked_user' => $value['visiter_id']])) {
                    $result[] = $this->notification->getVisiter($value['visiter_id'], $userId, $value['is_seen']);
                    $countNotSeen += $value['is_seen'] == 0 ?  1 : 0;
                }
                if ($value['name'] == 'addroomMessage' && $value['is_seen'] == 0) {
                    $dstMsg = $this->notification->getUserRoom($_SESSION['user_id'], $value['room_id']);
                    if ($dstMsg !== false && !$this->fetch('Blocked', ['user_id' => $userId, 'blocked_user' => $dstMsg['id']])) {
                        $countNotSeen += $value['is_seen'] == 0 ?  1 : 0;
                        !isset($result['countMessage']) ? $result['countMessage'] = 1 : $result['countMessage']++;
                    }
                }
            }
            $result['notifCount'] = $countNotSeen - ((isset($result['countMessage']) ? $result['countMessage'] : 0));
            echo encodeToJs(['notifications' => $result]);
        }
    }

    public function setSeenNotification()
    {
        $request = new Request();
        $data = $request->toJson();
        if (!keysExist(['notification'], $data)) {
            redirect('/');
        }
        foreach ($data['notification'] as $key => $value) {
            if (isset($value['type']) && ($value['type'] == 'like' || $value['type'] == 'visiter' || $value['type'] == 'match' || $value['type'] == 'unmatch')) {
                $this->update('Notification', ['is_seen' => 1], ['user_id' => $_SESSION['user_id'], 'name' => $value['type']]);
            }
        }
    }
}
