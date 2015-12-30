    <div class="blog-header">
        <h1 class="blog-title">Новости системы</h1>
        <p class="lead blog-description">Здесь публикуются все новости и обновления системы. Следите за новостями ежедневно!</p>
    </div>

    <div class="row">
        <div class="col-sm-8 blog-main">
        <? if (empty($this->blog[0])) { ?>
            <p>Нет данных для отображения</p>
        <? } else { foreach ($this->blog as $blog) { ?>
            <div class="blog-post">
                <h2 class="blog-post-title"><?=$blog->title?></h2>
                <p class="blog-post-meta"><?=$blog->published_at?> by <a href="<?=Tools::url('blog/about/author/' . $blog->user_id)?>"><?=$blog->user_name?></a></p>
                <?=empty($blog->tid) ? $blog->text : $blog->preview . '<a class="blog-detail-link" href="' . Tools::url('blog/article/show/' . $blog->tid) . '">Подробнее</a>'?>
            </div>
        <? }} ?>
        </div>
        <div class="col-sm-3 col-sm-offset-1 blog-sidebar">
            <div class="sidebar-module">
                <h4>Публикации по дате</h4>
            <? if (empty($this->pub_dates[0])) { ?>
                <p>Нет данных для отображения</p>
            <? } else { ?>
                <ol class="list-unstyled">
                <? foreach ($this->pub_dates as $item) { ?>
                    <li><a href="<?=Tools::url('blog/articles/date/' . $item->pub_date)?>"><?=$item->pub_date?></a></li>
                <? } ?>
                </ol>
            <? } ?>
            </div>
        </div>
    </div>