<link rel="stylesheet" href="<?php echo $_ENV['BASE_URL'] ?>/css/topic.css">

<section id="wrapper">
    <h2><b>Kategori: </b><?php renderText($topic->getCategory()->getTitle()); ?></h2>
    <h1><?php renderText($topic->getTitle()); ?></h1>
    <section id="topics">
        <table>
            <thead>
                <th>Tråd</th>
                <th>Senaste inlägg</th>
            </thead>
            <tbody>
                <tr>
                    <td>Test</td>
                    <td>Något</td>
                </tr>
            </tbody>
        </table>
    </section>
    <section id="posts">
        <?php
        foreach ($posts as $post) {
            ?>
            <div class="post">
                <div class="header">
                    <p class="author"><?php renderText($post->getAuthor()->getDisplayName()) ?></p>
                    <p class="publish_date"><?php renderText($post->getCreatedAt()->format("Y-m-d H:i:s")) ?></p>
                </div>
                <p><?php echo nl2br(htmlspecialchars($post->getMessage())) ?></p>
            </div>
            <?php
        }
        ?>
        <h3 id="message-form-title">Skapa inlägg</h3>
        <form id="message-form" action="<?php echo $_ENV['BASE_URL']; ?>/topic/<?php renderText($topic->getId()); ?>" method="post">
            <label for="message">Meddelande</label>
            <textarea type="text" name="message" id="message" rows="10" placeholder="Meddelande"></textarea>
            <input type="submit" value="Skapa inlägg">
        </form>
    </section>
</section>