<?php
namespace App\Models;

use App\Lib\Database;
use DateTime;

class Post {
    private int $id;
    private int $author_id;
    private int $topic_id;
    private string $message;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    /**
     * Spara objektet till presistent lagring
     *
     * @param Type|null $var
     * @return Post
     */
    public static function create(string $message, User $author, Topic $topic): Post
    {
        // Sparar i databas
        $conn = Database::getConnection();
        $stmt = $conn->prepare("INSERT INTO post (message, author_id, topic_id, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW());");
        $stmt->execute([$message, $author->getId(), $topic->getId()]);
        $stmt->closeCursor();

        // Returnera Post objekt med angiven data
        $now = new DateTime();
        return new Post(
            (int) $conn->lastInsertId(),
            $message,
            $author,
            $topic,
            $now, 
            $now
        );
    }

    // Konstruktorn är privat då create ska användas av externa klasser, för att även lagras presistent.
    // Konstruktorn har ingen lokik för presistent lagring, då den även används för att skapa objekt från databasen
    private function __construct(int $id, string $message, User $author, Topic $topic, DateTime $createdAt, DateTime $updatedAt) {
        $this->id = $id;
        $this->message = $message;
        $this->author_id = $author->getId();
        $this->topic_id = $topic->getId();
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Hämtar post från presistent lagring baserat på id
     *
     * @param string $id Trådens id
     * @return Post|null Tråden med givet id, null om ingen tråd med givet id finns
     */
    public static function getById(int $id): Post|null
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM post WHERE id = ?;");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        // Ingen användare med given e-post finns
        if($row === false) {
            return null;
        }

        return self::postFromStatmentResultRow($row);
    }

    /**
     * Hämtar inlägg från presistent lagring baserat på tråd
     *
     * @param Topic $topic Tråden
     * @return Post[] Alla trådar som tillhör angiven kategori
     */
    public static function getByTopic(Topic $topic): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM post WHERE topic_id = ?;");
        $stmt->execute([$topic->getId()]);
        $rows = $stmt->fetchAll();
        $posts = array();
        foreach ($rows as $row) {
            $posts[] = self::postFromStatmentResultRow($row);
        }
        $stmt->closeCursor();
        return $posts;
    }

    /**
     * Skapar Post-objekt från databas rad
     *
     * @param array $row Array innehållandes data från en databasrad
     * @return Post
     */
    private static function postFromStatmentResultRow(array $row): Post
    {
        $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $row['created_at']);
        $updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $row['created_at']);
        return new Post(
            (int) $row['id'],
            $row['message'],
            User::getById($row['author_id']),
            Topic::getById($row['topic_id']),
            $createdAt,
            $updatedAt
        );
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return User::getById($this->author_id);
    }

    public function getTopic(): Topic
    {
        return Topic::getById($this->topic_id);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}