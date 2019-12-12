<?php

class AntiCsrf
{
    /**
     * Refresh expired token if needed.
     */
    public function __construct()
    {
        if (!isset($_SESSION['csrf_token']) || $this->isExpired()) {
            $this->create();
        }
    }

    /**
     * Create a fresh token.
     * @return void
     */
    private function create()
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_time'] = time();
    }

    /**
     * Check if the current token is expired, after 30 minutes a new token is generated.
     * @return boolean
     */
    private function isExpired()
    {
        return ($this->getMinuteDiff($_SESSION['csrf_time'], time()) > 30 ? true : false);
    }

    /**
     * Return the difference between two timestamps.
     * @param  int old timestamp
     * @param  int current timestamp
     * @return int
     */
    private function getMinuteDiff($start, $end)
    {
        return (($end - $start) / 60);
    }

    /**
     * Check if a csrf token is provided in headers (javascript call) or in a POST request (php forms)
     * @return boolean
     */
    public function check()
    {
        $req = new Request();
        $headers = $req->getHeaders();
        $data = $req->get();

        if (isset($headers['X-Csrf-Token'])) {
            return (hash_equals($_SESSION['csrf_token'], $headers['X-Csrf-Token']));
        }
        if (isset($data['csrf_token'])) {
            return (hash_equals($_SESSION['csrf_token'], $data['csrf_token']));
        }
        return (false);
    }
}
