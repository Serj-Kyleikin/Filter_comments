<?php

//e($page[0]['static']);

    $count = count($page[0]['static']);
    $previous = $page[0]['pagination']['previous'];
    $next = $page[0]['pagination']['next'];
?>

<div class="articles">

    <?php for($i=0; $i<$count; $i++): 

        $article = $page[0]['static'][$i];
        $comments = $page[0]['dynamic']['content'][$i];
    ?>

        <div class="article" data-article="<?= $article['id']; ?>">
            <p><?=$article['text']; ?></p>
            <div class="comments">
                <? if(isset($_COOKIE['user'])): ?>
                    <div class="comments_add">
                    <p>Новый комментарий:</p>
                        <form method="post" name="add_<?= $article['id']; ?>" action="javascript:sentComment(<?= $article['id']; ?>)">
                            <textarea type="text" name="text" placeholder="Введите ваш комментарий" required></textarea>
                            <button name="submit" type="submit">Добавить</button>
                        </form>
                    </div>
                <? endif; ?>
                <div class="comments_filter">
                    <form method="post" action="javascript:showFiltered(<?= $article['id']; ?>)" autocomplete="off">
                        <input type="text" name="filter" id="filter_<?= $article['id']; ?>" oninput="filterComments(<?= $article['id']; ?>, this.value.length)" placeholder="Фильтр по имени автора">
                        <button name="submit" type="submit"></button>
                    </form>
                </div>
                <div class="comments_show">
                    <? if($comments != ''): foreach($comments as $key => $comment): ?>
                        <? if(gettype($comment) != 'string'): ?>
                            <div class="comment">
                                <b><? echo $comment['name']; ?></b>
                                <span><? echo $comment['text']; ?></span>
                                <p><? echo $comment['date']; ?></p>
                            </div>
                        <? endif; endforeach; endif; ?>
                </div>
                <? if(isset($comments[3]) and $comments[3] == 'show'): echo "<div class='comments_more' onclick='showComments(" . $article['id'] . ")'>Показать ещё</div>"; endif; ?>
            </div>
        </div>
    <?php endfor; ?>                      
</div>
