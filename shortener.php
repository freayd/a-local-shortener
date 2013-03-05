<?php

$short_url = @$_SERVER['REQUEST_URI'];
if (! isset($short_url) || ! is_string($short_url) || ! @preg_match('@^/[a-z0-9]{4}$@', $short_url)) {
    $short_url = null;
}

$config = @parse_ini_file(@dirname(__FILE__).'/shortener.ini', true);
if (isset($config) && is_array($config) && array_key_exists($short_url, $config)) {
    $config = $config[$short_url];
} else {
    $config = null;
}

if (! is_null($short_url) && ! is_null($config)) {
    // Config found for current URL

    echo '<h2>Config found:</h2>';
    echo '<pre>';
    var_dump($config);
    echo '</pre>';

} else {
    // URL not defined in config file

    if (@file_exists(@dirname(__FILE__).'/index.php')) {
        unset($short_url);
        unset($config);
        @require_once @dirname(__FILE__).'/index.php';
    } else if (@file_exists('index.php')) {
        unset($short_url);
        unset($config);
        @require_once 'index.php';
    } else {
        // No index found, display 404 message

        $protocol = @$_SERVER['SERVER_PROTOCOL'];
        if (! isset($protocol) || ! is_string($protocol) || ! @preg_match('@^HTTPS?/\d(\.\d)?$@i', $protocol)) {
            $protocol = 'HTTP/1.0';
        }

        header("$protocol 404 Not Found");

?><!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL <?php echo isset($short_url) && is_string($short_url) ? "$short_url " : ''; ?>was not found on this server.</p>
</body></html>
<?php
        @exit;
    }

}
