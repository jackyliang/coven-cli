<?php

const CACHE = "/.coven-cache";
const COVEN_API = "http://api.coven.link/api/v1/posts"; 
const NUM_POSTS = 20;

// Colors!!
// I was going to match coven.link's way of representing the website
// type using a background color + white text, but CLI outputs only
// support so few colors =(. Maybe I am wrong, though.
const WHITE = '[1;37m';
const LIGHT_BLUE = '[1;34m';
const YELLOW = '[1;33m';
const RED = '[0;31m';
const BROWN = '[0;33m';
const COLOR_BREAK = '\033[0m';

/**
 * Coven 
 * The only news you need
 * This is my first project done entirely in vi, so please bare with
 * me on the illogically place code. I am trying my best to use this
 * project to learn it!
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
    echo '[ ' . $value->source_data->symbol . ' ] ' .  
    $value->position . '. ' . 
    $value->title . 
    PHP_EOL;
    // TODO: think of a more elegant way to only show twenty results
    if($key === NUM_POSTS) {
        break;
    }
}
/**
 * Map website-type to color-formatted type  
 * TODO: Ok.. It doesn't seem like this works on my Mac Terminal.
 * Investigate how I can get purdy colors on here too
 * @param char    $symbol        A website-type char 
 * @return string $coloredSymbol Colored website-type
 */
function colorize($symbol) {
    $coloredSymbol = ''; 
    switch($symbol) {
        case 'r':
            $coloredSymbol = LIGHT_BLUE . ' r ' . COLOR_BREAK;
            break;
        case 'L':
            $coloredSymbol = BROWN . ' L ' . COLOR_BREAK;
            break;
        case 'Y':
            $coloredSymbol = YELLOW . ' Y ' . COLOR_BREAK;
            break;
        case 'P':
            $coloredSymbol = RED . ' P ' . COLOR_BREAK;
            break;
        default:
            $coloredSymbol = '';
    }
    return $coloredSymbol;
}

