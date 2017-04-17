<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf9680f8ec8a43027cd3cfc7eb8237557
{
    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Slim' => 
            array (
                0 => __DIR__ . '/..' . '/slim/slim',
            ),
        ),
    );

    public static $classMap = array (
        'Pusher' => __DIR__ . '/..' . '/pusher/pusher-php-server/lib/Pusher.php',
        'PusherException' => __DIR__ . '/..' . '/pusher/pusher-php-server/lib/Pusher.php',
        'PusherInstance' => __DIR__ . '/..' . '/pusher/pusher-php-server/lib/Pusher.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInitf9680f8ec8a43027cd3cfc7eb8237557::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitf9680f8ec8a43027cd3cfc7eb8237557::$classMap;

        }, null, ClassLoader::class);
    }
}