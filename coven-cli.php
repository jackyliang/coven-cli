<?php

const CACHE = "/.coven_cache";
const COVEN_API = "http://api.coven.link/api/v1/posts"; 

/**
 * Coven CLI 
 */
 
echo '---------------------------------------' . PHP_EOL;
echo '     Coven - The only news you need    ' . PHP_EOL;
echo '---------------------------------------' . PHP_EOL;

// Check cached JSON
if(!file_exists(dirname(__FILE__) . CACHE)) {
    // Get the file from api.coven.link if there's no cached version 
    $jsonRaw = file_get_contents(COVEN_API);
    file_put_contents(
        dirname(__FILE__) . CACHE, 
        $jsonRaw
    );
} else {
    // Otherwise, use the cached version
    $jsonRaw = file_get_contents(
        dirname(__FILE__) . CACHE
    );
}

// Convert string to json object
$jsonObject = json_decode($jsonRaw);

foreach($jsonObject as $key => $value) {
    echo '[ ' . $value->source_data->symbol . ' ] ' .  $value->position . '. ' . $value->title . PHP_EOL;
    if($key === 20) {
        break;
    }
}
