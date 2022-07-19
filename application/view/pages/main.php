<div class="article">
    <p><span>Model-View-Controller</span> – это фундаментальный паттерн, который нашел применение во многих технологиях, дал развитие новым технологиям и каждый день облегчает жизнь программистам.<br>
    <span>MVC</span> — описывает простой способ построения структуры приложения, целью которого является отделение бизнес-логики от пользовательского интерфейса.</p>
    <div class="comments">
        <? if(isset($_COOKIE['user'])): ?>
            <div class="comments_add">
               <p>Новый комментарий:</p>
                <form method="post" action="javascript:sentComment()">
                    <textarea type="text" name="text" placeholder="Введите ваш комментарий" required></textarea>
                    <button name="submit" type="submit">Добавить</button>
                </form>
            </div>
        <? endif; ?>
            <div class="comments_filter">
                <form method="post" action="javascript:showFiltered()" autocomplete="off">
                    <input type="text" name="filter" id="filter" oninput="filterComments(this.value.length)" placeholder="Фильтр по имени автора">
                    <button name="submit" type="submit"></button>
                </form>
            </div>
            <div class="comments_show">
                <? if($page['content'] != ''): foreach($page['content'] as $key => $comment): ?>
                    <? if(gettype($comment) != 'string'): ?>
                        <div class="comment">
                            <b><? echo $comment['name']; ?></b>
                            <span><? echo $comment['text']; ?></span>
                            <p><? echo $comment['date']; ?></p>
                        </div>
                    <? endif; ?>
                <?php endforeach; endif; ?>
            </div>
            <? if(isset($page['content'][3]) and $page['content'][3] == 'show'): echo "<div class='comments_more' onclick='showComments()'>Показать ещё</div>"; endif; ?>
    </div>
</div>