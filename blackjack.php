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
    // Generate the Ace. Default value is 11, although this can change later on
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
    return $deck;
}

/**
 * Deals a card to a player if that player is not bust
 *
 * @param $player array the array representing the player being dealt a card
 * @param $deck array the array representing the deck
 * @param $depth int the number of cards missing from the top of the deck. Passed by reference since drawing a card increases it
 *
 * @return int return 0 if the function executed successfully
 */
function deal(Array &$player, Array $deck, Int &$depth) : Int {
    if ($player['Bust'] == false) {
        $player['Hand'][] = $deck[$depth];
        $player['Score'] += $deck[$depth]['Value'];
    }
    $depth += 1;
    return 0;
}

/**
 * function to check if a player is bust. If a player is bust but has an Ace (a card with a value of 11, the card's value
 * and player's score are both then reduced by ten and the player is no longer bust.
 *
 * @param array $player the player whose hand should be checked. This function saves persistent data about the player's
 * score, the value of a card in his hand, and whether or not he is bust. Therefore this parameter is passed by
 * reference
 * @return bool is the player bust?
 */
function checkBust(Array &$player) : bool {
    if ($player['Score'] > 21) {
        $player['Bust'] = true;
        // needs to be a for loop so that changes made to card value are persistent
        for ($i = 0; $i < count($player['Hand']); $i++) {
            if ($player['Hand'][$i]['Value'] == 11) {
                $player['Hand'][$i]['Value'] -= 10;
                $player['Score'] -= 10;
                $player['Bust'] = false;
                break;
            }
        }
    }
    return $player['Bust'];
}

/**
 * Function to check if a player has a Blackjack
 *
 * @param $player array the player to check
 * @return bool does this player have a blackjack?
 */
function checkBlackjack(Array $player) : bool {
    $hasBlackjack = true;
    if (count($player['Hand']) != 2) {
       $hasBlackjack = false;
    }
    if ($player['Hand'][0]['Value'] != 11 && $player['Hand'][1]['Value'] != 11) {
        $hasBlackjack = false;
    }
    if ($player['Hand'][0]['Value'] != 10 && $player['Hand'][1]['Value'] != 10) {
        $hasBlackjack = false;
    }
    return $hasBlackjack;
}

/**
 * Function to print a player's score. The function also prints out special notes for if the player is bust or if the
 * player has a blackjack
 *
 * @param $player array the player whose score should be printed
 * @return int return 0 if function completed successfully
 */
function printScore(Array $player) : Int {
    $score = $player['Score'];
    echo '<h1>' . $player['Name'] . '</h1>';
    foreach ($player['Hand'] as $card) {
        echo $card['Name'] . '<br>';
    }
    if ($player['Score'] > 21) {
        $score .= ' Bust!';
    }
    if (checkBlackjack($player)) {
        $score .= ' Blackjack!';
    }
    echo 'Score: ' . $score . '<br><br>';
    return 0;
}

/**
 * Function to determine which of two players has won the game of blackjack and print the announcement to the page.
 * Function evaluates the following:
 * If a player is bust, then he loses. Due to the way the dealing criteria in the main function work, both players being
 * bust is impossible.
 * If one player has a higher score than the other and is not bust, then he wins.
 * If both players have the same score and neither have a blackjack, it is a draw. If both players have 21 and one of the
 * players have a blackjack, that player wins.
 * If both players have a blackjack, it is a draw
 *
 * @param $player1 array the first player in the game
 * @param $player2 array the second player in the game
 *
 * @return Int return 0 if the function completed successfully
 */
function printWinner (array $player1, array $player2) : Int {
    if ($player1['Bust'] == true) {
        $player1['Winner'] = false;
        $player2['Winner'] = true;
    } else if ($player2['Bust'] == true) {
        $player2['Winner'] = false;
        $player1['Winner'] = true;
    } else if ($player1['Score'] > $player2['Score']) {
        $player1['Winner'] = true;
        $player2['Winner'] = false;
    } else if ($player1['Score'] < $player2['Score']) {
        $player2['Winner'] = true;
        $player1['Winner'] = false;
    } else {
        (checkBlackjack($player1)) ? $player1['Winner'] = true : $player1['Winner'] = false;
        (checkBlackjack($player2)) ? $player2['Winner'] = true : $player2['Winner'] = false;
    }

    if ($player1['Winner'] == $player2['Winner']) {
        echo '<h1>It\'s a draw!</h1>';
    } else if ($player1['Winner'] == true) {
        echo '<h1>' . $player1['Name'] . ' wins!</h1>';
    } else {
        echo '<h1>' . $player2['Name'] . ' wins!</h1>';
    }

    return 0;
}

/**
 * Play a game of blackjack! Player 1 and 2 take turns to draw from the deck if their scores are below 16. If either
 * of them goes over 21, they lose. Aces can count as either a 1 or an 11.
 *
 * If the two players have the same score, they draw unless one of them has an Ace and a 10 - the player with the Ace
 * and 10 wins. If both players have this hand then the game is still a draw
 *
 * @param $player1 array The first player
 * @param $player2 array The second player
 * @return int return 0 if function completed successfully
 *
 * Internal parameters: depth stores the number of cards missing from the top of the deck
 */
function playGame(Array $player1, Array $player2) : Int {
    $deck = generateDeck();
    shuffle($deck);
    $depth = 0;
    // The game continues for as long as neither player is bust and one player can keep drawing cards
    while (($player1['Score'] < 17 || $player2['Score'] < 17) && $player1['Bust'] == false && $player2['Bust'] == false) {
        if ($player1['Score'] < 17) {
            deal($player1, $deck, $depth);
        }
        // Check whether a player is bust after eliminating Aces
        checkBust($player1);
        // If player 1 has not busted out, then deal to player 2
        if ($player2['Score'] < 17 && $player1['Bust'] == false) {
            deal($player2, $deck, $depth);
        }
        checkBust($player2);
    }
    // Once drawing has stopped, calculate the winner
    printScore($player1);
    printScore($player2);
    printWinner($player1, $player2);

    return 0;
}

$challenger=[
    'Hand' => [],
    'Name' => 'Challenger',
    'Score' => 0,
    'Bust' => false,
    'Winner' => false,
];
$dealer=[
    'Hand' => [],
    'Name' => 'Dealer',
    'Score' => 0,
    'Bust' => false,
    'Winner' => false,
];

playGame($challenger, $dealer);