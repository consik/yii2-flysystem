<?php
/**
 * @link https://github.com/consik/yii2-flysystem
 * @category yii2-extension
 * @package consik\yii2flysystem
 *
 * @author Sergey Poltaranin <consigliere.kz@gmail.com>
 * @copyright Copyright (c) 2017
 */
namespace consik\yii2flysystem;

use yii\base\InvalidCallException;
use yii\base\Object;

/**
 * Class Filesystem
 * Class provides dynamically creating \League\Flysystem\Filesystem instance with any available adapter
 * Example of using:
 * Configure filesystem component in your application:
 * config/web.php:
 * ...
 * 'components' => [
 *  ...
 *  'localFiles' => [
 *      'class' => \app\consik\yii2flysystem\Filesystem::class,
 *      'adapter' => \League\Flysystem\Adapter\Local::class,
 *      'adapterParams' => [
 *          __DIR__ //first argument for Local adapter constructor is root dir
 *      ]
 *      //'config' => [], //use that for setting \League\Flysystem\Filesystem::$config
 *      //'plugins' => [ \League\Flysystem\Plugin\ListFiles::class ] //describe plugins here if you need
 *  ]
 *  ...
 * ]
 * Than use it like any Yii component. For example:
 * \Yii::$app->localFiles->listContents();
 *
 * @see \League\Flysystem\Filesystem
 * @method bool has($path);
 * @method string|false read($path);
 * @method resource|false readStream($path);
 * @method array listContents($directory = '', $recursive = false);
 * @method array|false getMetadata($path);
 * @method int|false getSize($path);
 * @method string|false getMimetype($path);
 * @method string|false getTimestamp($path);
 * @method string|false getVisibility($path);
 * @method bool write($path, $contents, array $config = []);
 * @method bool writeStream($path, $resource, array $config = []);
 * @method bool update($path, $contents, array $config = []);
 * @method bool updateStream($path, $resource, array $config = []);
 * @method bool rename($path, $newpath);
 * @method bool copy($path, $newpath);
 * @method bool delete($path);
 * @method bool deleteDir($dirname);
 * @method bool createDir($dirname, array $config = []);
 * @method setVisibility($path, $visibility);
 * @method bool put($path, $contents, array $config = []);
 * @method bool putStream($path, $resource, array $config = []);
 * @method string|false readAndDelete($path);
 * @method \League\Flysystem\Handler get($path, \League\Flysystem\Handler $handler = null);
 * @method \League\Flysystem\AdapterInterface getAdapter()
 * @method \League\Flysystem\Config getConfig()
 * @method \League\Flysystem\Filesystem addPlugin(\League\Flysystem\PluginInterface $plugin)
 */
class Filesystem extends Object
{
    /** @var array Filesystem adapter config */
    public $adapter = '';
    /**
     * @var array Filesystem adapter constructor args
     * If adapter constructor required class object as argument, use this definition:
     * [
     *  'adapter' => 'path\to\adapter\Class',
     *  'adapterParams' => [
     *      'simpleStringParam',
     *      ['simpleArrayParam'],
     *      [
     *          'class' => 'path\to\required\object\Class', //it will create object of this class for adapter constructor
     *          //'params' => [ //constructor params for class ]
     *      ]
     *  ]
     * ]
     * */
    public $adapterParams = [];
    /** @var array|null Filesystem config */
    public $config;
    /**
     * @var array Plugins list for Filesystem object
     * Example:
     * [
     *  'plugins' => [
     *      'path\to\plugin\without\params\Class',
     *      [
     *          'class' => 'path\to\plugin\Class'
     *          'params' => [ //constructor params for plugin class ]
     *      ]
     *  ]
     * ]
     */
    public $plugins = [];
    /** @var \League\Flysystem\Filesystem */
    protected $instance;

    /**
     * @inheritdoc
     */
    public function init()
    {
        /**
         * Initializing adapter
         */
        foreach ($this->adapterParams as $key => $adapterParam) {
            if (is_array($adapterParam) && array_key_exists('class', $adapterParam)) {
                $this->adapterParams[$key] = $this->initObject($adapterParam);
            }
        }
        $adapter = $this->createObject($this->adapter, $this->adapterParams);

        if (!$adapter) {
            throw new InvalidCallException('Can\'t initialize filesystem adapter');
        }

        /**
         * Creating Filesystem instance
         */
        $this->instance = new \League\Flysystem\Filesystem($adapter, $this->config);

        if (!$this->instance) {
            throw new InvalidCallException('Can\'t initialize Filesystem instance');
        }

        /**
         * Adding plugins to filesystem
         */
        foreach ($this->plugins as $plugin) {
            $this->instance->addPlugin($this->initObject($plugin));
        }
    }

    /**
     * All magic is here :)
     * @inheritdoc
     */
    public function __call($name, $params)
    {
        return call_user_func_array([$this->instance, $name], $params);
    }


    /**
     * Creates object instance
     * @param array|string $object
     * @return mixed
     * @throws InvalidCallException
     */
    protected function initObject($object)
    {
        if (is_string($object)) {
            return $this->createObject($object);
        } elseif (is_array($object)) {
            return $this->createObject($object['class'], array_key_exists('params', $object) ? $object['params'] : []);
        }
        throw new InvalidCallException('Invalid argument for creating object');
    }

    /**
     * Returns instance of $class
     * @param string $class
     * @param array $params Constructor args for $class
     * @return mixed
     */
    protected function createObject($class, array $params = [])
    {
        return new $class(...$params);
    }
}