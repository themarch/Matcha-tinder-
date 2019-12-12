<?php
class Validate
{
    private $path;
    private $message;
    public $loadedMessage = [];
    private $customType;

    /**
     * Load a given filter on the request's data
     * @param array - data to filter
     * @param array - filter rules
     * @param string - path to view
     * @param array - message displayed to the user if any check failed.
     * @param array - custom data accessible from view
     */
    public function __construct($data, $filter, $path, $message, $customType = null)
    {
        $this->path = $path;
        $this->message = $message;
        $this->customType = $customType ?? null;
        $this->filterData($data, $filter);
    }

    /**
     * Load rules on the request's data and call a filter method
     * @param array - data to filter
     * @param array - filter rules
     * @return void
     */
    private function filterData($data, $filter)
    {
        foreach ($filter as $column => $filterValue) {
            if (isset($data[$column])) {
                $filterArray = explode('|', $filterValue);
                foreach ($filterArray as $value) {
                    if (strpos($value, ':') !== false) {
                        $filterLength = explode(':', $value);
                        if (count($filterLength) == 2) {
                            $method = 'valid'.ucfirst(strtolower($filterLength[0]));
                            if (method_exists(__CLASS__, $method)) {
                                $this->{$method}($column, $data[$column], $filterLength[1]);
                            }
                        }
                    } else {
                        $method = 'valid'.ucfirst(strtolower($value));
                        if (method_exists(__CLASS__, $method)) {
                            $this->{$method}($column, $data[$column]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Load a message into view ($warning) or store the messages (ex: js call).
     * @param  string - message name
     * @param  string - custom data name
     * @param  array - associative array of messages (name => value)
     * @return void
     */
    public function loadMessage($column, $customTypeKey, $message)
    {
        if ($this->path == "sendToJs") {
            $this->loadedMessage[] = $this->message[$column];
        } else {
            view(
                $this->path,
                [
              'warning' => $message,
              $customTypeKey => $this->customType[$customTypeKey] ?? 'null'
            ]
          );
        }
    }

    /**
     * Check if the string value is alphanumeric
     * @param  string - message name
     * @param  string - data value
     * @return void
     */
    public function validAlphanum($column, $data)
    {
        if (!isValidRegex(ALPHA_NUM, $data)) {
            $customTypeKey = $this->customType ? key($this->customType) : 'noKey';
            $this->loadMessage($column, $customTypeKey, $this->message[$column] ?? null);
        }
    }

    /**
     * Check if the string value is alphabetic
     * @param  string - message name
     * @param  string - data value
     * @return void
     */
    public function validAlpha($column, $data)
    {
        if (!isValidRegex(ALPHA, $data)) {
            $customTypeKey = $this->customType ? key($this->customType) : 'noKey';
            $this->loadMessage($column, $customTypeKey, $this->message[$column] ?? null);
        }
    }

    /**
     * Check if the data value is a digit
     * @param  string - message name
     * @param  string - data value
     * @return void
     */
    public function validDigit($column, $data)
    {
        if (!isValidRegex(DIGITS, $data)) {
            $customTypeKey = $this->customType ? key($this->customType) : 'noKey';
            $this->loadMessage($column, $customTypeKey, $this->message[$column] ?? null);
        }
    }

    /**
     * Check if the data value is a password
     * @param  string - message name
     * @param  string - data value
     * @return void
     */
    public function validPassword($column, $data)
    {
        if (!isValidRegex(PASSWORD, $data)) {
            $customTypeKey = $this->customType ? key($this->customType) : 'noKey';
            $this->loadMessage($column, $customTypeKey, $this->message[$column] ?? null);
        }
    }

    /**
     * Check if the data value is an email
     * @param  string - message name
     * @param  string - data value
     * @return void
     */
    public function validEmail($column, $data)
    {
        if (!filterData($data, "mail")) {
            $customTypeKey = $this->customType ? key($this->customType) : 'noKey';
            $this->loadMessage($column, $customTypeKey, $this->message[$column] ?? null);
        }
    }

    /**
     * Check if the data is a valid base 64 string
     * @param  string - message name
     * @param  string - data value
     * @return void
     */
    public function validImage($column, $data)
    {
        if (!checkBase64Format($data)) {
            $customTypeKey = $this->customType ? key($this->customType) : 'noKey';
            $this->loadMessage($column, $customTypeKey, $this->message[$column] ?? null);
        }
    }

    /**
     * Check if the data length is less than a given value
     * @param  string - message name
     * @param  string - data value
     * @param  string - min length value
     * @return void
     */
    public function validMin($column, $data, $length)
    {
        if (strlen($data) < $length) {
            $customTypeKey = $this->customType ? key($this->customType) : 'noKey';
            $this->loadMessage($column, $customTypeKey, $column.' n\'a pas assez de charactéres (taille minimale : '.$length.')');
        }
    }

    /**
     * Check if the data length is greater than a given value
     * @param  string - message name
     * @param  string - data value
     * @param  string - max length value
     * @return void
     */
    public function validMax($column, $data, $length)
    {
        if (strlen($data) > $length) {
            $customTypeKey = $this->customType ? key($this->customType) : 'noKey';
            $this->loadMessage($column, $customTypeKey, $column.' à trop de charactéres (taille maximale : '.$length.')');
        }
    }

    /**
     * Check if the data have a valid text format
     * @param  string - message name
     * @param  string - data value
     * @return void
     */
    public function validText($column, $data)
    {
        if (!isValidRegex(TEXT, $data)) {
            $customTypeKey = $this->customType ? key($this->customType) : 'noKey';
            $this->loadMessage($column, $customTypeKey, $column.' à trop de charactéres (taille maximale : '.$length.')');
        }
    }

    /**
     * Check if data have a valid name format
     */
    public function validName($column, $data)
    {
        if (!isValidRegex(NAME, $data)) {
            $customTypeKey = $this->customType ? key($this->customType) : 'noKey';
            $this->loadMessage($column, $customTypeKey, $column.' n\'est pas un nom valid');
        }
    }
}
