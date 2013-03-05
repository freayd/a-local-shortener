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

$config = @json_decode(@file_get_contents(@dirname(__FILE__).'/shortener-config.json'), true);
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
                                               && is_string($config['redirect-to'])
                                               && @preg_match('@^/@', $config['redirect-to'])) {
    // Set cookies
    if (array_key_exists('set-cookies', $config) && is_array($config['set-cookies'])) {
        foreach ($config['set-cookies'] as $name => $value) {
            setcookie($name, $value);
        }
    }

    // Redirect
    $uri = $config['redirect-to'];
    $host = @$_SERVER['HTTP_HOST'];
    if (isset($host) && is_string($host)) {
        $https = @$_SERVER['HTTPS'];
        $protocol = isset($https) && is_string($https)
                                  && $https != ''
                                  && $https != 'off'
                                  ? 'https'
                                  : 'http';
        if (@parse_url("$protocol://$host$uri") !== false) {
            $uri = "$protocol://$host$uri";
        }
    }
    $header = "Location: $uri";
    if (array_key_exists('redirect-status', $config) && is_int($config['redirect-status'])
                                                     && $config['redirect-status'] >= 100
                                                     && $config['redirect-status'] <= 599) {
        @header($header, true, $config['redirect-status']);
    } else {
        @header($header);
    }

    @exit;
}

// 'redirect-to' not defined in config file

if (@file_exists(@dirname(__FILE__).'/index.php')) {
    unset($short_url, $config);
    @require_once @dirname(__FILE__).'/index.php';
} else if (@file_exists('index.php')) {
    unset($short_url, $config);
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
