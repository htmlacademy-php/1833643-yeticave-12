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
    <div class="container">
        <section class="lots">
            <h2>Результаты поиска по запросу «<span><?= filteredGET('search'); ?></span>»</h2>
            <h3><?php if (count($searchResults) === 0): ?> Ничего не найдено по Вашему запросу <?php endif; ?></h3>
            <ul class="lots__list">
                <?php foreach ($searchResults as $searchResults): ?>
                    <li class="lots__item lot">
                        <div class="lot__image">
                            <img src="<?= e($searchResults['image_url']) ?>" width="350" height="260"
                                 alt="<?= e($searchResults['name']) ?>">
                        </div>
                        <div class="lot__info">
                            <span class="lot__category"><?= e($searchResults['name_category']) ?></span>
                            <h3 class="lot__title"><a class="text-link"
                                                      href="/lot.php?id=<?= $searchResults['id'] ?>"><?= e($searchResults['name']) ?></a>
                            </h3>
                            <div class="lot__state">
                                <div class="lot__rate">
                                    <span class="lot__amount">Стартовая цена</span>
                                    <span
                                        class="lot__cost"><?= e(formatAmount($searchResults['initial_price'])) ?></span>
                                </div>
                                <?php $hoursMinuts = countdown($searchResults['completion_date']); ?>
                                <div
                                    class="lot__timer timer <?php if ($hoursMinuts[0] < 1): ?>timer--finishing<?php endif; ?>">
                                    <?= getTimerValue($hoursMinuts); ?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php
        $previous = (int)$activePage - 1;
        $next = (int)$activePage + 1;

        $parametrs_query['search'] = filteredGET('search');
        $parametrs_query['find'] = filteredGET('find');

        $query = http_build_query($parametrs_query);
        $url_page = 'search.php?' . $query;
        ?>
        <?php if ($numberOfSearchedLots > $numberLotsOnPage): ?>
            <ul class="pagination-list">
                <li class="pagination-item pagination-item-prev"><?php if ((int)$activePage > 1): ?> <a
                        href="<?= e($url_page . '&page=' . $previous) ?>">Назад</a><?php endif; ?></li>
                <?php for ($i = 1; $i <= $numberOfPage; $i++): ?>
                    <li class="pagination-item <?php if ((int)$activePage === $i): ?> pagination-item-active <?php endif; ?>">
                        <a href="<?= e($url_page . '&page=' . $i) ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <li class="pagination-item pagination-item-next"><?php if ((int)$activePage < $numberOfPage): ?>  <a
                        href="<?= e($url_page . '&page=' . $next) ?>">Вперед</a><?php endif; ?></li>
            </ul>
        <?php endif; ?>
    </div>
</main>

