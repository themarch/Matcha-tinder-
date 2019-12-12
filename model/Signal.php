<?php


class Signal
{
    public function fetchBlockedUser($userId)
    {
        $sql = "SELECT lastname,firstname,Profil.id AS 'profil_id',User.id AS 'user_id'
        FROM User INNER JOIN Profil ON Profil.user_id = User.id
        INNER JOIN Blocked ON Blocked.blocked_user = User.id WHERE Blocked.user_id = ?";
        return (execQuery($this->db, $sql, [$userId], PDO::FETCH_ASSOC, FETCH_ALL));
    }
}
