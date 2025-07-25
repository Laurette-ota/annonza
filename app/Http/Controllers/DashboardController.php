<?php

// app/Http/Controllers/DashboardController.php

/**
 * Contrôleur DashboardController
    * Ce contrôleur gère l'affichage du tableau de bord de l'utilisateur.
    * Il vérifie si l'utilisateur vient de s'inscrire ou si son compte est très récent.
    */
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Vérifie si l'utilisateur vient de s'inscrire (via session flash)
        $isNewUser = session()->pull('just_registered', false);

        // Sinon, vérifie si le compte est très récent (< 5 minutes)
        if (!$isNewUser && auth()->check()) {
            $isNewUser = auth()->user()->created_at->diffInMinutes(now()) < 5;
        }

        return view('dashboard', compact('isNewUser'));
    }
}
