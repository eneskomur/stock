<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc0ca8e35aa6cd27c106713ffd3e87c9d
{
    public static $files = array (
        'fc73bab8d04e21bcdda37ca319c63800' => __DIR__ . '/..' . '/mikecao/flight/flight/autoload.php',
        '5b7d984aab5ae919d3362ad9588977eb' => __DIR__ . '/..' . '/mikecao/flight/flight/Flight.php',
    );

    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Medoo\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Medoo\\' => 
        array (
            0 => __DIR__ . '/..' . '/catfan/medoo/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'a' => 
        array (
            'app\\' => 
            array (
                0 => __DIR__ . '/../..' . '/',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc0ca8e35aa6cd27c106713ffd3e87c9d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc0ca8e35aa6cd27c106713ffd3e87c9d::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitc0ca8e35aa6cd27c106713ffd3e87c9d::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitc0ca8e35aa6cd27c106713ffd3e87c9d::$classMap;

        }, null, ClassLoader::class);
    }
}