<?php

if (count($data) > 0):
    ?>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?
                    foreach ($data->item as $item):?>
                        <?//print_r($item);
                        ?>
                        <div class="postitem">
                            <div>
                                <h2><?= htmlspecialchars_decode($item->title) ?></h2>
                                <span>Дата: <?= $item->created ?></span>

                            </div>
                            <div>
                                <?= word_limiter(htmlspecialchars_decode($item->body), 100) ?>
                            </div>
                            <div><a class="btn btn-info" href="<?= base_url() . 'main/detail/' ?><?= $item->id ?>/">Подробнее</a>
                                <?
                                if ($item->edit === true):?><a class="btn btn-info"
                                                               href="<?= base_url() . 'main/editpost/' ?><?= $item->id ?>/">
                                        Редактировать</a><?endif; ?>
                                <?
                                if ($item->delete === true):?><a class="btn btn-info deleteitem"
                                                                 data-id="<?= $item->id ?>" href="javascript:void(0)">
                                        Удалить</a><?endif; ?>
                            </div>
                            <div></div>
                            <hr>
                        </div>

                    <?endforeach; ?>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $pagination ?>
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

