<?php
/** @var yii\web\View $this */
/** @var app\models\Book $model */

$this->title = $model->book_name;
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['site/books']];
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="book-view">
        <h1 class="page-title"><?= htmlspecialchars($model->book_name) ?></h1>

        <div class="card mb-20">
            <div class="row">
                <div class="col-md-4">
                    <div class="book-cover-container">
                        <img src="<?= $model->getCoverUrl() ?>"
                             alt="<?= htmlspecialchars($model->book_name) ?>"
                             class="img-fluid rounded shadow"
                             style="max-height: 400px; width: auto;">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="book-details">
                        <h3 class="mb-3">Информация о книге</h3>

                        <div class="mb-3">
                            <strong>Название:</strong> <?= htmlspecialchars($model->book_name) ?>
                        </div>

                        <div class="mb-3">
                            <strong>Автор(ы):</strong> <?= htmlspecialchars($model->getAuthorsString()) ?>
                        </div>

                        <div class="mb-3">
                            <strong>Год выпуска:</strong> <?= $model->getFormattedReleaseYear() ?>
                        </div>

                        <div class="mb-3">
                            <strong>ISBN:</strong> <?= htmlspecialchars($model->isbn) ?>
                        </div>

                        <div class="mb-3">
                            <strong>Описание:</strong>
                            <p class="mt-2"><?= nl2br(htmlspecialchars($model->description)) ?></p>
                        </div>

                        <div class="mt-20">
                            <?php if (!Yii::$app->user->isGuest): ?>
                                <a href="<?= \yii\helpers\Url::to(['book/update', 'id' => $model->id]) ?>"
                                   class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Редактировать
                                </a>

                                <button type="button"
                                        class="btn btn-danger"
                                        onclick="deleteBook(<?= $model->id ?>)">
                                    <i class="fas fa-trash"></i> Удалить
                                </button>
                            <?php endif; ?>

                            <a href="<?= \yii\helpers\Url::to(['site/books']) ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Назад к списку
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$this->registerJs(<<<JS
function deleteBook(id) {
    if (!confirm('Вы уверены, что хотите удалить эту книгу?')) {
        return;
    }
    
    fetch('/book/delete?id=' + id, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '/books';
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка при удалении книги');
    });
}
JS);
?>