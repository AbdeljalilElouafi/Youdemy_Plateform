<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit36436288c3a5ee797aa3a6d92ee2f437
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'src\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'src\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit36436288c3a5ee797aa3a6d92ee2f437::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit36436288c3a5ee797aa3a6d92ee2f437::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit36436288c3a5ee797aa3a6d92ee2f437::$classMap;

        }, null, ClassLoader::class);
    }
}