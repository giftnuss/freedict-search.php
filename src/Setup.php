<?php

namespace Freedict\Search;

use Siox\Db\Setup as DbSetup;

class Setup
{
    protected $db;
    protected $schema;

    public function __construct($db)
    {
        $this->db = $db;
        $this->schema = new Schema();
    }

    public function init()
    {
        $tables = $this->db->info()->listTableNames();
        if (count($tables) == 0) {
            $this->loadSchema();
        }
        else {
            (new DbSetup($this->db))->update($this->schema);
        }
        // dev - update
        $this->schema->loadCoreData($this->db);
    }

    protected function loadSchema()
    {
        $setup = new DbSetup($this->db);
        $setup->initSchema($this->schema);
        $this->schema->loadCoreData($this->db);
    }

    public function getModel()
    {
        return new Model($this->db, $this->schema);
    }

    public function getTables()
    {
        return $this->schema->getTables();
    }

    public function getTable($name)
    {
        return $this->schema->getTable($name);
    }
}
