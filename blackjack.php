<?php

// A card in Blackjack is one of 13 values and one of four suits. Each of the values is associated to a name

// Generate a suit as an array from two to ten
// Add the picture cards to the array
// Grant each of the cards a name

/**
 * function to generate the cards belonging to a single suit
 *
 * @param String suitName - the name of the suit which will be generated
 *
 * @return array - the 13 cards comprising the generated suit
 */
function generateSuit(String $suitName) : Array {
    $suit = [];
    // Generate the Ace. Default value is 11, although this can change later on
    $suit[1] = [
        'Name' => 'Ace of ' . $suitName,
        'Value' => 11,
        'id' => 1,
    ];
    for ($i = 2; $i <= 10; $i++) {
        $suit[] = [
            'Name' => $i . ' of ' . $suitName,
            'Value' => $i,
            'id' => $i,
        ];
    }
    // Generate the Picture Cards
    $suit[11] = [
        'Name' => 'Jack of ' . $suitName,
        'Value' => 10,
        'id' => 11,
    ];
    $suit[12] = [
        'Name' => 'Queen of ' . $suitName,
        'Value' => 10,
        'id' => 12,
    ];
    $suit[13] = [
        'Name' => 'King of ' . $suitName,
        'Value' => 10,
        'id' =>13,
    ];
    return $suit;
}

/**
 * Generates a standard deck of 52 cards without jokers. Do this by taking each suit and dealing them into the array.
 * When we do this, we need to ensure that no two cards are assigned the same ID so that no card is overwritten and
 * our final deck contains exactly 52 cards.
 * This function does this by assigning each card to an initial index equal to the number of cards it would be from the
 * top if the suits were simply placed on top of each other in descending order (starting with the King of Spades and
 * ending with the Ace of Diamonds).
 * For example, the Queen of Hearts is the 12th card of the Hearts, and is underneath all of the Diamonds and Clubs.
 * There are 13 each of Diamonds and clubs, so the index of the Queen of Hearts is 2*13 + 12 = 38.
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
    shuffle($deck);

    return $deck;
}

/**
 * Deals a card to a player if that player Has 16 points or fewer
 *
 * @param $player array the array representing the player being dealt a card
 * @param $deck array the array representing the deck
 * @param $depth int the number of cards missing from the top of the deck. Passed by reference since drawing a card
 * increases it
 *
 * @return int return 0 if the function completed successfully
 */
function deal(Array &$player, Array $deck, Int &$depth) : Int {
    $player['Hand'][] = $deck[$depth];
    $player['Score'] += $deck[$depth]['Value'];
    $depth += 1;
    return 0;
}

/**
 * function to check if a player is bust. If a player is bust but has an Ace (a card with a value of 11, the card's
 * value and player's score are both then reduced by ten and the player is no longer bust.
 *
 * @param array $player the player whose hand should be checked. This function saves persistent data about the player's
 * score, the value of a card in his hand, and whether or not he is bust. Therefore this parameter is passed by
 * reference
 *
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
 *
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
 *
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
 * Function to determine whether or not a player will have a card dealt to them. A player will want to draw an
 * additional card if they have not already won, their score is not currently over 16, and they have not already stopped
 * drawing cards by standing or going bust.
 *
 * @param $activePlayer array The player evaluating whether or not they would like to draw another card
 *
 * @return bool return whether or not the active player would like to draw another card
 */
function hitMe(array $players, array $activePlayer) : bool {
    $wantsACard = true;
    $allBust = true;
    foreach($players as $player) {
        if ($player != $activePlayer && $player['Bust'] == false) {
            $allBust = false;
        }
    }
    if ($activePlayer['Score'] > 16 || $activePlayer['Stood'] == true || $activePlayer['Bust'] == true || $allBust == true) {
        $wantsACard = false;
    }
    return $wantsACard;
}

/**
 * Function to determine which of the players has won the game of blackjack and print the announcement to the page.
 * Function evaluates the following:
 * If a player is bust, then he loses. Due to the way the dealing criteria in the main function work, all players being
 * bust is impossible.
 * If one player has a higher score than the other and is not bust, then he wins.
 * If both players have the same score and neither have a blackjack, it is a draw. If both players have 21 and one of
 * the players have a blackjack, that player wins.
 * If both players have a blackjack, it is a draw
 *
 * @param $players array the list of active players in the game
 *
 * @return Int return 0 if the function completed successfully
 */
function printWinners (array $players) : Int {
    // Find the highest score among all players
    $highestScore = 0;
    foreach ($players as $player) {
        // Bust flag was not reading correctly without this for some reason, although all bust players should
        // theoretically be flagged as such
        checkBust($player);
        if ($player['Bust'] == false && $player['Score'] > $highestScore) {
            $highestScore = $player['Score'];
        }
    }
    // Detect whether any player has a blackjack
    $playerHasBlackjack = false;
    foreach ($players as $player) {
        if (checkBlackjack($player) == true) {
            $playerHasBlackjack = true;
        }
    }
    // Create the array of Winners
    $winners = [];
    foreach ($players as $player) {
        if ($highestScore == $player['Score']) {
            if ($playerHasBlackjack == false) {
                $winners[] = $player['Name'];
            } else if ($playerHasBlackjack == true && checkBlackjack($player) == true) {
                $winners[] = $player['Name'];
            }
        }
    }
    // Print the array of Winners
    if (count($winners) == 1) {
        echo '<br> The winner is: ';
    } else {
        echo 'The winners are: ';
    }
    foreach ($winners as $winner) {
        echo $winner . ', ';
    }
    echo ' with a score of: ' . $highestScore;
    if ($playerHasBlackjack == true) {
        echo ' and a Blackjack!';
    }
    echo '</br>';
    return 0;
}

/**
 * Function to create a player
 *
 * @param $playerName String the name of the player
 *
 * @return array the player created by this function
 */
function createPlayer (String $playerName) : array {
    $player =[
        'Hand' => [],
        'Name' => $playerName,
        'Score' => 0,
        'Bust' => false,
        'Winner' => false,
        'Stood' => false,
    ];
    return $player;
}

/**
 * Function to create all players. Will always create the dealer as the last player present
 *
 * @param Int $playerNumber
 *
 * @return array the array of players who will be participating in the game
 */
function createAllPlayers (Int $playerNumber) : array {
    $players = [];
    for ($i = 0; $i < $playerNumber - 1; $i++) {
        $playerId = $i + 1;
        $players[] = createPlayer('Challenger ' . $playerId);
    }
    $players[] = createPlayer('Dealer');

    return $players;
}

/**
 * Function to initialise the hands
 *
 * @param array $players the players currently playing the game. Passed as reference because this function can add a
 * new player
 * @param array $deck the deck of cards being used to play
 * @param Int $depth the number of cards missing from the top of the deck (because they have already been dealt)
 *
 * @return Int return 0 if the function is run successfully
 */
function handInit(array &$players, array &$deck, Int &$depth) : Int {
    for ($i = 0; $i < count($players); $i++) {
        deal($players[$i], $deck, $depth);
        deal($players[$i], $deck, $depth);
        // If a player is dealt a double, their hand can be split provided there are no more than the maximum no. of
        // players
        if ($players[$i]['Hand'][0]['id'] == $players[$i]['Hand'][1]['id'] && count($players) < 7) {
            for ($j = count($players); $j > $i; $j--) {
                $players[$j] = $players[$j - 1];
            }
            $players[$i+1] = [
                'Hand' => [
                    0 => $players[$i]['Hand'][1],
                ],
                'Name' => $players[$i]['Name'] . ' Hand 2',
                'Score' => $players[$i]['Hand'][1]['Value'],
                'Bust' => false,
                'Winner' => false,
                'Stood' => false,
            ];
            deal($players[$i+1], $deck, $depth);
            $players[$i]['Name'] .= ' Hand 1';
            $players[$i]['Hand'] = [
                0 => $players[$i]['Hand'][0],
            ];
            $players[$i]['Score'] = $players[$i]['Hand'][0]['Value'];
            deal($players[$i], $deck, $depth);
            // Do not initialise the split hand again
            $i++;
        }
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
 * @param $playerNumber Int the number of players taking part in the game
 *
 * @return int return 0 if function completed successfully
 *
 * Internal parameters:
 * depth stores the number of cards missing from the top of the deck
 * activePlayers denotes the numbers of players who are still drawing cards (have not stood or bust)
 */
function playGame(Int $playerNumber) : Int {
    // players are stored as an array. Each individual player is also an array containing pieces of information about
    // that player
    $players = createAllPlayers($playerNumber);
    $activePlayers = count($players);
    $deck = generateDeck();
    $depth = 0;
    // Deal each player two cards
    handInit($players, $deck, $depth);
    // The game continues for as long as no player is bust and one player can keep drawing cards
    while ($activePlayers > 0) {
        // Since changes must be made to the player arrays themselves, this needs to be a for loop rather than a for
        // Each loop
        for ($i = 0; $i < count($players); $i++) {
            checkBust($players[$i]);
            // Deal card to each player in turn
            if (hitMe($players, $players[$i]) == true) {
                deal($players[$i], $deck, $depth);
            } else {
                $players[$i]['Stood'] = true;
                $activePlayers--;
            }
        }
    }
    // Once drawing has stopped, calculate the winner
    foreach($players as $player) {
        printScore($player);
    }
    printWinners($players);

    return 0;
}

playGame(4);