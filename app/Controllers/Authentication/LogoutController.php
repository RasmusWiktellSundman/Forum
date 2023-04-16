<?php
namespace App\Controllers\Authentication;

use App\Lib\Auth;

class LogoutController {
    public function destroy()
    {
        if(Auth::isLoggedIn())
            Auth::logout();

        // Dirigera till startsidan
        http_response_code(301); // Moved permanently
        header('Location: '.$_ENV['BASE_URL']);
    }
}