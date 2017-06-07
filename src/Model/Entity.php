<?php

namespace Freedict\Search\Model;

class Entity
{
    protected $_properties = [];
    protected $_loaded = false;
    protected $_saved = false;

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

}
