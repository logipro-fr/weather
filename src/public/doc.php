<?php

// @phpunit-ignore-coverage
header('Access-Control-Allow-Origin: *');

echo file_get_contents(__DIR__ . "/doc.yaml");
