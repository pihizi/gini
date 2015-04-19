<?php

namespace Gini\HTTP;

class Response
{
    public $header = [];
    public $status = null;
    public $body = null;

    public function __construct($data, $status)
    {
        list($header, $body) = explode("\n\n", str_replace("\r", '', $data), 2);

        $this->body = trim($body);

        $header = explode("\n", $header);
        $status = array_shift($header);
        $this->status = $status;

        foreach ($header as $h) {
            list($k, $v) = explode(': ', $h, 2);
            if ($k) {
                $this->header[$k] = $v;
            }
        }
    }

    public function __toString()
    {
        return $this->body;
    }
}
