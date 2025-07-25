<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Exécutez les seeders de la base de données.
     * Ce seeder remplit la table des devises avec des données de devises prédéfinies.
     * Les données sont chargées depuis un fichier JSON situé dans database/seeders/currencies.json.
     * Chaque devise est identifiée par son code unique. Si une devise existe déjà, elle ne sera pas dupliquée. Le nom et le symbole de la devise sont également enregistrés.
     */
    // database/seeders/CurrencySeeder.php
public function run(): void
{
    $path = database_path('seeders/iso4217.json');
    if (!file_exists($path)) {
        throw new \Exception("Fichier iso4217.json introuvable dans database/seeders/");
    }

    $json = json_decode(file_get_contents($path), true);
    if (!is_array($json)) {
        throw new \Exception("JSON invalide ou vide");
    }

    foreach ($json as $row) {
        if (!empty($row['AlphabeticCode']) && !empty($row['Currency'])) {
            \App\Models\Currency::firstOrCreate(
                ['code' => $row['AlphabeticCode']],
                [
                    'name'   => $row['Currency'],
                    'symbol' => $row['CurrencySymbol'] ?? null,
                ]
            );
        }
    }
}
}
