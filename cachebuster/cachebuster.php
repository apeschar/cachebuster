<?php
/*
Plugin Name: Cachebuster
Description: Append a file's modification time to its URL and prevent stale caches.
Version: 1.0
Author: Albert Peschar
Author URI: https://kiboit.com
License: GPLv2 or later
*/

function cabu_src($src, $handle) {
    static $site_url;

    if(!isset($site_url))
        $site_url = cabu_normalize_url(get_site_url());

    $file = cabu_normalize_url($src);

    if($site_url && strpos($file, $site_url) === 0)
        $file = substr($file, strlen($site_url));
    elseif(strpos($file, '//') === 0)
        return $src;

    if(($pos = strpos($file, '?')) !== false)
        $file = substr($file, 0, $pos);

    $file = ltrim($file, '/');

    if($file == '')
        return $src;

    $path = ABSPATH . '/' . $file;

    $mtime = @filemtime($path);

    if(!$mtime)
        return $src;

    $query = '?=' . $mtime;

    if(($pos = strpos($src, '?')) !== false)
        $src = substr($src, 0, $pos);

    return $src . $query;
}

function cabu_normalize_url($url) {
    return preg_replace('|^https?://|i', '//', $url);
}

add_filter('style_loader_src',  'cabu_src', 10, 2);
add_filter('script_loader_src', 'cabu_src', 10, 2);
