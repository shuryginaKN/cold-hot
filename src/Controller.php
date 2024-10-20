<?php

namespace shuryginaKN\cold_hot\Controller;

use shuryginaKN\cold_hot\View;
use shuryginaKN\cold_hot\Game;

function startGame()
{
    View\displayStartScreen();

    $game = new Game();

    do {
        $guess = View\getUserInput();
        $feedback = $game->checkGuess((int) $guess);
        View\showFeedback($feedback);
    } while (!$game->isCorrectGuess((int) $guess));

    View\showFeedback("Congratulations! You've guessed the number in " . $game->getAttempts() . " attempts.");
}
