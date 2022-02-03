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
    <section class="lot-item container">
        <h2><?= e($item['name']) ?></h2>
        <div class="lot-item__content">
            <div class="lot-item__left">
                <div class="lot-item__image">
                    <img src="<?= $item['image_url'] ?>" width="730" height="548" alt="<?= e($item['name']) ?>">
                </div>
                <p class="lot-item__category">Категория: <span><?= e($item['category']) ?></span></p>
                <p class="lot-item__description"><?= e($item['description']) ?></p>
            </div>
            <div class="lot-item__right">
                <div class="lot-item__state">
                    <div class="lot-item__timer timer
                        <?= $countdown = countdown($item['completion_date']); ?>
                        <?= ($countdown[0]) === '00' ? 'timer--finishing' : '' ?>">
                        <?= e($countdown[0]); ?>:
                        <?= e($countdown[1]); ?>
                    </div>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span class="lot-item__cost"><?= e(formatAmount($item['initial_price'])) ?></span>
                        </div>
                        <div class="lot-item__min-cost">
                            <?php $newBet = formatAmount($currentPrice + (int)$openLot['bet_step']) ?>
                            Мин. ставка <span><?=e($newBet) ?></span>
                        </div>
                    </div>
                    <?php if (isset($_SESSION['userId'])) : ?>
                        <form class="lot-item__form" action="lot.php?id=<?=$_SESSION['lotId'] ?>" method="post" autocomplete="off">
                            <p class="lot-item__form-item form__item <?php if(count($errors) > 0): ?> form__item--invalid
                            <?php endif; ?>">
                                <label for="cost">Ваша ставка</label>
                                <input id="cost" type="text" name="cost" value="<?=getFilteredPostVal('cost'); ?>" placeholder="<?=e($newBet) ?>">
                                <span class="form__error"><?php echo ($errors['cost'] ?? "") ?></span>
                            </p>
                            <button type="submit" name="submit_bet" class="button">Сделать ставку</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="history">
                    <h3>История ставок (<span><?=count($openBets) ?></span>)</h3>
                    <table class="history__list">
                        <?php if (count($openBets)) {
                            foreach ($openBets as $bet): ?>
                                <tr class="history__item">
                                    <td class="history__name"><?= e($bet['name']) ?></td>
                                    <td class="history__price"><?= e(formatAmount($bet['amount'])) ?></td>
                                    <td class="history__time"><?= timeAgo($bet['created_at']) ?></td>
                                </tr>
                            <?php endforeach;} ?>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>
