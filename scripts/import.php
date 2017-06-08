<?php

$app = require __DIR__ . '/../app/src/app.php';
$c = $app->getContainer();

$settings = $c->get('settings');
$model = $c->get('model');
$db = $c->get('db');

$base = $settings['freedict']['rootdir'];
$dicts = $settings['freedict']['dictionaries'];

print_r($dicts);

foreach($dicts as $dict) {
    list($src,$tgt) = explode("-",$dict);

    $filename = join("/",[$base,$dict,"{$dict}.tei"]);
    if(!file_exists($filename)) {
        error_log("No file $filename?");
        continue;
    }

    $db->beginTransaction();
    $xmlObject = new XMLReader();
    $xmlObject->open($filename);

    $constructor = $model->getEntryFactory($src,$tgt);
    $translation = $model->getTranslationFactory();

    while($xmlObject->read()){
        if($xmlObject->name == 'entry'){
            $entry = $constructor();
            $inner = new XMLReader();
            $inner->xml($xmlObject->readOuterXml());
            while($inner->read()) {
                if($inner->name == 'orth') {
                    $inner->read();
                    $word = trim($inner->value);
                    if(strlen($word) > 0) {
                        $entry['headword']= $word;
                    }
                }
                if($inner->name == 'quote') {
                    $inner->read();
                    $trans = trim($inner->value);
                    if(strlen($trans) > 0) {
                        $object = $translation();
                        $object['translation'] = $trans;
                        array_push($entry['translation'], $object);
                    }
                }
            }
            if($entry['headword'] !== null) {
                $entry->save();
            }
        }
    }

    $xmlObject->close();
    $db->commit();
}
