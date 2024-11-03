<?php

namespace shuryginaKN\cold_hot;

class Game
{
    private $targetNumber;
    private $attempts = 0;
    private $lastGuess;


    public function __construct()
    {
        $this->database = $database;
        $this->targetNumber = $this->generateUniqueThreeDigitNumber();
    }

    private function generateUniqueThreeDigitNumber(): int
    {
        $digits = range(0, 9);
        shuffle($digits);
        $number = $digits[0] * 100 + $digits[1] * 10 + $digits[2];

        if ($number < 100) {
            return $this->generateUniqueThreeDigitNumber();
        }

        return $number;
    }

    private function getComparisonFeedback($difference, $previousDifference)
    {
        if ($difference < $previousDifference) {
            return ' and getting closer';
        } elseif ($difference > $previousDifference) {
            return ' and getting farther';
        }
        return '';
    }

    private function getFeedback($difference)
    {
        if ($difference > 50) {
            return 'Very cold';
        } elseif ($difference > 20) {
            return 'Cold';
        } elseif ($difference > 10) {
            return 'Warm';
        } elseif ($difference > 5) {
            return 'Hot';
        } elseif ($difference > 0) {
            return 'Very hot';
        } else {
            return 'Correct';
        }
    }

    public function checkGuess(int $guess): string
    {
        if ($guess < 100 || $guess > 999 || count(array_unique(str_split((string)$guess))) < 3) {
            return 'Please enter a three-digit number with unique digits.';
        }

        $this->attempts++;
        $difference = abs($guess - $this->targetNumber);

        $feedback = $this->getFeedback($difference);

        if (isset($this->lastGuess)) {
            $previousDifference = abs($this->lastGuess - $this->targetNumber);
            $feedback .= $this->getComparisonFeedback($difference, $previousDifference);
        }

        $this->lastGuess = $guess;

        return $feedback;
    }


    public function isCorrectGuess(int $guess): bool
    {
        return $guess == $this->targetNumber;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function getTargetNumber()
    {
        return $this->targetNumber;
    }
}
