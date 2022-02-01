<?php

require_once './FacturxReader.php';

$xml = file_get_contents('./sample.xml');

$reader = new FacturxReader($xml);

var_dump($reader->getDocumentLines());