<h1>Kategorier</h1>
<table>
    <thead>
        <th>Titel</th>
        <th>Senaste inlägg</th>
    </thead>
    <tbody>
    <?php
    foreach ($categories as $category) {
        ?>
        
        <tr onclick="window.location = 'category/<?php renderText($category->getId()); ?>'" style="cursor: pointer;">
            <td><?php renderText($category->getTitle()) ?></td>
            <td>Kommer snart</td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>