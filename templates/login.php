<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="pages/all-lots.html"><?= e($category['name']) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <form class="form container <?php if ($errors): ?> form--invalid <?php endif; ?>" action="sign-in.php"
          method="post">
        <h2>Вход</h2>
        <div class="form__item <?php if ($errors): ?> form__item--invalid <?php endif; ?>">
            <label for="email">E-mail <sup>*</sup></label>
            <input id="email" type="text" name="email" placeholder="Введите e-mail"
                   value="<?= getFilteredPostVal('email'); ?>">
            <span class="form__error"><?php if ($errors): ?>Неверный e-mail или пароль <?php endif; ?></span>
        </div>
        <div
            class="form__item form__item--last <?php if ($errors['password']): ?> form__item--invalid <?php endif; ?>">
            <label for="password">Пароль <sup>*</sup></label>
            <input id="password" type="password" name="password" placeholder="Введите пароль"
                   value="<?= getFilteredPostVal('password'); ?>">
            <span class="form__error"><?php if ($errors): ?>Неверный e-mail или пароль <?php endif; ?></span>
        </div>
        <button type="submit" name="submit" class="button">Войти</button>
    </form>
</main>
