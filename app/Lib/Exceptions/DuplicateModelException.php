<?php
namespace App\Lib\Exceptions;

use Exception;

class DuplicateModelException extends Exception {
    private string $duplicateColumn;

    public function __construct(string $duplicateColumn, string $message)
    {
        parent::__construct($message);
        $this->duplicateColumn = $duplicateColumn;
    }

    public function getDuplicateColumn()
    {
        return $this->duplicateColumn;
    }
}