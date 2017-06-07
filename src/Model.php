<?php

namespace Freedict\Search;

use Gmo\Iso639\Languages;

class Model
{
    protected $db;
    protected $sql;
    protected $schema;
    public $orm;

    public function __construct($db, $schema)
    {
        $this->db = $db;
        $this->sql = $db->sql();
        $this->schema = $schema;
        $this->orm = $db->orm($schema);
    }
    /**
     * Returns a function which is able to create a dictionary entry.
     * For performance reason the language preloaded.
     */
    public function getEntryFactory($src,$tgt)
    {
        $source = $this->getLanguage($src);
        $target = $this->getLanguage($tgt);
    }

    public function getLanguage($code)
    {
        $lang = new Model\Language();
        $this->orm->query('language')->search(['language' => $code],
            function ($row) use ($lang) { $lang->hydrate($row);
        });
        if(!$lang->loaded()) {
            $languages = new Languages();
            $language = $languages->findByCode3($code);
            $this->sql->insert('language',['language' => $code]);
            return $this->getLanguage($code);
        }
        return $lang;
    }

}
