<?php
namespace App\Controllers;

use App\Lib\Auth;
use App\Lib\Exceptions\DuplicateModelException;
use App\Lib\Exceptions\InvalidUserInput;
use App\Models\User;
use InvalidArgumentException;

class ProfileController {
    /**
     * Visa profilsida
     *
     * @return void
     */
    public function index()
    {
        // Kollar att användaren är inloggad
        if(!Auth::isLoggedIn()) {
            http_response_code(403);
            renderView('errors/403', 'base');
            return;
        }
        renderView('profile', 'base', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Uppdatera inloggad användare
     * Användarens anrop ska göras med POST
     *
     * @return void
     */
    public function store()
    {
        // Kollar att användaren är inloggad
        if(!Auth::isLoggedIn()) {
            http_response_code(403);
            renderView('errors/403', 'base');
            return;
        }

        try {
            // Array innehållandes godkänd användardata
            $validated = $this->validateStoreInput($_POST);
        } catch (InvalidUserInput $ex) {
            // Visa vy med errors vid ogiltig input och avbryt exekvering
            renderView('profile', 'base', [
                'errors' => $ex->getErrors(),
                'previous' => $ex->getValidated(),
                'user' => Auth::user()
            ]);
            return;
        }

        try {
            // Försöker uppdatera inloggad användare
            $user = Auth::user();
            // $user->set
        } catch (DuplicateModelException $ex) {
            // Uppdatering av användare misslyckades, visar felmeddelande
            renderView('profile', 'base', [
                'errors' => [$ex->getDuplicateColumn() => $ex->getMessage()],
                'previous' => $_POST,
                'user' => Auth::user()
            ]);
            return;
        } catch (InvalidArgumentException $ex) {
            // Uppdatering av användare misslyckades, visar felmeddelande
            renderView('profile', 'base', [
                'errors' => [ => $ex->getMessage()],
                'previous' => $_POST,
                'user' => Auth::user()
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

        if(isset($errors)) {
            throw new InvalidUserInput($errors, $validated);
        }

        return $validated;
    }
}