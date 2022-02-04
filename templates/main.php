<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и
        горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <!--заполните этот список из массива категорий-->
        <?php foreach ($categories as $category): ?>
            <li class="promo__item promo__item--<?= e($category['symbol_code']); ?>">
                <a class="promo__link"
                   href="index.php?category=<?= $category['symbol_code'] ?>"><?= e($category['name']); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2><?= e($categoryName); ?></h2>
    </div>
    <ul class="lots__list">
        <?php foreach ($units as $unit): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= $unit['image']; ?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= e($unit['category']); ?></span>
                    <h3 class="lot__title"><a class="text-link"
                                              href="lot.php?id=<?= $unit['id'] ?>"><?= e($unit['name']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?= e(formatAmount($unit['price'])); ?></span>
                        </div>
                        <div class="lot__timer timer
                        <?= $countdown = countdown($unit['finTime']); ?>
                        <?= ($countdown[0]) === '00' ? 'timer--finishing' : '' ?>">
                            <?= e($countdown[0]); ?>:
                            <?= e($countdown[1]); ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

