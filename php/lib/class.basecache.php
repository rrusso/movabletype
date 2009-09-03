<?php
# Movable Type (r) Open Source (C) 2001-2009 Six Apart, Ltd.
# This program is distributed under the terms of the
# GNU General Public License, version 2.
#
# $Id: class.basecache.php 106007 2009-07-01 11:33:43Z ytakayama $
abstract class BaseCache {
    protected $_ttl = 0;

    public function __construct($ttl = 0) {
        $this->_ttl = $ttl;
    }

    abstract public function get ($key, $ttl = null);
    abstract public function get_multi ($keys, $ttl = null);
    abstract public function delete ($key);
    abstract public function add ($key, $val, $ttl = null);
    abstract public function replace ($key, $val, $ttl = null);
    abstract public function set ($key, $val, $ttl = null);
    abstract public function flush_all();
}

abstract class CacheProviderFactory {
    private static $_provider = array(
        'session' => 'cachesession',
        'memcached' => 'cachememcached',
        'memory' => 'cachememory'
        );

    public static function get_provider($type = 'memory', $ttl = 0) {
        if (empty($type))
            return null;

        if (!array_key_exists($type, self::$_provider)) {
            require_once('class.exception.php');
            throw new MTException("Can't load cache provider. (" . $type . ")");
        }

        $name = CacheProviderFactory::$_provider[$type];
        require_once("class.$name.php");
        $provider = new $name($ttl);
        return $provider;
    }

    public static function add_provider($type, $class) {
        if (empty($type) or empty($class))
            return false;

        CacheProviderFactory::$_provider[$type] = $class;
        return true;
    }
}