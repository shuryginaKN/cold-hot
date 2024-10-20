<?php

namespace shuryginaKN\cold_hot;

class Game
{
    private $targetNumber;
    private $attempts = 0;
    private $lastGuess;

    public function __construct()
    {
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

    public function checkGuess(int $guess): string
    {
        if ($guess < 100 || $guess > 999 || count(array_unique(str_split((string)$guess))) < 3) {
            return 'Please enter a three-digit number with unique digits.';
        }

        $this->attempts++;
        $difference = abs($guess - $this->targetNumber);

        if ($difference > 50) {
            $feedback = 'Very cold';
        } elseif ($difference > 20) {
            $feedback = 'Cold';
        } elseif ($difference > 10) {
            $feedback = 'Warm';
        } elseif ($difference > 5) {
            $feedback = 'Hot';
        } elseif ($difference > 0) {
            $feedback = 'Very hot';
        } else {
            $feedback = 'Correct';
        }

        if (isset($this->lastGuess)) {
            $previousDifference = abs($this->lastGuess - $this->targetNumber);
            if ($difference < $previousDifference) {
                $feedback .= ' and getting closer';
            } elseif ($difference > $previousDifference) {
                $feedback .= ' and getting farther';
            }
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
}
