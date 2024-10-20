<?php

namespace shuryginaKN\cold_hot\View;

use cli;

function displayStartScreen()
{
    cli\line("Cold-Hot!");
}

function getUserInput(): string
{
    return cli\prompt('Enter your guess');
}

function showFeedback(string $feedback)
{
    cli\line($feedback);
}
