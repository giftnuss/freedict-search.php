<?php

namespace Freedict\Search\Model;

use ArrayAccess;

class Entity implements ArrayAccess
{
    protected $_properties = [];
    protected $_loaded = false;
    protected $_saved = false;
    protected $_saveAction;

    public function __get($name)
    {
        return $this->_properties[$name];
    }

    public function hydrate($row)
    {
         $this->_properties = array_merge ($this->_properties, $row);
         $this->_loaded = true;
         $this->_saved = true;
    }

    public function loaded()
    {
        return $this->_loaded;
    }

    public function saved()
    {
        return $this->_saved;
    }

    public function saveAction(callable $action)
    {
        $this->_saveAction = $action;
    }

    public function save()
    {
        $action = $this->_saveAction;
        $action($this);
        $this->_saved = true;
    }

    public function getData(array $keys = null)
    {
        if($keys === null) {
            return $this->_properties;
        }
        else {
            return array_intersect_key($this->_properties, array_flip($keys));
        }
    }

    /**
     * @implements ArrayAccess
     */
    public function offsetExists ( $offset )
    {
        return array_key_exists($offset, $this->_properties);
    }

    /**
     * Returning a reference is important here.
     * @implements ArrayAccess
     */
    public function &offsetGet ( $offset )
    {
        return $this->_properties[$offset];
    }

    /**
     * @implements ArrayAccess
     */
    public function offsetSet ( $offset , $value )
    {
        $this->_properties[$offset] = $value;
        $this->_saved = false;
    }

    /**
     * @implements ArrayAccess
     */
    public function offsetUnset ( $offset )
    {
        unset($this->_properties[$offset]);
    }
}
