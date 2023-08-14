<?php

require_once("search.php");

$SEARCH_ID = '1:4';

$json = loadJSON();
checkKeyDocument($json);
search( $SEARCH_ID, [$json['document']]);


/**
 * @return array
 */
function loadJSON(): array
{
    return json_decode(file_get_contents("data.json"), true) ?? [];
}

/**
 * @param array $json
 * @return void
 */
function checkKeyDocument(array $json): void
{
    if (!isset($json['document'])) {
        print_r('Not found key document');
        exit();
    }
}