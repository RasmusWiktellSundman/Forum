<?php
namespace App\Controllers\Authentication;

use App\Lib\Auth;
use App\Lib\Exceptions\DuplicateModelException;
use App\Lib\Exceptions\InvalidUserInput;
use App\Models\User;

class RegisterController {
    public function index()
    {
        if(Auth::isLoggedIn()) {
            // Användaren är redan inloggad, dirigera till startsidan
            http_response_code(301); // Moved permanently
            header('Location: '.$_ENV['BASE_URL']);
        }
        renderView('auth/register', 'base');
    }

    /**
     * Skapa ny användare
     * Användarens anrop ska göras med POST
     *
     * @return void
     */
    public function store()
    {
        try {
            // Array innehållandes godkänd användardata
            $validated = $this->validateStoreInput($_POST);
        } catch (InvalidUserInput $ex) {
            // Visa vy med errors vid ogiltig input och avbryt exekvering
            renderView('auth/register', 'base', [
                'errors' => $ex->getErrors(),
                'previous' => $ex->getValidated()
            ]);
            return;
        }

        try {
            // Försöker skapa användare
            User::create(
                $validated['email'], 
                $validated['username'],
                $validated['firstname'],
                $validated['lastname'],
                $validated['password'],
                false
            );
        } catch (DuplicateModelException $ex) {
            // Skapande av användare misslyckades, visar felmeddelande
            renderView('auth/register', 'base', [
                'errors' => [$ex->getDuplicateColumn() => $ex->getMessage()],
                'previous' => $validated
            ]);
            return;
        }

        // Loggar in användaren med det nya kontot
        Auth::login($validated['email'], $validated['password']);


        // Dirigera till startsidan
        http_response_code(301); // Moved permanently
        header('Location: '.$_ENV['BASE_URL']);
    }

    /**
     * Validerar användarens indata
     *
     * @param array $input Användarens indata
     * @throws InvalidUserInput Ifall angiven indata är ogiltig
     * @return array Validerad indata
     */
    private function validateStoreInput(array $input): array
    {
        // Array innehållandes godkänd användardata
        $validated = [];

        if(!isset($input['username']) || $input['username'] == '') {
            $errors['username'] = "Användarnamn är obligatoriskt";
        } else if(strlen($input['username']) > 45) {
            $errors['username'] = "Användarnamnet får inte vara mer än 45 tecken";
        } else if(!preg_match('/^[\w_-]+$/', $input['username'])) {
            $errors['username'] = "Användarnamnet får endast innehålla a-z, A-Z, 0-9, - och _";
        } else {
            $validated['username'] = test_input($input['username']);
        }

        if(!isset($input['email'])|| $input['email'] == '') {
            $errors['email'] = "E-post är obligatoriskt";
        } else if(strlen($input['email']) > 128) {
            $errors['email'] = "E-post får inte vara mer än 128 tecken";
        } else if(!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Ogiltigt format på e-postaddress";
        } else {
            $validated['email'] = test_input($input['email']);
        }

        if(!isset($input['firstname']) || $input['firstname'] == '') {
            $errors['firstname'] = "Förnamn är obligatoriskt";
        } else if(strlen($input['firstname']) > 45) {
            $errors['firstname'] = "Förnamnet får inte vara mer än 45 tecken";
        } else if(!preg_match('/^[\wå-öÅ-Ö _-]+$/', $input['firstname'])) {
            $errors['firstname'] = "Förnamn får endast innehålla a-z, A-Z, å-ö, Å-Ö, 0-9, -, _ och mellanrum";
        } else {
            $validated['firstname'] = test_input($input['firstname']);
        }

        if(!isset($input['lastname']) || $input['lastname'] == '') {
            $errors['lastname'] = "Efternamn är obligatoriskt";
        } else if(strlen($input['lastname']) > 45) {
            $errors['lastname'] = "Efternamnet får inte vara mer än 45 tecken";
        } else if(!preg_match('/^[\wå-öÅ-Ö _-]+$/', $input['lastname'])) {
            $errors['lastname'] = "Efternamn får endast innehålla a-z, A-Z, å-ö, Å-Ö, 0-9, -, _ och mellanrum";
        } else {
            $validated['lastname'] = test_input($input['lastname']);
        }

        if(!isset($input['password']) || $input['password'] == '') {
            $errors['password'] = "Lösenord är obligatoriskt";
        } else if(strlen($input['password']) < 8) {
            $errors['password'] = "Lösenordet måste innehålla minst åtta tecken";
        } else if($input['password'] != $input['password_confirm']) {
            $errors['password'] = "De två angiva lösenorden är inte samma";
        } else {
            $validated['password'] = test_input($input['password']);
        }

        if(isset($errors)) {
            throw new InvalidUserInput($errors, $validated);
        }

        return $validated;
    }
}