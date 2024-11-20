<?php 
// D:\wamp3\www\www.heroesprint.eu\rector.php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\Core\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    // Analizza tutti i file PHP tranne quelli in vendor
    $rectorConfig->paths([
        __DIR__ . '/*.php',           // File PHP nella root
        __DIR__ . '/logic',         // Cartella classes se esiste
        //__DIR__ . '/include',        // Cartella includes se esiste
        //__DIR__ . '/js',         // Cartella modules se esiste
        //__DIR__ . '/admin',             // Cartella src se esiste
        //__DIR__ . '/gmap',             // Cartella lib se esiste
        //__DIR__ . '/admin',           // Cartella admin se esiste
        // aggiungi qui altre cartelle specifiche se necessario
    ]);

    // Cartelle da escludere
    $rectorConfig->skip([
        __DIR__ . '/vendor/**',       // Esclude vendor e tutte le sue sottocartelle
        __DIR__ . '/tools/**',        // Esclude la cartella tools
        __DIR__ . '/cache/**',        // Esclude la cache
        __DIR__ . '/temp/**',         // Esclude i file temporanei
        __DIR__ . '/logs/**',         // Esclude i log
    ]);

    // Configurazioni per output e performance
    $rectorConfig->disableParallel();
    $rectorConfig->reportUnmatchedIgnoredErrors();
    $rectorConfig->indent(' ', 4);
    
    // Abilita statistiche dettagliate
    $rectorConfig->enableBypassGetters();
};
