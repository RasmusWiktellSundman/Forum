<?php
namespace App\Lib;

use App\Models\User;

class Auth {
    private static ?User $logedInUser = null;

    /**
     * Logga in med hjälp av användarnamn och lösenord
     *
     * @param string $email    Användarens e-post
     * @param string $password Användarens lösenord
     * @return User|null       User objekt ifall uppgifterna stämmer, annars null
     */
    public static function login(string $email, string $password): User|null
    {
        $user = User::getByEmailAndPassword($email, $password);
        if($user == null) {
            return null;
        }

        // Skapar nytt session id, beskriv varför...
        session_regenerate_id();

        // Sparar endast id i session, då resterande information kan ändras. Om den ändras vill jag inte behöva uppdatera sessionen också, då det riskerar att missas och därmed ha olika version av datan. 
        // Istället sparas variabeln $logedInUser som endast är giltig under ett anrop
        $_SESSION['userId'] = $user->getId();
        self::$logedInUser = $user;
        return $user;
    }

    /**
     * Hämtar inloggad användare
     *
     * @return User|null User-objekt ifall en användare är inloggad, annars null
     */
    public static function user(): User|null
    {
        if(isset(self::$logedInUser)) {
            return self::$logedInUser;
        }

        // Om användaren inte är sparad, hämta då från databas ifall användaren är inloggad
        if(isset($_SESSION['userId'])) {
            self::$logedInUser = User::getById($_SESSION['userId']);
            return self::$logedInUser;
        }
        return null;
    }

    /**
     * Kollar om det finns en inloggad användare
     *
     * @return boolean Om användaren är inloggad
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['userId']);
    }

    public static function logout()
    {
        // Tar bort alla sessions variabler
        session_unset();

        // Tar bort sessionen
        session_destroy();

        // Tar bort användaren från Auth klassen
        self::$logedInUser = null;
    }
}