<?php
/**
 * This file is part of A Local Shortener.
 *
 * A Local Shortener is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * A Local Shortener is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with A Local Shortener. If not, see <http://www.gnu.org/licenses/>.
 */

$short_url = @$_SERVER['REQUEST_URI'];
if (! isset($short_url) || ! is_string($short_url) || ! @preg_match('@^/[a-z0-9]{4}$@', $short_url)) {
    $short_url = null;
}

$config = @parse_ini_file(@dirname(__FILE__).'/shortener.ini', true);
if (isset($config) && is_array($config) && array_key_exists($short_url, $config)) {
    if (array_key_exists('global', $config)) {
        $config = array_merge($config['global'], $config[$short_url]);
    } else {
        $config = $config[$short_url];
    }
} else {
    $config = null;
}

if (is_string($short_url) && is_array($config) && array_key_exists('redirect-to', $config)
                                               && is_string($config['redirect-to'])) {
    echo '<h2>Config found:</h2>';
    echo '<pre>';
    var_dump($config);
    echo '</pre>';

    // TODO Redirect to an bsolute URI as required by HTTP/1.1
    // TODO Validate URL with filter_var()
    $header = 'Location: '.$config['redirect-to'];
    if (array_key_exists('redirect-status', $config) && is_string($config['redirect-status'])
                                                     && @preg_match('@^[1-5]\d{2}$@', $config['redirect-status'])) {
        @header($header, true, intval($config['redirect-status']));
    } else {
        @header($header);
    }
    @exit;
}

// 'redirect-to' not defined in config file

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

    @header("$protocol 404 Not Found");

?><!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL <?php echo is_string($short_url) ? "$short_url " : ''; ?>was not found on this server.</p>
</body></html>
<?php
    @exit;
}
