<?php

class Notification
{
    public function getUserInfo($userId, $type, $isSeen)
    {
        $sql = "SELECT firstname,lastname,User.id FROM User WHERE User.id = ?";
        $result = execQuery($this->db, $sql, [$userId], PDO::FETCH_ASSOC, FETCH_ONE);
        $result['type'] = $type;
        $result['isSeen'] = $isSeen;
        return ($result);
    }

    public function getVisiter($userId, $currentUser, $isSeen)
    {
        $sql = "SELECT firstname,lastname,User.id FROM User
        INNER JOIN Visite ON User.id = Visite.visiter_id WHERE Visite.visiter_id = ? AND Visite.user_id = ?";
        $result = execQuery($this->db, $sql, [$userId, $currentUser], PDO::FETCH_ASSOC, FETCH_ONE);
        $result['type'] = 'visiter';
        $result['isSeen'] = $isSeen;
        return ($result);
    }

    public function getUserRoom($userId, $roomId)
    {
        $sql = "SELECT firstname,lastname,User.id,Rooms.id AS 'room_id' FROM User
        INNER JOIN Rooms ON Rooms.user1_id = User.id OR Rooms.user2_id = User.id
        WHERE Rooms.id = ?";
        $result = execQuery($this->db, $sql, [$roomId], PDO::FETCH_ASSOC, FETCH_ALL);
        $result['type'] = 'message';
        if ($result[0]['id'] !== $userId) {
            return ($result[0]);
        } elseif ($result[1]['id'] !== $userId) {
            return ($result[1]);
        } else {
            return (false);
        }
    }

    public function getAllNotif($userId)
    {
        $sql = "SELECT * FROM Notification WHERE user_id = ? ORDER BY created_at DESC";
        $result = execQuery($this->db, $sql, [$userId], PDO::FETCH_ASSOC, FETCH_ALL);
        return ($result);
    }

    public function countSeenNotification($userId)
    {
        $sql = "SELECT count(is_seen) AS 'is_seen_counter' FROM Notification WHERE user_id = ? AND is_seen = 0";
        $result = execQuery($this->db, $sql, [$userId], PDO::FETCH_ASSOC, FETCH_ONE);
        return ($result['is_seen_counter']);
    }

    public function getAllMessageNotif($userId)
    {
        $sql = "SELECT * FROM Notification WHERE user_id = ? AND name ='addroomMessage'";
        $result = execQuery($this->db, $sql, [$userId], PDO::FETCH_ASSOC, FETCH_ALL);
        return ($result);
    }

    //public function updateChatNotification($userId)
}
