<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc8c058789fe19be9e5d5c6ee03ed1348
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc8c058789fe19be9e5d5c6ee03ed1348::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc8c058789fe19be9e5d5c6ee03ed1348::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc8c058789fe19be9e5d5c6ee03ed1348::$classMap;

        }, null, ClassLoader::class);
    }
}
