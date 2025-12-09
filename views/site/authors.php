<?php
/** @var yii\web\View $this */

/** @var app\models\Authors[] $authors */

use yii\widgets\ActiveForm;

$this->title = 'Книжный каталог - Авторы';

// Функция для безопасного вывода
function safeHtml($value, $default = '')
{
    return $value !== null ? htmlspecialchars((string)$value) : $default;
}

?>

<h1 class="page-title">Авторы</h1>

<div class="card mb-20">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <p>Всего авторов в каталоге: <strong><?= count($authors) ?></strong></p>
            <?php if (!Yii::$app->user->isGuest): ?>
                <p class="text-muted" style="margin-top: 5px;">
                    <i class="fas fa-user-check"></i> Вы авторизованы как:
                    <strong><?= Yii::$app->user->identity->username ?? 'Пользователь' ?></strong>
                </p>
            <?php endif; ?>
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
            <div class="search-box">
                <input type="text" placeholder="Поиск авторов..." id="author-search">
                <i class="fas fa-search"></i>
            </div>

            <?php if (!Yii::$app->user->isGuest): ?>
                <a href="<?= \yii\helpers\Url::to(['author/create']) ?>" class="btn btn-success">
                    <i class="fas fa-plus"></i> Добавить автора
                </a>
            <?php else: ?>
                <a href="<?= \yii\helpers\Url::to(['auth/login']) ?>" class="btn btn-outline-primary"
                   title="Для добавления авторов необходимо авторизоваться">
                    <i class="fas fa-sign-in-alt"></i> Войти для добавления
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="authors-container" id="authors-container">
    <?php if (empty($authors)): ?>
        <div class="card">
            <p style="text-align: center; padding: 40px; color: #7f8c8d;">
                <i class="fas fa-user-pen" style="font-size: 48px; margin-bottom: 20px; display: block;"></i>
                В каталоге пока нет авторов
                <?php if (!Yii::$app->user->isGuest): ?>
                    <br>
                    <a href="<?= \yii\helpers\Url::to(['author/create']) ?>" class="btn btn-success mt-20">
                        <i class="fas fa-plus"></i> Добавить первого автора
                    </a>
                <?php else: ?>
                    <br>
                    <a href="<?= \yii\helpers\Url::to(['auth/login']) ?>" class="btn btn-primary mt-20">
                        <i class="fas fa-sign-in-alt"></i> Войти для добавления
                    </a>
                <?php endif; ?>
            </p>
        </div>
    <?php else: ?>
        <?php foreach ($authors as $author): ?>
            <?php
            $authorName = $author->full_name ?: 'Автор без имени';
            $booksCount = $author->getBooksCount();
            ?>
            <div class="author-card card" data-author-id="<?= $author->id ?>">
                <div class="author-info">
                    <div class="author-avatar">
                        <?= $author->getInitials() ?>
                    </div>
                    <div style="flex: 1;">
                        <div class="author-name">
                            <a href="<?= \yii\helpers\Url::to(['author/view', 'id' => $author->id]) ?>"
                               style="color: inherit; text-decoration: none;">
                                <?= safeHtml($authorName) ?>
                            </a>
                        </div>
                        <div class="author-stats">
                            <strong>Книг в каталоге:</strong> <?= $booksCount ?>
                        </div>

                        <?php if (!Yii::$app->user->isGuest): ?>
                            <div class="author-actions mt-10" style="display: flex; gap: 10px;">
                                <a href="<?= \yii\helpers\Url::to(['author/update', 'id' => $author->id]) ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Редактировать
                                </a>

                                <!-- ВСЕГДА показываем кнопку удаления, без проверки на книги -->
                                <!-- Простая форма без CSRF-токена -->
                                <form action="<?= \yii\helpers\Url::to(['author/delete', 'id' => $author->id]) ?>"
                                      method="post"
                                      style="display: inline;"
                                      onsubmit="return confirm('Вы уверены, что хотите удалить автора \'<?= addslashes($authorName) ?>\'? Все его книги останутся в каталоге, но без этого автора.')">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i> Удалить
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <button class="subscribe-btn" data-author-id="<?= $author->id ?>"
                        data-author-name="<?= safeHtml($authorName) ?>">
                    <i class="fas fa-bell"></i> Подписаться
                </button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Попап для подписки -->
<div id="subscribe-popup" class="popup-overlay" style="display: none;">
    <div class="popup">
        <div class="popup-header">
            <h3 class="popup-title">Подписаться на автора</h3>
            <button class="close-popup" id="close-popup">&times;</button>
        </div>
        <form id="subscribe-form">
            <input type="hidden" id="author-id" name="author_id">

            <div class="form-group">
                <label for="author-name-popup">Автор</label>
                <input type="text" id="author-name-popup" readonly class="form-control">
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
                <small class="text-muted">Только номер телефона. Другие данные не требуются.</small>
            </div>

            <div class="form-group mt-20">
                <button type="submit" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-paper-plane"></i> Отправить подписку
                </button>
            </div>

            <div class="text-center mt-10">
                <small class="text-muted">Подписываясь, вы соглашаетесь получать уведомления о новых книгах
                    автора</small>
            </div>
        </form>
    </div>
</div>

<?php
// JavaScript для работы с авторами
$this->registerJs(<<<JS
// Открытие попапа подписки
document.querySelectorAll('.subscribe-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const authorId = this.getAttribute('data-author-id');
        const authorName = this.getAttribute('data-author-name');
        
        document.getElementById('author-id').value = authorId;
        document.getElementById('author-name-popup').value = authorName;
        
        const popup = document.getElementById('subscribe-popup');
        popup.style.display = 'flex';
        
        // Фокус на поле телефона
        setTimeout(() => {
            document.getElementById('phone').focus();
        }, 100);
    });
});

// Закрытие попапа
document.getElementById('close-popup').addEventListener('click', function() {
    document.getElementById('subscribe-popup').style.display = 'none';
});

// Закрытие по клику вне попапа
document.getElementById('subscribe-popup').addEventListener('click', function(e) {
    if (e.target === this) {
        this.style.display = 'none';
    }
});

// Отправка формы подписки
document.getElementById('subscribe-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Отправка...';
    submitBtn.disabled = true;
    
    // Здесь должна быть логика отправки данных на сервер
    // Например, fetch запрос к API
    
    setTimeout(() => {
        alert('Заявка на подписку отправлена! Мы уведомим вас о новых книгах автора.');
        document.getElementById('subscribe-popup').style.display = 'none';
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        this.reset();
    }, 1000);
});

// Поиск авторов на клиенте
const authorSearch = document.getElementById('author-search');
if (authorSearch) {
    authorSearch.addEventListener('input', function(e) {
        const searchTerm = this.value.toLowerCase();
        const authorCards = document.querySelectorAll('.author-card');
        let visibleCount = 0;
        
        authorCards.forEach(card => {
            const authorName = card.querySelector('.author-name').textContent.toLowerCase();
            
            if (authorName.includes(searchTerm)) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Обновляем счетчик при поиске
        const countElement = document.querySelector('p:has(strong)');
        if (countElement && searchTerm) {
            countElement.innerHTML = 'Найдено авторов: <strong>' + visibleCount + '</strong>';
        } else if (countElement) {
            countElement.innerHTML = 'Всего авторов в каталоге: <strong>' + authorCards.length + '</strong>';
        }
    });
}
JS
);
?>

<?php
$this->registerJs(<<<JS
// Обработка форм удаления
document.addEventListener('submit', function(e) {
    if (e.target.tagName === 'FORM' && 
        e.target.action.includes('author/delete')) {
        
        const authorName = e.target.closest('.author-card')?.querySelector('.author-name')?.textContent?.trim() || 'автора';
        
        if (!confirm('Вы уверены, что хотите удалить автора "' + authorName + '"?')) {
            e.preventDefault();
            return false;
        }
        
        // Можно добавить индикатор загрузки
        const submitBtn = e.target.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            submitBtn.disabled = true;
        }
    }
});
JS
);
?>
