<?php
namespace App\Controllers;

use App\Lib\Auth;
use App\Lib\Exceptions\DuplicateModelException;
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

        // Försöker uppdatera inloggad användare
        $user = Auth::user();
        $errors = [];
        // Uppdaterar värdet för samtliga ändringsbara fält
        foreach (["username", "email", "firstname", "lastname"] as $property) {
            try {
                $setMethod = "set".$property;
                $user->$setMethod(test_input($_POST[$property]));
            } catch (InvalidArgumentException | DuplicateModelException $ex) {
                $errors[$property] = $ex->getMessage();
            }
        }
        if(empty($errors)) {
            // Uppdaterar databas
            $user->update();
        } else {
            // Användarens indata är felaktig, visar felmeddelande
            renderView('profile', 'base', [
                'errors' => $errors,
                'previous' => $_POST,
                'user' => Auth::user()
            ]);
            return;
        }

        // Laddar om sidan
        renderView('profile', 'base', [
            'user' => Auth::user(),
            'updatedSuccessfully' => true
        ]);
    }

    /**
     * Uppdaterar användares lösenord
     *
     * @return void
     */
    public function updatePassword(): void
    {
        // Kollar att användaren är inloggad
        if(!Auth::isLoggedIn()) {
            http_response_code(403);
            renderView('errors/403', 'base');
            return;
        }

        // Validerar input
        if(!isset($_POST['old_password'])) {
            $errors['old_password'] = "Gammalt lösenord är obligatoriskt";
        }
        if(!isset($_POST['new_password'])) {
            $errors['new_password'] = "Nytt lösenord är obligatoriskt";
        }
        if(test_input($_POST['new_password']) != test_input($_POST['password_confirm'])) {
            $errors['new_password'] = "De två angiva lösenorden är inte samma";
        }

        // Hämtar inloggad användare
        $user = Auth::user();

        if(!password_verify(test_input($_POST['old_password']), $user->getHashedPassword())) {
            $errors['old_password'] = "Lösenordet är fel";
        }

        if(isset($errors)) {
            // Användarens indata är felaktig, visar felmeddelande
            renderView('profile', 'base', [
                'errors' => $errors,
                'previous' => $_POST,
                'user' => Auth::user()
            ]);
            return;
        }

        // Indata är korrekt, uppdaterar användare
        try {
            $user->setPassword(test_input($_POST['new_password']));
        } catch (InvalidArgumentException $ex) {
            renderView('profile', 'base', [
                'errors' => ['new_password' => $ex->getMessage()],
                'previous' => $_POST,
                'user' => Auth::user()
            ]);
            return;
        }
        $user->update();

        // Lyckades uppdatera lösenordet, laddar om sidan
        renderView('profile', 'base', [
            'user' => Auth::user(),
        'updatedPasswordSuccessfully' => true
        ]);
    }
}