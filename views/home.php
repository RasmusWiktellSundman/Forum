<h1>Kategorier</h1>
<?php

use App\Models\Category;

foreach (Category::getAll() as $category) {
    ?>
    <h2><?php echo htmlspecialchars($category->getTitle()) ?></h2>
    <?php
}
?>