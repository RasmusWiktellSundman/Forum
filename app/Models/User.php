<?php
namespace App\Models;

use App\Lib\Database;
use App\Lib\Exceptions\DuplicateModelException;
use DateTime;
use InvalidArgumentException;
use PDO;

class User {
    private static array $valid_db_columns = ["id", "email", "username", "firstname", "lastname", "password", "admin", "created_at", "updated_at"];
    
    private string $id;
    private string $email;
    private string $username;
    private string $firstname;
    private string $lastname;
    private string $hashed_password;
    private bool $admin = false;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function index()
    {
        renderView('test', 'base');
    }

    /**
     * Spara objektet till presistent lagring
     *
     * @param Type|null $var
     * @return User
     */
    public static function create(string $email, string $username, string $firstname, string $lastname, string $password, bool $admin = false): User
    {
        // password_hash hashar lösenordet, samt saltar automatiskt.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Använder inte en loop för check av användarnamn/e-post då stavningen är olika (-en/-et), la till metod för validering av kolumn värde istället
        // Kollar så användarnamn inte redan används
        if(self::exsistsColumnValue('username', $username)) {
            throw new DuplicateModelException('username', "Användarnamnet är upptaget");
        }

        // Kollar så e-posten inte redan används
        if(self::exsistsColumnValue('email', $email)) {
            throw new DuplicateModelException('email', "E-postadressen är upptagen");
        }

        // Sparar i databas
        $conn = Database::getConnection();
        $stmt = $conn->prepare("INSERT INTO user (email, username, firstname, lastname, password, admin, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW());");
        $stmt->execute([$email, $username, $firstname, $lastname, $hashed_password, (int) $admin]);
        $stmt->closeCursor();

        // Returnera User objekt med angiven data
        $now = new DateTime();
        return new User(
            (int) $conn->lastInsertId(),
            $email, 
            $username, 
            $firstname, 
            $lastname, 
            $hashed_password, 
            $now, 
            $now, 
            $admin
        );
    }

    /**
     * Hämtar användare från presistent lagring baserat på e-post och lösenord
     *
     * @param string $email E-post
     * @param string $password Lösenord i klartext
     * @return User|null User om email och lösenord stämde, annars null
     */
    public static function getByEmailAndPassword(string $email, string $password): User|null
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM user WHERE email = ?;");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        // Ingen användare med given e-post finns
        if($row === false) {
            return null;
        }
        
        // Kollar om angivet lösenord inte stämmer
        if(!password_verify($password, $row['password'])) {
            return null;
        }

        return self::userFromStatmentResultRow($row);
    }

    /**
     * Hämtar användare från presistent lagring baserat id
     *
     * @param string $id Användarens id
     * @return User|null User om email och lösenord stämde, annars null
     */
    public static function getById(int $id): User|null
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM user WHERE id = ?;");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        // Ingen användare med given e-post finns
        if($row === false) {
            return null;
        }

        return self::userFromStatmentResultRow($row);
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

        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM user WHERE $column = ?;");
        $stmt->execute([$value]);
        // Hämta antal rader från count
        $count = $stmt->fetchColumn();
        $stmt->closeCursor();

        return $count > 0;
    }

    /**
     * Skapar User-objekt från databas rad
     *
     * @param array $row Array innehållandes data från en databasrad
     * @return User
     */
    private static function userFromStatmentResultRow(array $row): User
    {
        $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $row['created_at']);
        $updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $row['created_at']);
        return new User(
            (int) $row['id'],
            $row['email'],
            $row['username'],
            $row['firstname'],
            $row['lastname'],
            $row['password'],
            $createdAt,
            $updatedAt,
            (bool) $row['admin']
        );
    }

    // Konstruktorn är privat då create ska användas av externa klasser, för att även lagras presistent.
    private function __construct(int $id, string $email, string $username, string $firstname, string $lastname, string $hashed_password, DateTime $createdAt, DateTime $updatedAt, bool $admin = false) {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->hashed_password = $hashed_password;
        $this->admin = $admin;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Hela namnet består av förnamn efternamn
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Visningsnamnet består av Förnamn efternamn (användarnamn)
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->firstname . ' ' . $this->lastname . ' (' . $this->username . ')';
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }
    
    public function getEmail(): string {
        return $this->email;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function getHashedPassword(): string {
        return $this->hashed_password;
    }

    public function isAdmin(): bool {
        return $this->admin;
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