<?php
define('SHFORM', 'SHForm');

$root = __DIR__;
$separator = DIRECTORY_SEPARATOR;

$path = array_map('realpath', array(
    'root' => $root,
    'app' => $root . $separator . 'app',
));

$path = array_merge($path, array(
    'separator' => $separator,
));

spl_autoload_register(function ($class) use ($path) {
    if (strpos($class, SHFORM) !== false) {
        $file = str_replace(array('\\', '_'), '/', $class);

        include $path['app'].$path['separator'].'src'.$path['separator'].$file.'.php';
    }
});