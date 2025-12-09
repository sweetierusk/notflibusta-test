<?php
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <?= Html::csrfMetaTags() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= Html::encode($this->title) ?> | Точно не флибуста</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>

    <!-- Шапка сайта -->
    <header>
        <div class="container header-content">
            <a href="<?= Yii::$app->homeUrl ?>" class="logo">
                <i class="fas fa-book"></i>
                Точно не флибуста
            </a>

            <div class="main-nav">
                <ul class="nav-menu">
                    <li><a href="<?= \yii\helpers\Url::to(['site/books']) ?>">Книги</a></li>
                    <li><a href="<?= \yii\helpers\Url::to(['site/authors']) ?>">Авторы</a></li>
                    <li><a href="<?= \yii\helpers\Url::to(['site/report']) ?>">Отчёт</a></li>
                </ul>

                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= \yii\helpers\Url::to(['auth/login']) ?>" class="auth-link login">
                        <i class="fas fa-sign-in-alt"></i> Войти
                    </a>
                <?php else: ?>
                    <div class="user-menu">
                        <div class="user-info">
                            <div class="user-avatar">
                                <?= strtoupper(substr(Yii::$app->user->identity->username, 0, 1)) ?>
                            </div>
                            <span class="user-name"><?= \yii\helpers\Html::encode(Yii::$app->user->identity->username) ?></span>
                        </div>
                        <a href="<?= \yii\helpers\Url::to(['auth/logout']) ?>"
                           data-method="post"
                           class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <?= $content ?>
        </div>
    </main>

    <div id="subscribe-popup" class="popup-overlay" style="display: none;">
        <div class="popup">
            <div class="popup-header">
                <h3 class="popup-title">Подписаться на автора</h3>
                <button class="close-popup" id="close-popup">&times;</button>
            </div>
            <form id="subscribe-form">
                <!-- Поле author-id будет добавляться динамически -->

                <div class="form-group">
                    <label for="author-name">Автор</label>
                    <input type="text" id="author-name" readonly class="form-control">
                </div>

                <div class="form-group">
                    <label for="phone">Номер телефона *</label>
                    <input type="tel"
                           id="phone"
                           name="phone"
                           placeholder="+7 (999) 123-45-67"
                           required
                           class="form-control"
                           pattern="\+?[0-9\s\-\(\)]+">
                    <small class="text-muted">Только номер телефона</small>
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-paper-plane"></i> Отправить подписку
                </button>
            </form>
        </div>
    </div>

    <!-- Футер -->
    <footer>
        <div class="container footer-content">
            <p>Не Флибуста &copy; <?= date('Y') ?>. Все права защищены.</p>
            <p>Верстка адаптирована для фреймворка Yii2</p>
        </div>
    </footer>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>