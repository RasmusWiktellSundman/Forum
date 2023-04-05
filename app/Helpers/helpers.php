<?php

/**
 * Renderar en vy
 *
 * @param string $fileName
 * @return void
 */
function renderView(string $fileName, array $variables = [])
{
    // Skapar variabler för datan som skickades med till funktionen. Dessa variabler kan sedan användas i vyn.
    foreach ($variables as $key => $value) {
        $$key = $value;
    }
    require VIEWS_PATH . '/' . $fileName . '.php';
}