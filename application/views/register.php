<div class="contact-clean" style="background-color: rgb(237,242,247);">
    <form method="post" id="form-reg">
        <h2 class="text-center">Регистрация</h2>
        <div class="form-group"><input class="form-control " type="email" name="email" placeholder="Email">
            <small class="form-text text-danger"></small>
        </div>
        <div class="form-group"><input class="form-control " type="password" name="password" placeholder="Пароль">
            <small class="form-text text-danger"></small>
        </div>
        <div class="form-group">
            <button class="btn btn-primary" type="submit">Регистрация</button>
            <a class="btn btn-primary" href="<?= base_url() ?>auth">Войти</a></div>
    </form>
</div>
