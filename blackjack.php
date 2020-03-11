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
function generateSuit($suitname) {
    $suit = [];
    $suit[1] = [
        'Name' => 'Ace of ' . $suitname . '<br>',
        'Value' => 11,
    ];
    for ($i = 2; $i <= 10; $i++) {
        $suit[] = [
            'Name' => $i . ' of ' . $suitname . '<br>',
            'Value' => $i,
        ];
    }
    $suit[11] = [
        'Name' => 'Jack of ' . $suitname . '<br>',
        'Value' => 10,
    ];
    $suit[12] = [
        'Name' => 'Queen of ' . $suitname . '<br>',
        'Value' => 10,
    ];
    $suit[13] = [
        'Name' => 'King of ' . $suitname . '<br>',
        'Value' => 10,
    ];
    return $suit;
}

/**
 * Generates a standard deck of 52 cards without jokers
 *
 * @return array a standard deck of 52 cards without jokers
 */
function generateDeck() {
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
    return $deck;
}

/**
 * Deals a card to a player
 *
 * @param $playerHand array the array representing the hand of one of the players. This function needs to actually change
 *                      the player's hand, so is passed by reference
 * @param $deck array the array representing the deck
 * @param $depth int draw the card this many cards from the top (dealing the same depth twice will result in duplicates)
 */
function deal(&$playerHand, $deck, $depth) {
    $playerHand[] = $deck[$depth];
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
    shuffle($deck);
    deal($player1['Hand'], $deck, 0);
    deal($player2['Hand'], $deck, 1);
    deal($player1['Hand'], $deck, 2);
    deal($player2['Hand'], $deck, 3);
    $player1['Score'] = $player1['Hand'][0]['Value'] + $player1['Hand'][1]['Value'];
    $player2['Score'] = $player2['Hand'][0]['Value'] + $player2['Hand'][1]['Value'];
    printScore($player1);
    printScore($player2);
    if ($player1['Score'] > $player2['Score']) {
        echo '<h1>' . $player1['Name'] . ' Wins! <br>';
    } else if ($player2['Score'] > $player1['Score']) {
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
playGame($player1, $player2);