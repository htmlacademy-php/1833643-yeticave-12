<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="index.php?category=<?= $category['symbol_code'] ?>"><?= e($category['name']) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <section class="rates container">
        <h2>Мои ставки</h2>
        <table class="rates__list">
            <?php foreach ($lotsWithMyBets as $lotsWithMyBet): ?>
                <?php $hours_and_minuts_with_seconds = get_dt_range_with_seconds($lotsWithMyBet['completion_date']);

                if (!isset($lotsWithMyBet['winner_users_id'])) {
                    $lotsWithMyBet['winner_users_id'] = 0;
                }
                if ($lotsWithMyBet['winner_users_id'] == $_SESSION['userId']) {
                    //$class_item = "rates__item--win";
                    $class_timer = "timer--win";
                    $text_timer = "Ставка выиграла";
                } elseif ($lotsWithMyBet['winner_users_id'] != $_SESSION['userId'] && $hours_and_minuts_with_seconds[2] < 1) {
                    $class_item = "rates__item--end";
                    $class_timer = "timer--end";
                    $text_timer = "Торги окончены";
                } elseif ($hours_and_minuts_with_seconds[0] < 1) {
                    $class_timer = "timer--finishing";
                    $text_timer = getTimerValue($hours_and_minuts_with_seconds);
                } else {
                    $class_timer = "";
                    $text_timer = getTimerValue($hours_and_minuts_with_seconds);
                }
                ?>

                <tr class="rates__item <?= $class_item ?>">
                    <td class="rates__info">
                        <div class="rates__img">
                            <img src="<?= $lotsWithMyBet['image_url'] ?>" width="54" height="40" alt="Сноуборд">
                        </div>
                        <div>
                            <h3 class="rates__title"><a
                                    href="/lot.php?id=<?= $lotsWithMyBet['lot_Id'] ?>"><?= e($lotsWithMyBet['name']) ?></a>
                            </h3>


                            <?php if ($text_timer == "Ставка выиграла" || $text_timer == "Торги окончены") : ?>
                                <p> <?= e($lotsWithMyBet['contacts']) ?> </p><?php endif; ?>
                        </div>
                    </td>
                    <td class="rates__category">
                        <?= e($lotsWithMyBet['category_name']) ?>
                    </td>
                    <td class="rates__timer">
                        <div class="timer <?= $class_timer ?>"><?= $text_timer ?></div>
                    </td>
                    <td class="rates__price">
                        <?= e(formatAmount($lotsWithMyBet['price_my_bet'])) ?>
                    </td>
                    <td class="rates__time">
                        <?= get_dt_range_back($lotsWithMyBet['date_create_bet']) ?>
                        <!-- 5 минут назад -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
</main>
