# Yii2 Flysystem component
[![Latest Stable Version](https://poser.pugx.org/consik/yii2-flysystem/v/stable)](https://packagist.org/packages/consik/yii2-flysystem)
[![Total Downloads](https://poser.pugx.org/consik/yii2-flysystem/downloads)](https://packagist.org/packages/consik/yii2-flysystem)
[![License](https://poser.pugx.org/consik/yii2-flysystem/license)](https://packagist.org/packages/consik/yii2-flysystem)

Yii2 component for working with league/flysystem
Based on [flysystem](https://github.com/thephpleague/flysystem)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require consik/yii2-flysystem
```

or add

```json
"consik/yii2-flysystem": "^1.0"
```

## Using

Define component in yii config for your filesystem:
```php
//config/web.php for simple application
'components' => [
    ... 
    'localFiles' => [
        'class' => consik\yii2flysystem\Filesystem::class,
        'adapter' => \League\Flysystem\Adapter\Local::class,
        'adapterParams' => [
            __DIR__ //first argument for Local adapter constructor is root dir
        ],
        'plugins' => [
            \League\Flysystem\Plugin\ListFiles::class
        ]
    ]
    ...
]
```
Use [Filesystem](https://github.com/thephpleague/flysystem/blob/master/src/Filesystem.php) methods via this component:
```php
\Yii::$app->localFiles->listFiles();
...etc
```

See [DocBlock](/Filesystem.php) for more info about configuration params.

### FTP source example:
```php
'components' => [
    ... 
    'ftp' => [
        'class' => consik\yii2flysystem\Filesystem::class,
        'adapter' => \League\Flysystem\Adapter\Ftp::class,
        'adapterParams' => [
            [ //for FTP constructor first param is configuration array
                'host' => 'your.ftp.host',
                'username' => 'username',
                'password' => 'password'
            ]
        ],
        'plugins' => [
            \League\Flysystem\Plugin\ListFiles::class
        ]
    ]
    ...
]
```

List of available adapters or plugins see on official flysystem page: [https://github.com/thephpleague/flysystem](https://github.com/thephpleague/flysystem)

That's all! Enjoy! 