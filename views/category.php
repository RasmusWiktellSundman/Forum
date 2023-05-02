<?php

use App\Lib\Auth;
use App\Models\Topic;

?>

<h1>Kategori: <?php renderText($category->getTitle()); ?></h1>
<?php 
if(Auth::isLoggedIn()) {
?> <a href="../topic/create?category=<?php renderText($category->getId()); ?>"><button>Skapa tråd</button></a> <?php
}
?>
<h2>Trådar</h2>
<table>
    <thead>
        <th>Titel</th>
        <th>Skapare</th>
        <th>Senaste meddelande</th>
    </thead>
    <tbody>
        <?php
        /** @var Topic[] $topics */
        foreach ($topics as $topic) {
            ?>
            <tr onclick="window.location = '<?php renderText($category->getId()); ?>/<?php renderText($topic->getId()); ?>'" style="cursor: pointer;">
                <td><?php renderText($topic->getTitle()) ?></td>
                <td><?php renderText($topic->getAuthor()->getUsername()) ?></td>
                <td>Kommer snart</td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>