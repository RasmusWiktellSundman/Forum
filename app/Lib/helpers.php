<?php

/**
 * Renderar en vy med standard layout
 *
 * @param string $fileName
 * @param string|null $layoutName
 * @param array $variables
 * @return void
 */
function renderView(string $fileName, ?string $layoutName, array $variables = [])
{
    // Skapar variabler för datan som skickades med till funktionen. Dessa variabler kan sedan användas i vyn.
    foreach ($variables as $key => $value) {
        $$key = $value;
    }

    if($layoutName != null) {
        // Sparar view i variabel, variabeln används i layouten för att rendera innehållet på rätt plats.
        ob_start();
        require VIEWS_PATH . '/' . $fileName . '.php';
        $content = ob_get_clean();

        require VIEWS_PATH . '/layouts/' . $layoutName . '.php';
    } else {
        require VIEWS_PATH . '/' . $fileName . '.php';
    }
}

function renderError(?array $errors, string $errorName) {
    if(isset($errors[$errorName])) {
        ?>
        <p class="error"><?php echo htmlspecialchars($errors[$errorName]) ?></p>
        <?php
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}