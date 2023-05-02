<?php
namespace App\Models;

use App\Lib\Database;
use App\Lib\Exceptions\DuplicateModelException;
use DateTime;

class Topic {
    private int $id;
    private int $author_id;
    private int $category_id;
    private string $title;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    /**
     * Spara objektet till presistent lagring
     *
     * @param Type|null $var
     * @return Topic
     */
    public static function create(string $title, User $author, Category $category): Topic
    {
        // Sparar i databas
        $conn = Database::getConnection();
        $stmt = $conn->prepare("INSERT INTO topic (title, author_id, category_id, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW());");
        $stmt->execute([$title, $author->getId(), $category->getId()]);
        $stmt->closeCursor();

        // Returnera Topic objekt med angiven data
        $now = new DateTime();
        return new Topic(
            (int) $conn->lastInsertId(),
            $title,
            $author,
            $category,
            $now, 
            $now
        );
    }

    // Konstruktorn är privat då create ska användas av externa klasser, för att även lagras presistent.
    // Konstruktorn har ingen lokik för presistent lagring, då den även används för att skapa objekt från databasen
    private function __construct(int $id, string $title, User $author, Category $category, DateTime $createdAt, DateTime $updatedAt) {
        $this->id = $id;
        $this->title = $title;
        $this->author_id = $author->getId();
        $this->category_id = $category->getId();
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Hämtar topic från presistent lagring baserat på id
     *
     * @param string $id Trådens id
     * @return Topic|null Tråden med givet id, null om ingen tråd med givet id finns
     */
    public static function getById(int $id): Topic|null
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM topic WHERE id = ?;");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        // Ingen användare med given e-post finns
        if($row === false) {
            return null;
        }

        return self::topicFromStatmentResultRow($row);
    }

    /**
     * Hämtar topics från presistent lagring baserat på kategori
     *
     * @param Category $category Kategorin
     * @return Topic[] Alla trådar som tillhör angiven kategori
     */
    public static function getByCategory(Category $category): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM topic WHERE category_id = ?;");
        $stmt->execute([$category->getId()]);
        $rows = $stmt->fetchAll();
        $topics = array();
        foreach ($rows as $row) {
            $topics[] = self::topicFromStatmentResultRow($row);
        }
        $stmt->closeCursor();
        return $topics;
    }

    /**
     * Skapar Topic-objekt från databas rad
     *
     * @param array $row Array innehållandes data från en databasrad
     * @return Topic
     */
    private static function topicFromStatmentResultRow(array $row): Topic
    {
        $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $row['created_at']);
        $updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $row['created_at']);
        return new Topic(
            (int) $row['id'],
            $row['title'],
            User::getById($row['author_id']),
            Category::getById($row['category_id']),
            $createdAt,
            $updatedAt
        );
    }

    /**
     * Hämtar alla inlägg kopplat till denna tråd
     *
     * @return Post[]
     */
    public function getPosts(): array
    {
        return Post::getByTopic($this);
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return User::getById($this->author_id);
    }

    public function getCategory(): Category
    {
        return Category::getById($this->category_id);
    }

    public function getTitle(): string
    {
        return $this->title;
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