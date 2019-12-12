<?php

class Profil
{
    public function fetchUserProfilData($userId)
    {
        $sql = "SELECT firstname,lastname,age,bio,score,genre,orientation,profile_pic_name,localisation,user_id
        FROM User INNER JOIN Profil ON User.id = Profil.user_id WHERE Profil.user_id = ?";
        $userInfo = execQuery($this->db, $sql, [$userId], PDO::FETCH_ASSOC, FETCH_ONE);
        $sql = "SELECT name FROM Tag WHERE user_id=?";
        $userTags = execQuery($this->db, $sql, [$userId], PDO::FETCH_COLUMN, FETCH_ALL);
        $userInfo['tags'] = $userTags;
        return ($userInfo);
    }

    public function fetchProfilViews($userId)
    {
        $sql ="SELECT lastname,firstname,age,localisation,last_visited,profile_pic_path,profile_pic_name,
      Profil.user_id AS 'user_id', Profil.id AS 'profil_id' FROM Profil INNER JOIN User ON Profil.user_id = User.id
      INNER JOIN Visite ON Visite.visiter_id = User.id WHERE Visite.user_id = ?";
        $visitedUserInfo = execQuery($this->db, $sql, [$userId], PDO::FETCH_ASSOC, FETCH_ALL);
        $sql = "SELECT name,Visite.visiter_id FROM Tag INNER JOIN Visite ON Visite.visiter_id = Tag.user_id WHERE Visite.user_id = ?";
        $visitedUserTags = execQuery($this->db, $sql, [$userId], PDO::FETCH_ASSOC, FETCH_ALL);
        $visitedUserInfo['visiterTags'] = $visitedUserTags;
        return ($visitedUserInfo);
    }

    public function fetchProfilLikes($userId)
    {
        $sql ="SELECT lastname,firstname,age,localisation,last_visited,profile_pic_path,profile_pic_name,
    Profil.user_id AS 'user_id', Profil.id AS 'profil_id' FROM Profil INNER JOIN User ON Profil.user_id = User.id
    INNER JOIN Likes ON Likes.liked_by = User.id WHERE Likes.user_id = ?";
        $likeUserInfo = execQuery($this->db, $sql, [$userId], PDO::FETCH_ASSOC, FETCH_ALL);
        $sql = "SELECT name,Likes.liked_by FROM Tag INNER JOIN Likes ON Likes.liked_by = Tag.user_id WHERE Likes.user_id = ?";
        $likeUserTags = execQuery($this->db, $sql, [$userId], PDO::FETCH_ASSOC, FETCH_ALL);
        $likeUserInfo['likesTags'] = $likeUserTags;
        return ($likeUserInfo);
    }
}
