<?php
# Simple class to fetch a HTTP URL. Supports "Location:"-redirections. Useful for servers with allow_url_fopen=false. Works with SSL-secured hosts.
# From http://www.php.net/manual/en/function.fopen.php#58099
# Updated by TJ <linux@tjworld.net> to support cookies
# usage:
# $r = new HTTPRequest('http://www.example.com', $cookie_array);
# echo $r->DownloadToString();

class HTTPRequest
{
    var $_fp;        // HTTP socket
    var $_url;        // full URL
    var $_host;        // HTTP host
    var $_protocol;    // protocol (HTTP/HTTPS)
    var $_uri;        // request URI
    var $_port;        // port
    var $_cookies = array();    // array of cookies
    var $_errstr = '';
    var $_errno = 0;

    // scan url
    function _scan_url()
    {
        $req = $this->_url;

        $pos = strpos($req, '://');
        $this->_protocol = strtolower(substr($req, 0, $pos));

        $req = substr($req, $pos+3);
        $pos = strpos($req, '/');
        if($pos === false)
            $pos = strlen($req);
        $host = substr($req, 0, $pos);

        if(strpos($host, ':') !== false)
        {
            list($this->_host, $this->_port) = explode(':', $host);
        }
        else
        {
            $this->_host = $host;
            $this->_port = ($this->_protocol == 'https') ? 443 : 80;
        }

        $this->_uri = substr($req, $pos);
        if($this->_uri == '')
            $this->_uri = '/';
    }

    // constructor
    function HTTPRequest($url, $cookies)
    {
        $this->_url = $url;
        $this->_scan_url();
        $this->_cookies = $cookies;
    }

    // download URL to string
    function DownloadToString()
    {
        $crlf = "\r\n";

        // generate request
        $req = 'GET ' . $this->_uri . ' HTTP/1.0' . $crlf
            .  'Host: ' . $this->_host . $crlf;

        // add cookies if any exist
        if(count($this->_cookies)) {
            $req .= "Cookie: ";
            foreach($this->_cookies as $key => $value) {
               $req .= $key . "=" . $value . "; ";
            }
            $req .= $crlf;
        }
        $req .=  $crlf;

        // fetch
        try {
            $this->_fp = fsockopen(($this->_protocol == 'https' ? 'ssl://' : '') . $this->_host,
                                   $this->_port, $this->_errorno, $this->_errstr);
            if($this->_fp) {
                fwrite($this->_fp, $req);
                $response = '';
                while(is_resource($this->_fp) && $this->_fp && !feof($this->_fp))
                    $response .= fread($this->_fp, 1024);
                fclose($this->_fp);

                // split header and body
                $pos = strpos($response, $crlf . $crlf);
                if($pos === false)
                    return($response);
                $header = substr($response, 0, $pos);
                $body = substr($response, $pos + 2 * strlen($crlf));

                // parse headers
                $headers = array();
                $lines = explode($crlf, $header);
                foreach($lines as $line) {
                    if(($pos = strpos($line, ':')) !== false) {
                        $key = strtolower(trim(substr($line, 0, $pos)));
                        $value = trim(substr($line, $pos+1));
                        $headers[$key] = $value;
                        if(strcmp($key, "set-cookie") == 0) {
                            if(($pos = strpos($value, '=')) !== false) {
                                $key = trim(substr($value, 0, $pos));
                                $value = trim(substr($value, $pos+1));
                                $this->_cookies[$key] = $value;
                            }
                        }
                    }
                }

                // redirection?
                if(isset($headers['location']))
                {
                    $http = new HTTPRequest($headers['location'], $this->_cookies);
                    return($http->DownloadToString());
                }
                else
                {
                    return($body);
                }
            }
            else {
                return(FALSE);
            }
        } catch (Exception $exception) {
            $this->_errstr = $exception->getMessage();
            $this->_errno  = $exception->getCode();
            return(FALSE);
        }
    }
}
?>
