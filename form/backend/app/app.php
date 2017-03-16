<?php
SHForm\AliasLoader::getInstance(array(
    'Arr' => 'SHForm\\Arr',
    'Option' => 'SHForm\\Option',
    'CheckInput' => 'SHForm\\CheckInput',
    'PrepareInput' => 'SHForm\\PrepareInput'
))->register();

$config = array_map('realpath', array(
    'path.root' => __DIR__ . '/..',
    'path.app' => __DIR__,
    'path.vendor' => __DIR__ . DIRECTORY_SEPARATOR . 'vendor'
));

$configPath = $config['path.root'] . '/config.php';

if (is_file($configPath)) {
    $configInclude = include $configPath;

    $config = array_merge($configInclude, $config);
}

foreach ($config as $key => $val) {
    Option::set($key, $val);
}

include 'function.php';