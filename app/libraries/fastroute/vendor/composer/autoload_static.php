<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5d0d442b27658a17fbabdcd7f830a7a3
{
    public static $files = array (
        '253c157292f75eb38082b5acb06f3f01' => __DIR__ . '/..' . '/nikic/fast-route/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'FastRoute\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'FastRoute\\' => 
        array (
            0 => __DIR__ . '/..' . '/nikic/fast-route/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5d0d442b27658a17fbabdcd7f830a7a3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5d0d442b27658a17fbabdcd7f830a7a3::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
