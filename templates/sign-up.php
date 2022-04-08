<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php if (isset($categories)): foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="/index.php?category=<?= $category['symbol_code'] ?>"><?= e($category['name']) ?></a>
                </li>
            <?php endforeach; endif; ?>
        </ul>
    </nav>
    <form class="form container <?php if (isset($errors)): ?> form--invalid <?php endif; ?>" action="/add-account.php"
          method="post" autocomplete="off">
        <h2>Регистрация нового аккаунта</h2>
        <div class="form__item <?php if (isset($errors['email'])): ?> form__item--invalid <?php endif; ?>">
            <label for="email">E-mail <sup>*</sup></label>
            <input id="email" type="text" name="email" placeholder="Введите e-mail"
                   value="<?= getFilteredPostVal('email'); ?>">
            <span class="form__error"><?= $errMail = $errors['email'] ?? ""  ?></span>
        </div>
        <div class="form__item <?php if (isset($errors['password'])): ?> form__item--invalid <?php endif; ?>">
            <label for="password">Пароль <sup>*</sup></label>
            <input id="password" type="password" name="password" placeholder="Введите пароль"
                   value="<?= getFilteredPostVal('password'); ?>">
            <span class="form__error"><?= $errPass = $errors['password'] ?? "" ?></span>
        </div>
        <div class="form__item <?php if (isset($errors['name'])): ?> form__item--invalid <?php endif; ?>">
            <label for="name">Имя <sup>*</sup></label>
            <input id="name" type="text" name="name" placeholder="Введите имя"
                   value="<?= getFilteredPostVal('name'); ?>">
            <span class="form__error"><?= $errName = $errors['name'] ?? "" ?></span>
        </div>
        <div class="form__item <?php if (isset($errors['message'])): ?> form__item--invalid <?php endif; ?>">
            <label for="message">Контактные данные <sup>*</sup></label>
            <textarea id="message" name="message"
                      placeholder="Напишите как с вами связаться"><?= getFilteredPostVal('message'); ?></textarea>
            <span class="form__error"><?= $errMessage = $errors['message'] ?? "" ?></span>
        </div>
        <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
        <button type="submit" name="submit" class="button">Зарегистрироваться</button>
        <a class="text-link" href="#">Уже есть аккаунт</a>
    </form>
</main>
