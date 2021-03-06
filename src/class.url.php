<?php

/**
 * Parsing url for components like scheme, host, port, user, pass, path, file, query, fragment
 *
 * @author KronuS
 */
class url {
    /**
     * @var array
     */
    private $_components;
    /**
     * @var string
     */
    private $_raw_url;

    /**
     * Return raw url
     * @return string
     */
    public function raw() {
        return $this->_raw_url;
    }

    /**
     * Return parsed url components
     * @return array
     */
    public function get_all_components() {
        return $this->_components;
    }

    /**
     * Get url component by its name (like schema, host, port etc)
     * @param string $component
     * @return string|null
     */
    public function get($component) {
        $component = (string)$component;
        return isset($this->_components[$component]) ? $this->_components[$component] : null;
    }

    /**
     * Set url component by its name
     * @param string $component component name
     * @param string $value component value
     */
    public function set($component, $value) {
        $this->_components[(string)$component] = (string)$value;
    }

    public function assembly() {
        $url = '';
        $scheme = $this->get('scheme');
        if ($scheme) {
            $url .= $scheme;
        }
        else {
            $url .= 'http';
        }
        $url .= '://';
        $user = $this->get('user');
        $pass = $this->get('pass');
        if ($user && $pass) {
            $url .= $user.':'.$pass.'@';
        }
        $url .= $this->get('host');
        $port = $this->get('port');
        if ($port) {
            $url .= ':'.$port;
        }
        $path = $this->get('path');
        if ($path) {
            $url .= $path;
        }
        $file = $this->get('file');
        if ($file) {
            $url .= $file;
        }
        $query = $this->get('query');
        if ($query) {
            $url .= '?'.$query;
        }
        $fragment = $this->get('fragment');
        if ($fragment) {
            $url .= '#'.$fragment;
        }
        return $url;
    }

    public function __construct($url) {
        $url = (string)$url;
        $this->_raw_url = $url;
        $url_parsed = parse_url($url);
        if (!isset($url_parsed['host'])) {
            $url_parsed = parse_url('http://'.$url);
        }
        if (!isset($url_parsed['path'])) {
            $url_parsed['path'] = '/';
        }
        $this->_components = $url_parsed;
        $path_info = pathinfo($this->_components['path']);
        if (isset($path_info['extension'])) {
            $file = $path_info['filename'].'.'.$path_info['extension'];
            $this->_components['path'] = str_replace($file, '', $this->_components['path']);
            $this->_components['file'] = $file;
        }
    }

    public static function encode($url) {
        //return str_rot13(base64_encode($url));
        return str_replace('+','|',openssl_encrypt($url, 'cast5-ecb', session_id()));
    }

    public static function decode($string) {
        //return base64_decode(str_rot13($string));
        return openssl_decrypt(str_replace('|','+',$string), 'cast5-ecb', session_id());
    }
}