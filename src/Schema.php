<?php

namespace Freedict\Search;

use Siox\Db\Schema as Base;

class Schema extends Base
{
    protected function _construct()
    {
        $this->name = 'freedict_search';

        $this->type('id')->int->size(14);
        $this->type('iso639_3')->char->size(3);
        $this->type('word')->varchar->size(128);

        $this->table('language')
            ->column->id('id')->iso639_3('language')
            ->constraint->pk('id')->unique('language');

        $this->table('headword')
            ->column->id('id')->word('headword')->id('language')
            ->constraint->pk('id')->unique('headword','language');

        $this->table('translation')
            ->column->id('id')
                    ->id('headword')
                    ->word('translation')
                    ->id('language')
            ->constraint->pk('id')->unique('headword','translation','language');
    }

    public function loadCoreData($db)
    {
       // $model = new Model($db, $this);
        $db->beginTransaction();

        $db->commit();
    }
}
