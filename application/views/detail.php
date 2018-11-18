<?php

if (isset($data->item) > 0):
    ?>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?//foreach($data->item as $item):
                    ?>
                    <?//print_r($item);
                    ?>
                    <div>
                        <div>
                            <h2><?= htmlspecialchars_decode($data->item->title) ?></h2>

                        </div>
                        <div>
                            <?= htmlspecialchars_decode($data->item->body) ?>
                        </div>

                    </div>
                    <?//endforeach;
                    ?>

                </div>
                <?//print_R($comments['data'])
                ?>
                <?
                if (isset($comments['data']->items) && count($comments['data']->items) > 0):?>
                    <div class="col-md-12">

                        <div class="comments">
                            <h3 class="title-comments">Комментарии (<?= count($comments['data']->items) ?>)</h3>
                            <ul class="media-list">
                                <?
                                foreach ($comments['data']->items as $cl):?>

                                    <hr>
                                    <li class="media">
                                        <div class="media-left">

                                        </div>
                                        <div class="media-body">
                                            <div class="media-heading">
                                                <div class="author"></div>
                                                <div class="metadata">
                                                    <span class="date"><?= $cl->created ?></span>
                                                </div>
                                            </div>
                                            <div class="media-text text-justify">
                                                <?= htmlspecialchars_decode($cl->body) ?>
                                            </div>
                                            <div class="footer-comment">
                                                <?
                                                if ($cl->edit === true):?><span class="edit-comment"><a
                                                            class="btn btn-info" data-id="<?= $cl->id ?>"
                                                            href="javascript:void(0)">Редактировать</a>
                                                    </span><?endif; ?>
                                                <?
                                                if ($cl->edit === true):?><span class="delete-comment"><a
                                                            class="btn btn-warning" data-id="<?= $cl->id ?>"
                                                            href="javascript:void(0)">Удалить</a></span><?endif; ?>
                                                <?
                                                if (isset($cl->cheked)):?><span class="change-comment"><a
                                                            class="btn btn-dark" data-id="<?= $cl->id ?>"
                                                            data-checked="<?= $cl->cheked ?>"
                                                            href="javascript:void(0)"><?= ($cl->cheked == 1) ? 'Не показывать' : 'Показывать' ?></a>
                                                    </span><?endif; ?>
                                            </div>
                                            <div class="ec d-none">

                                                <div class="form-group"><textarea rows="14" name="body"
                                                                                  placeholder="Message"
                                                                                  class="form-control"><?= htmlspecialchars_decode($cl->body) ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-primary update-comment"
                                                            data-id="<?= $cl->id ?>" type="submit">Сохранить
                                                    </button
                                                </div>
                                            </div>

                                        </div>
                                    </li>

                                <?endforeach; ?>

                            </ul>
                        </div>

                    </div>

                <?endif; ?>

                <div class="contact-clean" class="cfffw100" style="">
                    <form method="post" id="commentAdd" class="mwn" style=""><p>Добавить комментарий</p>
                        <input type="hidden" name="post_id" value="<?= $data->item->id ?>">
                        <div class="form-group"><textarea rows="14" name="body" placeholder="Message"
                                                          class="form-control"></textarea></div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Отправить</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>


<?
else:
    ?>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    Нету данных
                </div>
            </div>
        </div>
    </div>
<?
endif;
?>

