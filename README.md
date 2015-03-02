# Coven CLI
All the news a programmer needs in your command line!

### Introduction 

Coven CLI is a command line version of the incredible programmer news
aggregator [coven.link](coven.link) which aggregates news from the
Hacker News, /r/Programming, Lobsters, and Product Hunt.

Coven CLI is super fast, using local caching to get the busy you the
latest programming news. 

### Installation

    git clone https://github.com/jackyliang/coven-cli.git
    cd coven-cli
    alias coven='php coven-cli.php'

### Usage

Get the latest Coven headliners

    $ coven

Refresh your feed

    $ coven refresh

Open a particular post in your browser

    $ coven open [index]
