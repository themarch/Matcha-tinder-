<?php

class Chat
{
    public function getMatchedUser($userId)
    {
        $sql = "SELECT Message.date as 'msg_time',profile_pic_path,lastname,firstname,Message.room_id AS 'room_id',
        Message.user_id as 'user_msg_id',content,
        Message.date as 'post_msg_time',user_profil_id FROM Matched
        INNER JOIN Message ON Message.user_id = Matched.user_id OR Message.user_id = user_profil_id
        INNER JOIN Rooms ON Rooms.id = Message.room_id AND ((Rooms.user1_id = Matched.user_id
        AND Rooms.user2_id = user_profil_id) OR (Rooms.user1_id = user_profil_id AND Rooms.user2_id = Matched.user_id))
        INNER JOIN Profil ON Profil.user_id = Message.user_id
        INNER JOIN User ON User.id = Profil.user_id
        WHERE Matched.user_id = ? AND user_profil_id NOT IN (SELECT blocked_user FROM Blocked WHERE Blocked.user_id = ?)";
        return (execQuery($this->db, $sql, [$userId, $userId], PDO::FETCH_ASSOC, FETCH_ALL));
    }

    public function findMatchedUser($userId, $search)
    {
        $sql = "SELECT firstname,lastname,user_profil_id FROM User
          INNER JOIN Matched ON (Matched.user_profil_id = User.id AND Matched.user_id = ?)
          WHERE (lastname LIKE ? OR firstname LIKE ?) AND user_profil_id NOT IN (SELECT blocked_user FROM Blocked WHERE Blocked.user_id = ?)
          GROUP BY Matched.user_profil_id";
        return (execQuery($this->db, $sql, [$userId, "%$search%", "%$search%", $userId], PDO::FETCH_ASSOC, FETCH_ALL));
    }

    public function getRoom($userId, $search)
    {
        $sql = "SELECT * FROM Rooms WHERE ((user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?))";
        return (execQuery($this->db, $sql, [$userId, $search, $search, $userId], PDO::FETCH_ASSOC, FETCH_ONE));
    }
}
