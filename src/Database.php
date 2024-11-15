<?php

namespace shuryginaKN\cold_hot;

use RedBeanPHP\R as R;
use cli;

class Database
{
    public function __construct()
    {

        if (!R::testConnection()) {
            R::setup('sqlite:cold_hot.db');
        }

        if (!R::testConnection()) {
            throw new \Exception("Unable to connect to the database.");
        }

        $this->createTables();
    }

    private function createTables()
    {
        if (!R::findOne('games')) {
            R::exec("CREATE TABLE IF NOT EXISTS games (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                player_name TEXT,
                field_size INTEGER,
                target_number INTEGER,
                start_time DATETIME DEFAULT CURRENT_TIMESTAMP,
                end_time DATETIME,
                attempts INTEGER,
                result TEXT
            )");
        }
    }

    public function createMovesTable()
    {
        if (!R::findOne('attempts')) {
            R::exec("CREATE TABLE IF NOT EXISTS attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                game_id INTEGER,
                move_number INTEGER,
                guess INTEGER,
                feedback TEXT,
                time DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (game_id) REFERENCES games(id)
            )");
        }
    }

    public function saveGame(array $data)
    {
        $game = R::dispense('games');
        $game->player_name = $data['player_name'];
        $game->field_size = $data['field_size'];
        $game->target_number = $data['target_number'];
        $game->start_time = $data['start_time'];
        $game->attempts = $data['attempts'];
        $game->result = $data['result'];

        return R::store($game);
    }

    public function updateGame(int $id, array $data)
    {
        $game = R::load('games', $id);

        if ($game) {
            $game->attempts = $data['attempts'];
            $game->result = $data['result'];
            $game->end_time = $data['end_time'];

            return R::store($game);
        }

        return null;
    }

    private function execute($sql, $data)
    {
        $stmt = R::getWriter()->getConnection()->prepare($sql);
        $stmt->execute($data);

        if (strpos($sql, 'INSERT') === 0) {
            return R::getWriter()->getConnection()->lastInsertId();
        }

        return $stmt->rowCount();
    }

    public function saveMove(int $game_id, int $move_number, int $guess, string $feedback)
    {
        if (!$this->gameExists($game_id)) {
            \cli\line("Error: Game with ID $game_id does not exist.");
            return;
        }

        $move = R::dispense('moves');
        $move->game_id = $gameId;
        $move->move_number = $moveNumber;
        $move->guess = $guess;
        $move->feedback = $feedback;

        R::store($move);

        \cli\line("Move saved: Game ID: $game_id, Move Number: $move_number, Guess: $guess, Result: $feedback");
    }

    public function getGameById(int $id)
    {
        return R::load('games', $id);
    }

    public function getGames()
    {
        return R::findAll('games', 'ORDER BY start_time DESC');
    }

    public function getMovesByGameId(int $game_id)
    {
        return R::findAll('moves', 'game_id = ? ORDER BY move_number ASC', [$game_id]);
    }
}
