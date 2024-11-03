<?php

namespace shuryginaKN\cold_hot;

use cli;

class Database
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new \PDO('sqlite:cold_hot.db');
        $this->createTables();
    }

    private function createTables()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS games (
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

    public function createMovesTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            game_id INTEGER,
            move_number INTEGER,
            guess INTEGER,
            feedback TEXT,
            time DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (game_id) REFERENCES games(id)
        )");
    }

    public function saveGame(array $data)
    {
        $sql = "INSERT INTO games (player_name, field_size, target_number, start_time, attempts, result) 
                VALUES (:player_name, 
                        :field_size, 
                        :target_number, 
                        :start_time, 
                        :attempts, 
                        :resultv)";
        return $this->execute($sql, $data);
    }

    public function updateGame(int $id, array $data)
    {
        $data[':id'] = $id;
        $sql = "UPDATE games SET attempts = :attempts, result = :result, end_time = :end_time WHERE id = :id";
        return $this->execute($sql, $data);
    }

    private function execute($sql, $data)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return $this->pdo->lastInsertId();
    }

    public function saveMove(int $game_id, int $move_number, int $guess, string $feedback)
    {
        if (!$this->gameExists($game_id)) {
            \cli\line("Error: Game with ID $game_id does not exist.");
            return;
        }

        $stmt = $this->pdo->prepare("INSERT INTO moves (game_id, move_number, guess, feedback) 
                                    VALUES (:game_id, :move_number, :guess, :feedback)");
        $stmt->execute([
            ':game_id' => $game_id,
            ':move_number' => $move_number,
            ':guess' => $guess,
            ':feedback' => $feedback
        ]);

        \cli\line("Move saved: Game ID: $game_id, Move Number: $move_number, Guess: $guess, Result: $feedback");
    }

    public function getGameById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM games WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getGames()
    {
        $stmt = $this->pdo->query("SELECT * FROM games ORDER BY start_time DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMovesByGameId(int $game_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM moves WHERE game_id = :game_id ORDER BY move_number ASC");
        $stmt->execute(['game_id' => $game_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
