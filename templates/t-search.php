<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php if (isset($categories)): foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="/index.php?category=<?= $category['symbol_code'] ?>"><?= e($category['name']) ?></a>
                </li>
            <?php endforeach;endif; ?>
        </ul>
    </nav>
    <div class="container">
        <section class="lots">
            <h2>Результаты поиска по запросу «<span><?= filteredGET('search'); ?></span>»</h2>
            <h3><?php if (count($searchResults ?? null) === 0): ?> Ничего не найдено по Вашему запросу <?php endif; ?></h3>
            <ul class="lots__list">
                <?php if (isset($searchResults)): foreach ($searchResults as $searchResult): ?>
                    <li class="lots__item lot">
                        <div class="lot__image">
                            <img src="<?= e($searchResult['image_url']) ?>" width="350" height="260"
                                 alt="<?= e($searchResult['name']) ?>">
                        </div>
                        <div class="lot__info">
                            <span class="lot__category"><?= e($searchResult['name_category']) ?></span>
                            <h3 class="lot__title"><a class="text-link"
                                                      href="/lot.php?id=<?= $searchResult['id'] ?>"><?= e($searchResult['name']) ?></a>
                            </h3>
                            <div class="lot__state">
                                <div class="lot__rate">
                                    <span class="lot__amount">Стартовая цена</span>
                                    <span
                                        class="lot__cost"><?= e(formatAmount($searchResult['initial_price'])) ?></span>
                                </div>
                                <?php $hoursMinuts = countdown($searchResult['completion_date']); ?>
                                <div
                                    class="lot__timer timer <?php if ($hoursMinuts[0] < 1): ?>timer--finishing<?php endif; ?>">
                                    <?= getTimerValue($hoursMinuts); ?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach;endif; ?>
            </ul>
        </section>
        <?php
        $previous = (int)(isset($activePage)) - 1;
        $next = (int)(isset($activePage)) + 1;

        $parametrs_query['search'] = filteredGET('search');
        $parametrs_query['find'] = filteredGET('find');

        $query = http_build_query($parametrs_query);
        $url_page = 'search.php?' . $query;
        ?>
        <?php if (isset($numberOfSearchedLots) > isset($numberLotsOnPage)): ?>
            <ul class="pagination-list">
                <li class="pagination-item pagination-item-prev"><?php if ((int)(isset($activePage)) > 1): ?> <a
                        href="<?= e($url_page . '&page=' . $previous) ?>">Назад</a><?php endif; ?></li>
                <?php for ($i = 1; $i <= isset($numberOfPage); $i++): ?>
                    <li class="pagination-item <?php if ((int)(isset($activePage)) === $i): ?> pagination-item-active <?php endif; ?>">
                        <a href="<?= e($url_page . '&page=' . $i) ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <li class="pagination-item pagination-item-next"><?php if ((int)(isset($activePage)) < (isset($numberOfPage))) : ?>
                    <a
                        href="<?= e($url_page . '&page=' . $next) ?>">Вперед</a><?php endif; ?></li>
            </ul>
        <?php endif; ?>
    </div>
</main>

