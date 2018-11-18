<?php
if (isset($data->item) > 0):
    ?>

    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="contact-clean">
                        <form method="post" id="editpost" class="mwn">
                            <h2 class="text-center">Редактировать пост</h2>
                            <input type="hidden" name="id" value="<?= $data->item->id ?>">
                            <div class="form-group"><input type="text" name="title" placeholder="Тема"
                                                           value="<?= $data->item->title ?>" class="form-control"/>
                            </div>
                            <div class="form-group"><textarea rows="14" name="body" placeholder="Сообщение"
                                                              class="form-control h-100"><?= $data->item->body ?></textarea>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12"></div>
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

