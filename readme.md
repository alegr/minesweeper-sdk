
# minesweeper-API SDK

SDK for interacting with MineSweeper API. 

This library was built in an api-agnostic fashion, allowing the API to grow in features and incorporate new namespaces or edit existing ones without having to update the current repository, thus reducing its maintainance to almost none.

## Installation

    git clone https://github.com/alegr/minesweeper-sdk.git

## Usage

You need to create a new MineSweeperSDK instance with the following configuration parameters

    <?php 
    include('MineSweeperSDK.php');
    $MineSweeperSDK = new \MineSweeperSDK([
        'url' => 'http://local.dev.minesweeper/api',
    ]);

Now you are ready to make API calls

## Available methods


### List all games

    $response = $MineSweeperSDK->games();

### Create new game

    $response = $MineSweeperSDK->games->new([
        'columns'   =>  2,
        'rows'      =>  2,
        'mines'     =>  1,
    ]);

### Retrieve specific game

    $response = $MineSweeperSDK->games(1);

### Click on a cell of a specific active game

    $response = $MineSweeperSDK->games->id(1)->click('1-1');

### Mark a cell of a specific active game as flag

    $response = $MineSweeperSDK->games->id(23)->flag('0-0');

### Mark a cell of a specific active game as question mark

    $response = $MineSweeperSDK->games->id(23)->questionMark('0-0');

### Debugging 

You can activate a debugged response by setting debug to true with the following method

    $MineSweeperSDK->debug(true);

