<?php

class TagController extends Models
{
    public function addTag()
    {
        $request = new Request();
        $isNewProfil = false;
        $data = $request->toJson();
        if (!array_key_exists('name', $data) || empty($data['name'])) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
              'tag' => 'alpha|min:3|max:256'
            ],
            'sendToJs',
            Message::$userMessages
        );
        $data['name'] = strtolower($data['name']);
        if ($this->fetch('Tag', ['user_id' => $_SESSION['user_id'], 'name' => $data['name']])) {
            die();
        }
        if (($tag = $this->fetch('Tag_list', ['name' => $data['name']]))) {
            $this->update('Tag_list', ['count' => $tag['count'] + 1], ['name' => $data['name']]);
        } else {
            $this->insert('Tag_list', ['name' => $data['name'], 'count' => 1]);
        }
        if (!empty($validate->loadedMessage)) {
            echo encodeToJs(['Message' => $validate->loadedMessage]);
        } else {
            $this->insert('Tag', ['user_id' => $_SESSION['user_id'], 'name' => $data['name']]);
        }
    }

    public function deleteTag()
    {
        $request = new Request();
        $data = $request->toJson();
        if (!array_key_exists('name', $data) || empty($data['name'])) {
            redirect('/');
        }
        $validate = new Validate(
            $data,
            [
              'tag' => 'alpha|min:3|max:256'
            ],
            'sendToJs',
            Message::$userMessages
        );
        $data['name'] = strtolower($data['name']);
        if (!empty($validate->loadedMessage)) {
            echo encodeToJs(['Message' => $validate->loadedMessage]);
        } else {
            $this->delete('Tag', ['name' => $data['name']], ['user_id' => $_SESSION['user_id']]);
        }
        if (($tag = $this->fetch('Tag_list', ['name' => $data['name']]))) {
            if ($tag['count'] !== 0) {
                $this->update('Tag_list', ['count' => $tag['count'] - 1], ['name' => $data['name']]);
            } else {
                $this->delete('Tag_list', ['name' => $data['name']]);
            }
        }
    }

    public function getTag()
    {
        $tags = $this->fetchAll('Tag', ['user_id' => $_SESSION['user_id']], PDO::FETCH_ASSOC);
        if ($tags) {
            echo encodeToJs($tags);
        }
    }

    public function getTagList()
    {
        $tagsList = $this->tag->fetchTagList();
        if ($tagsList) {
            echo encodeToJs($tagsList);
        }
    }
}
