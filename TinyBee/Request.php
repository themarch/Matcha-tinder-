<?php

class Request
{
    private $request;
    public $headers;

    /**
     * Get the request data.
     */
    public function __construct()
    {
        $this->run();
    }

    /**
     * Resolve the request method and set the data.
     * @return void
     */
    private function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (isset($_POST)  && !empty($_POST) && $method === 'POST') {
            $this->request = $_POST;
        } elseif (isset($_GET) && !empty($_GET) && $method === 'GET') {
            $this->request = $_GET;
        } else {
            $input = file_get_contents('php://input');
            if (isset($input) && !empty($input)) {
                $this->request = $input;
            }
        }
    }

    /**
     * Get all request headers
     * @return array
     */
    public function getHeaders()
    {
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }

    /**
     * Get stored request data if they are available
     * @return array request data or null if no data
     */
    public function get()
    {
        return ($this->request ?? null);
    }

    /**
     * Convert a json object to an array.
     * @param  boolean - true by default (create an array)
     * @return array - by default return the request data
     */
    public function toJson($param = true)
    {
        if (is_bool($param)) {
            $this->request = json_decode($this->request, $param);
        }
        return ($this->request);
    }
}
