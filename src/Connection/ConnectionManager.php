<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 24/08/2017
 * Time: 17:41
 */

namespace FiremonPHP\Database\Connection;


class ConnectionManager
{
    /**
     * @var \FiremonPHP\Database\Database[]
     */
    private static $connections = [];

    /**
     * @param string $name
     * @param array $configsKey
     */
    public static function config(string $name, array $configsKey)
    {
        self::$connections[$name] = new \FiremonPHP\Database\Database(
            self::getConnection($configsKey)
        );
    }

    /**
     * @param string $name
     * @return \FiremonPHP\Database\Database
     */
    public static function get(string $name)
    {
        return self::$connections[$name];
    }

    /**
     * @param array $config
     * @return Connection
     * @throws \Exception
     */
    private static function getConnection(array $config)
    {
        if (
            count($config) >= 5 ||
            count($config) === 2 &&
            isset($config['url']) &&
            isset($config['database'])
        ) {
            return new Connection($config['url'], $config['database'], self::setByArrayConfig($config));
        } else {
            throw new \Exception('This setting is not correct!');
        }
    }

    private static function setByArrayConfig(array $config)
    {
        return [];
    }
}