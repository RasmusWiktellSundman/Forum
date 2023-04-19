<?php
namespace App\Models;

use App\Lib\Database;
use App\Lib\Exceptions\DuplicateModelException;
use DateTime;
use InvalidArgumentException;

class Category {
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
     * @return User
     */
    public static function create(string $title, string $description, bool $showInNavigation): Category
    {
        if(self::exsistsColumnValue('username', $username)) {
            throw new DuplicateModelException('username', "Användarnamnet är upptaget");
        }
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
}