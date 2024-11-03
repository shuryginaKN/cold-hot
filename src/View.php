<?php

namespace shuryginaKN\cold_hot\View;

use cli;

function displayStartScreen()
{
    cli\line("Cold-Hot!");
}

function getUserInput($message = 'Enter your guess')
{
    return cli\prompt($message);
}

function showGameReplay(array $game, array $moves)
{
    cli\line("Game ID: {$game['id']}");
    cli\line("Player: {$game['player_name']}");
    cli\line("Field size: {$game['field_size']}");
    cli\line("Target number: {$game['target_number']}");
    cli\line("Start time: {$game['start_time']}");
    cli\line("End time: {$game['end_time']}");
    cli\line("Attempts: {$game['attempts']}");
    cli\line("Result: {$game['result']}");
    cli\line("\nMoves:");
    foreach ($moves as $move) {
        cli\line("Move {$move['move_number']}: Guess {$move['guess']}, Feedback {$move['feedback']}, Time {$move['time']}");
    }
}
