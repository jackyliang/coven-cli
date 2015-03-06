<?php

require_once("lib/Readability.php");
header('Content-Type: text/plain; charset=utf-8');

/**
 * Coven - The Only News You Need
 * This is my first project done entirely in vi, so please bear with
 * me on the illogically placed code. I am trying my best to use this
 * project to learn it!
 */

/**
 * TODO
 * 1. 'coven refresh' command         --- DONE
 * 2. 'coven open [index]' command    --- DONE
 * 3. 'coven help' command            ---
 * 4. Make colorize() work            ---
 * 5. xdg-open for Linux distros      ---
 * 6. Better ways to show n-results   --- 
 * 7. last_refreshed in JSON object   ---
 */

// Constants
const CACHE = "/.coven-cache";
const COVEN_API = "http://api.coven.link/api/v1/posts"; 
const NUM_POSTS = 30;

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

 
// Print fancy text
echo '---------------------------------------' . PHP_EOL;
echo '     Coven - The only news you need    ' . PHP_EOL;
echo '---------------------------------------' . PHP_EOL;

$jsonRaw = array();

// Get data depending on whether cache exists or not 
if(
    !file_exists(dirname(__FILE__) . CACHE) ||
        $argv[1] === 'refresh'
) {
    echo "Getting you the latest news from Coven!" . PHP_EOL;
    $jsonRaw = getOnlineData(); 
} else {
    $jsonRaw = getLocalData();
}

// Convert string to json object
$jsonObject = json_decode($jsonRaw);

// Open command
if($argv[1] === 'open' || $argv[1] === 'opena') {
    // If the second argv isn't empty and is an integer
    if(
        !strlen($argv[2]) == 0 &&
            is_numeric($argv[2])
    ) {
        // Assign the user
        $index = $argv[2] - 1;
        $url = $jsonObject[$index]->url;

        if ($argv[1] === 'open') {
            // Escape the input and open the URL in the browser
            exec('open ' . escapeshellarg($url));
            echo "Opening '" . $jsonObject[$index]->title . "'" . PHP_EOL;
            exit;
        } else if ($argv[1] === 'opena') {
            echo "Loading '" . $jsonObject[$index]->title . "'" . PHP_EOL;
            getArticle($url); 
            exit;
        }
    } else {
        echo PHP_EOL . 
            '[Error] Please enter a valid post # for me to open!' . 
            PHP_EOL;
        exit;
    }
}

// Print the posts
printPosts($jsonObject);

// Release from memory
unset($jsonObject);
unset($jsonRaw);

/**
 * Print the data to the console 
 * @param  JSON The Coven JSON   
 */
function printPosts($jsonObjectInput) {
    foreach($jsonObjectInput as $key => $value) {
        echo '[ ' . $value->source_data->symbol . ' ]' .  
            '[ ⇡' . $value->comment_count . ' ] ' .
            ($key + 1) . '. ' . 
            $value->title . 
            PHP_EOL;
        // TODO: think of a more elegant way to only show n-results
        if($key === NUM_POSTS) {
            break;
        }
    }
}

/**
 * Get the data from the Coven API 
 * @return JSON array
 */
function getOnlineData() {
    // TODO: Add in a key called `last_refreshed:YYYY-mm-dd HH:MM:ss` 
    // Get the file from api.coven.link if there's no cached version 
    $jsonRaw = file_get_contents(COVEN_API);
    file_put_contents(
        dirname(__FILE__) . CACHE, 
        $jsonRaw
    );
    return $jsonRaw;
}

/**
 * Get the data from locally cached version 
 * @return JSON array
 */
function getLocalData() {
    // Otherwise, use the cached version
    $jsonRaw = file_get_contents(
        dirname(__FILE__) . CACHE
    );
    return $jsonRaw;
}


/**
 * Get the article from URL using Readability-PHP 
 * @param mixed $url 
 */
function getArticle($url) {
    $html = file_get_contents($url); 

    // If we've got Tidy, let's clean up input.
    // This step is highly recommended - PHP's default HTML parser
    // often doesn't do a great job and results in strange output.
    if (function_exists('tidy_parse_string')) {
        echo "TIDY EXISTS!!!";
        $tidy = tidy_parse_string($html, array(), 'UTF8');
        $tidy->cleanRepair();
        $html = $tidy->value;
    }

    // give it to Readability
    $readability = new Readability($html, $url);
    // print debug output? 
    // useful to compare against Arc90's original JS version - 
    // simply click the bookmarklet with FireBug's console window open
    $readability->debug = false;
    // convert links to footnotes?
    $readability->convertLinksToFootnotes = true;
    // process it
    $result = $readability->init();
    // does it look like we found what we wanted?
    if ($result) {

        echo "== Title =====================================\n";
        echo $readability->getTitle()->textContent, "\n\n";
        echo "== Body ======================================\n";
        $content = $readability->getContent()->innerHTML;
        // if we've got Tidy, let's clean it up for output
        if(function_exists('tidy_parse_string')) {
            $tidy = tidy_parse_string(
                    $content, 
                    array('indent'=>true, 'show-body-only' => true), 
                    'UTF8'
                );
            $tidy->cleanRepair();
            $content = $tidy->value;
        }
        echo $content;
    } else {
        echo 'Looks like we couldn\'t find the content. :(';
    }
}

/**
 * Map website-type to color-formatted type  
 * TODO: Ok.. It doesn't seem like this works on my Mac Terminal.
 * Investigate how I can get purdy colors on here too
 * @param char    $symbol        A website-type char 
 * @return string $coloredSymbol A colored website-type char
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
