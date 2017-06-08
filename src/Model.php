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
        $sql = $this->sql;
        $orm = $this->orm;
        $save = function ($entity) use ($sql,$orm,$source,$target) {
            $orm->query('headword')->if_not(
                ['headword' => $entity['headword'], 'language' => $source->id],
                function () use ($sql,$entity,$source) {
                    $sql->insert('headword', ['headword' => $entity['headword'], 'language' => $source->id]);
                    $entity['id'] = $sql->lastInsertId();
                }, function ($row) use ($entity) {
                    $entity['id'] = $row['id'];
                });
            foreach($entity['translation'] as $translation) {
                $translation['headword'] = $entity->id;
                $translation['language'] = $target->id;
                $translation->save();
            }
            echo $entity['headword'],"\n";
        };

        return function () use ($save) {
            $entity = new Model\Entry();
            $entity->saveAction($save);
            $entity['translation'] = [];
            return $entity;
        };
    }

    public function getTranslationFactory()
    {
        $sql = $this->sql;
        $orm = $this->orm;

        $save = function ($entity) use ($orm,$sql) {
            $orm->query('translation')->if_not(
                $entity->getData(),
                function () use ($sql,$entity) {
                    $sql->insert('translation',$entity->getData());
                    $entity['id'] = $sql->lastInsertId();
                }, function ($row) use ($entity) {
                    $entity['id'] = $row['id'];
                });
        };

        return function () use ($save) {
            $entity = new Model\Translation();
            $entity->saveAction($save);
            return $entity;
        };
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

    public function search($term)
    {
        $select = $this->sql->select('headword');
        $select->join('translation')->as('t')->on('headword.id = t.headword');
        $select->where('headword.headword LIKE :term',['term' => "%$term%"]);

        $this->sql->doQuery($select,$stmt);
        $rows = [];
        while($row = $stmt->fetch(\PDO::FETCH_NAMED)) {
            $id = $row['id'][0];
            if(isset($rows[$id])) {
                $rows[$id]['translation'][] = $row['translation'];
            }
            else {
                $rows[$id] = ['headword' => $row['headword'][0],
                              'translation' => [$row['translation']]];
            }
        }
        return $rows;
    }
}
