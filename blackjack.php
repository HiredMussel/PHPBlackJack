<?php

// A card in Blackjack is one of 13 values and one of four suits. Each of the values is associated to a name

// Generate a suit as an array from two to ten
// Add the picture cards to the array
// Grant each of the cards a name

/**
 * function to generate the cards belonging to a single suit
 *
 * @param String suitname - the name of the suit which will be generated
 * @return array - the 13 cards comprising the generated suit
 */
function generateSuit(String $suitname) : Array {
    $suit = [];
    // Generate the Ace
    $suit[1] = [
        'Name' => 'Ace of ' . $suitname,
        'Value' => 11,
    ];
    for ($i = 2; $i <= 10; $i++) {
        $suit[] = [
            'Name' => $i . ' of ' . $suitname,
            'Value' => $i,
        ];
    }
    // Generate the Picture Cards
    $suit[11] = [
        'Name' => 'Jack of ' . $suitname,
        'Value' => 10,
    ];
    $suit[12] = [
        'Name' => 'Queen of ' . $suitname,
        'Value' => 10,
    ];
    $suit[13] = [
        'Name' => 'King of ' . $suitname,
        'Value' => 10,
    ];
    return $suit;
}

/**
 * Generates a standard deck of 52 cards without jokers. Do this by taking each suit and dealing them into the array.
 * There are 13 cards in a suit. This function replicates the effect of simply stacking the suits on top of each other
 * to create the deck, so there will be 13 cards between any two cards with the same value in neighbouring suits.
 *
 * The function also generates a unique id for each card corresponding to its initial position in the deck. This is so that
 * we can check for specific cards later without having to check their value (e.g. distinguish between face cards and a 10
 * for the purpose of detecting a Blackjack)
 *
 * @return array a standard deck of 52 cards without jokers
 */
function generateDeck() : Array {
    $deck = null;
    $diamonds = generateSuit('Diamonds');
    $clubs = generateSuit('Clubs');
    $hearts = generateSuit('Hearts');
    $spades = generateSuit('Spades');
    for ($i = 1; $i <= 13; $i++) {
        $deck[$i] = $diamonds[$i];
        $deck[13+$i] = $clubs[$i];
        $deck[26+$i] = $hearts[$i];
        $deck[39+$i] = $spades[$i];
    }
    for ($i = 1; $i <= 52; $i++) {
        $deck[$i]['id'] = $i;
    }
    return $deck;
}

/**
 * Deals a card to a player
 *
 * @param $playerHand array the array representing the hand of one of the players. This function needs to actually change
 *                      the player's hand, so is passed by reference
 * @param $deck array the array representing the deck
 * @param $depth int draw the card this many cards from the top (dealing the same depth twice will result in duplicates)
 *
 * @return Boolean return 0 if the function executed successfully
 */
function deal(Array &$playerHand, Array $deck, Int $depth) : Boolean {
    $playerHand[] = $deck[$depth];
    return 0;
}

/**
 * Function to print a player's score
 *
 * @param $player array the player whose score should be printed
 */
function printScore($player) {
    $score = 0;
    echo '<h1>' . $player['Name'] . '</h1>';
    foreach ($player['Hand'] as $card) {
        echo $card['Name'] . '<br>';
        $score += $card['Value'];
    }
    echo 'Score: ' . $score . '<br><br>';
}

/**
 * Play a game of blackjack!
 *
 * @param $player1 array The first player
 * @param $player2 array The second player
 */
function playGame($player1, $player2) {
    $deck = generateDeck();
    $depth = 0;
    shuffle($deck);
    deal($player1['Hand'], $deck, 0);
    deal($player2['Hand'], $deck, 1);
    deal($player1['Hand'], $deck, 2);
    deal($player2['Hand'], $deck, 3);
    $player1['Score'] = $player1['Hand'][0]['Value'] + $player1['Hand'][1]['Value'];
    $player2['Score'] = $player2['Hand'][0]['Value'] + $player2['Hand'][1]['Value'];
    printScore($player1);
    printScore($player2);
    if ($player1['Score'] > $player2['Score'] || ($player2['Score'] > 21 && $player1['Score'] <= 21)) {
        echo '<h1>' . $player1['Name'] . ' Wins! <br>';
    } else if ($player2['Score'] > $player1['Score'] || ($player1['Score'] > 21 && $player2['Score'] <= 21)) {
        echo '<h1>' . $player2['Name'] . ' Wins! <br>';
    } else {
        echo '<h1> It\'s a Draw!</h1>';
    }
}
$player1=[
    'Hand' => [],
    'Name' => 'Player 1',
    'Score' => 0,
];
$player2=[
    'Hand' => [],
    'Name' => 'Player 2',
    'Score' => 0,
];
$deck = generateDeck();
echo '<pre>';
var_dump($deck);
echo '</pre>';