<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdb731137ae463f7d30fbf0888698ef1b
{
    public static $prefixLengthsPsr4 = array (
        'N' => 
        array (
            'Nrm\\ActivityTracker\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Nrm\\ActivityTracker\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdb731137ae463f7d30fbf0888698ef1b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdb731137ae463f7d30fbf0888698ef1b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitdb731137ae463f7d30fbf0888698ef1b::$classMap;

        }, null, ClassLoader::class);
    }
}
