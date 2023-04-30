<?php
namespace App\Models;

use App\Lib\Database;
use App\Lib\Exceptions\DuplicateModelException;
use DateTime;
use InvalidArgumentException;

class Category {
    private static array $valid_db_columns = ["id", "title", "description", "show_in_navigation", "created_at", "updated_at"];
    private string $id;
    private string $title;
    private string $description;
    private bool $showInNavigation;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    /**
     * Spara objektet till presistent lagring
     *
     * @param Type|null $var
     * @return Category
     */
    public static function create(string $title, string $description, bool $showInNavigation): Category
    {
        // Kollar så titeln inte redan används
        if(self::exsistsColumnValue('title', $title)) {
            throw new DuplicateModelException('title', "Titeln är uppdatgen");
        }

        // Sparar i databas
        $conn = Database::getConnection();
        $stmt = $conn->prepare("INSERT INTO category (title, description, show_in_navigation, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW());");
        // Castar till int då MYSQL använder tinyint för boolean
        $stmt->execute([$title, $description, (int) $showInNavigation]);
        $stmt->closeCursor();

        // Returnera Category objekt med angiven data
        $now = new DateTime();
        return new Category(
            (int) $conn->lastInsertId(),
            $title,
            $description,
            $showInNavigation,
            $now,
            $now
        );
    }

    // Konstruktorn är privat då create ska användas av externa klasser, för att även lagras presistent.
    private function __construct(int $id, string $title, string $description, bool $showInNavigation, DateTime $createdAt, DateTime $updatedAt) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->showInNavigation = $showInNavigation;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Hämtar användare från presistent lagring baserat id
     *
     * @param string $id Användarens id
     * @return Category|null 
     */
    public static function getById(int $id): Category|null
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM category WHERE id = ?;");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        // Ingen användare med given e-post finns
        if($row === false) {
            return null;
        }

        return self::cateogryFromStatmentResultRow($row);
    }

    /**
     * Alla kategorier från presistent lagring
     *
     * @return Category[]
     */
    public static function getAll(): array
    {
        $categories = array();
        $stmt = Database::getConnection()->prepare("SELECT * FROM category;");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $categories[] = self::cateogryFromStatmentResultRow($row);
        }
        $stmt->closeCursor();
        return $categories;
    }

    /**
     * Skapar category-objekt från databas rad
     *
     * @param array $row Array innehållandes data från en databasrad
     * @return Category
     */
    private static function cateogryFromStatmentResultRow(array $row): Category
    {
        $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $row['created_at']);
        $updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $row['created_at']);
        return new Category(
            (int) $row['id'],
            $row['title'],
            $row['description'],
            (bool) $row['show_in_navigation'],
            $createdAt,
            $updatedAt,
        );
    }

    /**
     * Kollar om ett specifikt värde i en specifik kolumn finns
     *
     * @param string $column Kolumn att leta i
     * @param mixed $value Värdet att leta efter
     * @return boolean Om värdet finns i kolumnen
     */
    public static function exsistsColumnValue(string $column, mixed $value): bool
    {
        // En kolumn kan inte bindas i ett prepared statment, validerar därför att kolumnen finns.
        if(!in_array($column, self::$valid_db_columns, true)) {
            throw new InvalidArgumentException("Invalid column");
        }

        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM category WHERE $column = ?;");
        $stmt->execute([$value]);
        // Hämta antal rader från count
        $count = $stmt->fetchColumn();
        $stmt->closeCursor();

        return $count > 0;
    }

    /**
     * Hämtar alla trådar som tillhör denna kategori
     *
     * @return Topic[]
     */
    public function getTopics(): array
    {
        return Topic::getByCategory($this);
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getShowInNavigation(): bool {
        return $this->showInNavigation;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }
}