<?php
namespace App\Models;

use DateTime;

abstract class Model {
    protected DateTime $createdAt;
    protected DateTime $updatedAt;

    public function __construct(DateTime $createdAt, DateTime $updatedAt) {
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    // Metoden är protected eftersom update eller create ska användas externt, för att synkronisera med databasen.
    protected function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    // Metoden är protected eftersom update eller create ska användas externt, för att synkronisera med databasen.
    protected function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->createdAt = $updatedAt;
    }
}