<?php
class Tag
{
    public function fetchTagList()
    {
        $sql = "SELECT name, count AS 'tag_count' FROM Tag_list ORDER BY tag_count DESC LIMIT 10";
        return (execQuery($this->db, $sql, [], PDO::FETCH_ASSOC, FETCH_ALL));
    }
}
