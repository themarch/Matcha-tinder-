<?php


class User
{
    public function getOnlineUser($profilId)
    {
        $sql = "SELECT last_visited FROM `Profil` INNER JOIN User ON User.id = Profil.user_id WHERE Profil.user_id = ?";
        return (execQuery($this->db, $sql, [$profilId], PDO::FETCH_ASSOC, FETCH_ONE));
    }

    public function setDefaultOrientation($value, $id)
    {
        $sql = "INSERT INTO Profil (orientation) VALUES (?) WHERE user_id = ?";
        $prepare = $this->db->prepare($sql);
        $prepare->execute([$value, $id]);
    }
}
