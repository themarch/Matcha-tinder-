<?php

class ChatController extends Models
{
    public function fetchMatchedUser()
    {
        $matchedUser = $this->chat->getMatchedUser($_SESSION['user_id']);
        $userId = $_SESSION['user_id'];
        $result = [];
        if (($notifications = $this->notification->getAllMessageNotif($_SESSION['user_id']))) {
            foreach ($notifications as $value) {
                if ($value['is_seen'] == 1) {
                    continue;
                }
                $dstMsg = $this->notification->getUserRoom($_SESSION['user_id'], $value['room_id']);
                if ($dstMsg !== false && !$this->fetch('Blocked', ['user_id' => $userId, 'blocked_user' => $dstMsg['id']])) {
                    !isset($result[$value['room_id']]['countMessage']) ? $result[$value['room_id']]['countMessage'] = 1 : $result[$value['room_id']]['countMessage']++;
                }
            }
        }
        echo encodeToJs(['matched' => group_by('room_id', $matchedUser), 'notifications' => $result]);
    }

    public function addMessage()
    {
        $request = new Request();
        $data = $request->toJson();
        if (!keysExist(['roomId', 'message', 'time'], $data)) {
            redirect('/');
        }
        $sanitizeMsg = filter_var($data['message'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$sanitizeMsg || !validateDate($data['time'])) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
              'userId' => 'digit|min:1|max:11',
              'roomId' => 'digit|min:1|max:11'
          ],
            'sendToJs',
            Message::$userMessages
        );
        $this->insert('Message', [
          'user_id' => $_SESSION['user_id'],
          'room_id' => $data['roomId'],
          'content' => $sanitizeMsg,
          'date' => $data['time']]);
        // is room open / le user => triger pas les notifs.
        $dstUser = $this->notification->getUserRoom($_SESSION['user_id'], $data['roomId']);
        if ($dstUser !== false) {
            $this->insert('Notification', ['user_id' => $dstUser['id'], 'room_id' => $data['roomId'], 'name' => 'addroomMessage']);
        }
        $this->update('Rooms', ['last_msg_date' => $data['time']], ['id' => $data['roomId']]);
    }

    public function searchMatchedUser()
    {
        // si le user n'est pas bloque
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['search'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
            'search' => 'alpha|min:1|max:30'
          ],
            'sendToJs',
            Message::$userMessages
        );
        if (!empty($validate->loadedMessage)) {
            return ;
        }
        $result = $this->chat->findMatchedUser($_SESSION['user_id'], $data['search']);
        echo json_encode(['searchMatchedUser' => $result]);
    }

    public function findUserRoom()
    {
        // si le user n'est pas bloque
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['userId'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
          'userId' => 'digit|min:1|max:11'
        ],
            'sendToJs',
            Message::$userMessages
      );
        if (!empty($validate->loadedMessage)) {
            return ;
        }
        $roomId = 0;
        $haveMessages = false;
        if (($result = $this->chat->getRoom($_SESSION['user_id'], $data['userId'])) !== false) {
            if ($this->fetch('Message', ['room_id' => $result['id']])) {
                $haveMessages = true;
            }
            $roomId = $result['id'];
        } else {
            $this->insert('Rooms', ['user1_id' => $_SESSION['user_id'], 'user2_id' => $data['userId']]);
            $room = $this->fetch('Rooms', ['user1_id' => $_SESSION['user_id'], 'user2_id' => $data['userId']]);
            $roomId = $room['id'];
        }
        echo encodeToJs(['room_id' => $roomId, 'messageExist' => $haveMessages]);
    }

    public function updateNotification()
    {
        $request = new Request();
        $data = $request->toJson();

        if (!keysExist(['userId', 'roomId'], $data)) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
          'userId' => 'digit|min:1|max:11',
          'roomId' => 'digit|min:1|max:11'
        ],
            'sendToJs',
            Message::$userMessages
      );
        if (!empty($validate->loadedMessage)) {
            return ;
        }
        $this->update('Notification', ['is_seen' => 1], ['user_id' => $data['userId'], 'room_id' => $data['roomId']]);
    }
}
