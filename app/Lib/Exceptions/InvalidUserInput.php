<?php
namespace App\Lib\Exceptions;

use Exception;

class InvalidUserInput extends Exception {
    /**
     * Array innehållandes felmeddelanden för varje input fält
     *
     * @var array
     */
    private array $errors;

    /**
     * Array innehållandes godkänd data
     *
     * @var array
     */
    private ?array $validated;

    public function __construct(array $errors, array $validated = null) {
        parent::__construct('Invalid user input', 400);
        $this->errors = $errors;
        $this->validated = $validated;
    }

    public function getErrors() {
        return $this->errors;
    }
      
    public function getValidated() {
        return $this->validated;
    }
}