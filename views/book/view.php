<?php
/** @var yii\web\View $this */
/** @var app\models\Book $model */

$this->title = $model->book_name ?: 'Книга без названия';
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['site/books']];
$this->params['breadcrumbs'][] = $this->title;

function safeHtml($value, $default = '') {
    return $value !== null ? htmlspecialchars((string)$value) : $default;
}

$bookName = $model->book_name ?: 'Не указано';
$authorsString = $model->getAuthorsString() ?: 'Автор не указан';
$releaseYear = $model->getFormattedReleaseYear() ?: 'Не указан';
$isbn = $model->isbn ?: 'Не указан';
$description = $model->description ?: 'Описание отсутствует';
?>

    <div class="book-view">
        <h1 class="page-title"><?= htmlspecialchars($bookName) ?></h1>

        <div class="card mb-20">
            <div class="row">
                <div class="col-md-4">
                    <div class="book-cover-container">
                        <img src="<?= $model->getCoverUrl() ?>"
                             alt="<?= htmlspecialchars($bookName) ?>"
                             class="img-fluid rounded shadow"
                             style="max-height: 400px; width: auto;">
                        <?php if ($model->img_link && strpos($model->img_link, 'img/covers/') === 0): ?>
                            <div class="mt-2 text-muted text-center">
                                <small>Файл: <?= safeHtml(basename($model->img_link)) ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="book-details">
                        <h3 class="mb-3">Информация о книге</h3>

                        <div class="mb-3">
                            <strong>Название:</strong> <?= htmlspecialchars($bookName) ?>
                        </div>

                        <div class="mb-3">
                            <strong>Автор(ы):</strong> <?= htmlspecialchars($authorsString) ?>
                        </div>

                        <div class="mb-3">
                            <strong>Год выпуска:</strong> <?= htmlspecialchars($releaseYear) ?>
                        </div>

                        <div class="mb-3">
                            <strong>ISBN:</strong> <?= htmlspecialchars($isbn) ?>
                        </div>

                        <div class="mb-3">
                            <strong>Описание:</strong>
                            <p class="mt-2"><?= nl2br(htmlspecialchars($description)) ?></p>
                        </div>

                        <div class="mt-20">
                            <?php if (!Yii::$app->user->isGuest): ?>
                                <a href="<?= \yii\helpers\Url::to(['/book/update', 'id' => $model->id]) ?>"
                                   class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Редактировать
                                </a>

                                <a href="<?= \yii\helpers\Url::to(['/book/delete', 'id' => $model->id]) ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('Вы уверены, что хотите удалить эту книгу?')"
                                   data-method="post">
                                    <i class="fas fa-trash"></i> Удалить
                                </a>
                            <?php endif; ?>

                            <a href="<?= \yii\helpers\Url::to(['/site/books']) ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Назад к списку
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
// Если нужен JavaScript для data-method
$this->registerJs(<<<JS
document.addEventListener('click', function(e) {
    if (e.target.matches('a[data-method]')) {
        e.preventDefault();
        
        if (e.target.getAttribute('data-confirm')) {
            if (!confirm(e.target.getAttribute('data-confirm'))) {
                return;
            }
        }
        
        const form = document.createElement('form');
        form.method = 'post';
        form.action = e.target.href;
        
        // CSRF токен
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_csrf';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]') 
            ? document.querySelector('meta[name="csrf-token"]').content 
            : '';
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
});
JS);
?>