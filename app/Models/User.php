<?php
namespace App\Models;

use App\Lib\Database;
use App\Lib\Exceptions\DuplicateModelException;
use App\Lib\Exceptions\InvalidUserInput;
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
    private ?string $profileImage;
    private ?string $profileImageType;
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

        // Försöker skapa user objekt och utför därmed validering av data
        $now = new DateTime();
        $user = new User(
            0,
            $email, 
            $username, 
            $firstname, 
            $lastname, 
            $password, 
            $now, 
            $now,
            false, // Lösenordet är inte hashat, utan behöver hashas
            $admin
        );


        // Sparar i databas
        $conn = Database::getConnection();
        $stmt = $conn->prepare("INSERT INTO user (email, username, firstname, lastname, password, admin, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW());");
        $stmt->execute([$email, $username, $firstname, $lastname, $user->getHashedPassword(), (int) $admin]);
        $stmt->closeCursor();

        $user->setId((int) $conn->lastInsertId());
        return $user;
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
    public static function exsistsColumnValue(string $column, mixed $value, int $allowedUserId): bool
    {
        // En kolumn kan inte bindas i ett prepared statment, validerar därför att kolumnen finns.
        if(!in_array($column, self::$valid_db_columns, true)) {
            throw new InvalidArgumentException("Invalid column");
        }

        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM user WHERE $column = ? AND id != ?;");
        $stmt->execute([$value, $allowedUserId]);
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
            true, // Lösenordet är redan hashat
            $row['profile_image'],
            $row['profile_image_type'],
            (bool) $row['admin']
        );
    }

    // Konstruktorn är privat då create ska användas av externa klasser, för att även lagras presistent.
    private function __construct(int $id, string $email, string $username, string $firstname, string $lastname, string $password, DateTime $createdAt, DateTime $updatedAt, bool $passwordIsHashed, ?string $profileImage = null, ?string $profileImageType = null, bool $admin = false) {
        // Uppdaterar värdet med hjälp av loop, för att inte behöva upprepa try-catch för varje set metod, men ändå kunna få en lista på alla fel.
        foreach (["id", "username", "email", "firstname", "lastname", "admin", "createdAt", "updatedAt"] as $property) {
            try {
                $setMethod = "set".$property;
                $this->$setMethod($$property);
            } catch (InvalidArgumentException | DuplicateModelException $ex) {
                $errors[$property] = $ex->getMessage();
            }
        }

        // Sätter profilbild om given
        $this->profileImage = $profileImage;
        $this->profileImageType = $profileImageType;

        // Sätter lösenord, antingen ett redan hashat lösenord, eller ett som behöver hashas.
        if($passwordIsHashed) {
            $this->setHashedPassword($password);
        } else {
            // Använder setPassword som både hashar och sätter lösenordet
            try {
                $this->setPassword($password);
            } catch (InvalidArgumentException $ex) {
                $errors['password'] = $ex->getMessage();
            }
        }

        if(!empty($errors)) {
            throw new InvalidUserInput($errors);
        }
    }

    /**
     * Uppdaterar användarens databasrad
     *
     * @return void
     */
    public function update(): void
    {
        $conn = Database::getConnection();
        if(isset($this->profileImage) && isset($this->profileImageType)) {
            $sql = "UPDATE user SET email=?, username=?, firstname=?, lastname=?, password=?, profile_image=?, profile_image_type=?, admin=?, updated_at=NOW() WHERE id=?;";
            $values = [$this->email, $this->username, $this->firstname, $this->lastname, $this->hashed_password, $this->profileImage, $this->profileImageType, (int) $this->admin, $this->id];
        } else {
            $sql = "UPDATE user SET email=?, username=?, firstname=?, lastname=?, password=?, admin=?, updated_at=NOW() WHERE id=?;";
            $values = [$this->email, $this->username, $this->firstname, $this->lastname, $this->hashed_password, (int) $this->admin, $this->id];
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute($values);
        $stmt->closeCursor();

        // Sätter uppdateringstid till nu
        $this->setUpdatedAt(new DateTime());
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

    /**
     * Renderar profilbild, om användaren inte har en profilbild används standardbilden.
     *
     * @return void
     */
    public function renderProfileImage(int $sideLength = 100)
    {
        if($this->getProfileImageType() == null || $this->getProfileImage() == null) {
            echo "<img src='" . $_ENV['BASE_URL'] . "/images/default_user.svg' alt='Standardprofilbild' width='".$sideLength."' height='".$sideLength."'>";
        } else {
            echo "<img src='data:".$this->getProfileImageType() . ";charset=utf8;base64,". base64_encode($this->getProfileImage()) . "' alt='Profilbild' width='".$sideLength."' height='".$sideLength."' /> ";
        }
    }

    // Getters och setters
    public function getId(): int {
        return $this->id;
    }

    // Id ska vara permanent, men settern används av konstruktor
    private function setId(int $id): void {
        if($id < 0) {
            throw new InvalidArgumentException("ID kan inte vara negativt");
        }
        $this->id = $id;
    }
    
    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        if($email == '') {
            throw new InvalidArgumentException("E-post är obligatoriskt");
        } else if(strlen($email) > 128) {
            throw new InvalidArgumentException("E-post får inte vara mer än 128 tecken");
        } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Ogiltigt format på e-postaddress");
        }

        // Kontrollerar så e-posten inte redan används av annan användare
        if(self::exsistsColumnValue('email', $email, $this->getId())) {
            throw new DuplicateModelException('email', "E-postadressen är upptagen");
        }
        $this->email = $email;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $username): void {
        if($username == '') {
            throw new InvalidArgumentException("Användarnamn är obligatoriskt");
        } else if(strlen($username) > 45) {
            throw new InvalidArgumentException("Användarnamnet får inte vara mer än 45 tecken");
        } else if(!preg_match('/^[\w_-]+$/', $username)) {
            throw new InvalidArgumentException("Användarnamnet får endast innehålla a-z, A-Z, 0-9, - och _");
        }

        // Kontrollerar så användarnamn inte redan används av annan användare, id finns inte ifall användaren är ny
        if(self::exsistsColumnValue('username', $username, $this->getId())) {
            throw new DuplicateModelException('username', "Användarnamnet är upptaget");
        }
        $this->username = $username;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void {
        if($firstname == '') {
            throw new InvalidArgumentException("Förnamn är obligatoriskt");
        } else if(strlen($firstname) > 45) {
            throw new InvalidArgumentException("Förnamnet får inte vara mer än 45 tecken");
        } else if(!preg_match('/^[\wå-öÅ-Ö _-]+$/', $firstname)) {
            throw new InvalidArgumentException("Förnamn får endast innehålla a-ö, A-Ö, 0-9, -, _ och mellanrum");
        }
        $this->firstname = $firstname;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void {
        if(!isset($lastname) || $lastname == '') {
            throw new InvalidArgumentException("Efternamn är obligatoriskt");
        } else if(strlen($lastname) > 45) {
            throw new InvalidArgumentException("Efternamnet får inte vara mer än 45 tecken");
        } else if(!preg_match('/^[\wå-öÅ-Ö _-]+$/', $lastname)) {
            throw new InvalidArgumentException("Efternamn får endast innehålla a-z, A-Z, å-ö, Å-Ö, 0-9, -, _ och mellanrum");
        }
        $this->lastname = $lastname;
    }

    public function getHashedPassword(): string {
        return $this->hashed_password;
    }

    /**
     * Hashar och sätter det hashade lösenordet
     *
     * @param string $password Lösenord i klartext
     * @return void
     */
    public function setPassword(string $password)
    {
        if($password == '') {
            throw new InvalidArgumentException("Lösenord är obligatoriskt");
        } else if(strlen($password) < 8) {
            throw new InvalidArgumentException("Lösenordet måste innehålla minst åtta tecken");
        }
        // password_hash hashar lösenordet, samt saltar automatiskt.
        $this->hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Sätter ett redan hashat lösenord
     *
     * @param string $hashedPassword Hashat lösenord
     * @return void
     */
    public function setHashedPassword(string $hashedPassword): void {
        $this->hashed_password = $hashedPassword;
    }

    public function isAdmin(): bool {
        return $this->admin;
    }

    public function setAdmin(bool $admin): void
    {
        $this->admin = $admin;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    // Metoden är privat eftersom update eller create ska användas externt, för att synkronisera med databasen.
    private function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    // Metoden är privat eftersom update eller create ska användas externt, för att synkronisera med databasen.
    private function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->createdAt = $updatedAt;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function getProfileImageType(): ?string
    {
        return $this->profileImageType;
    }

    /**
     * Sätter profilbild
     *
     * @param array $image Samma format som $_FILES, fast för en specifik input
     * @return void
     */
    public function setProfileImage(array $image): void
    {
        // Valliderar att uppladdad fil är en bild (bara för att filändelsen är bildformat, måste det inte vara en bild)
        $check = getimagesize($image['tmp_name']);
        if(!$check) {
            throw new InvalidArgumentException('Uppladdad profilbild måste vara en bild!');
        }

        // Kontrollerar filstorlek
        if ($image['size'] > $_ENV['PROFILE_IMAGE_MAX_SIZE']) {
            throw new InvalidArgumentException('Bilden är för stor!');
        }

        // Kontrollera filtyp
        $imageFileType = strtolower(pathinfo($image['name'],PATHINFO_EXTENSION));
        if(!in_array($imageFileType, ['png', 'jpg', 'jpeg'])) {
            throw new InvalidArgumentException('Ogiltig filtyp, endast png, jpg och jpeg är tillåtna.');
        }

        $this->profileImage = file_get_contents($image['tmp_name']);
        $this->profileImageType = test_input($image['type']);
    }
}