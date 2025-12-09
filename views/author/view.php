<?php
/** @var yii\web\View $this */
/** @var app\models\Authors $model */

use yii\widgets\ActiveForm;

$this->title = $model->full_name ?: 'Автор без имени';
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['site/authors']];
$this->params['breadcrumbs'][] = $this->title;

// Функция для безопасного вывода
function safeHtml($value, $default = '') {
    return $value !== null ? htmlspecialchars((string)$value) : $default;
}

$authorName = $model->full_name ?: 'Автор без имени';
$booksCount = $model->getBooksCount();
?>

    <div class="author-view">
        <h1 class="page-title"><?= safeHtml($authorName) ?></h1>

        <div class="card mb-20">
            <div class="author-details">
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
                    <div class="author-avatar" style="width: 80px; height: 80px; font-size: 24px;">
                        <?= $model->getInitials() ?>
                    </div>
                    <div>
                        <h3 style="margin: 0 0 10px 0;"><?= safeHtml($authorName) ?></h3>
                        <div class="author-stats">
                            <strong>Книг в каталоге:</strong> <?= $booksCount ?>
                        </div>
                    </div>
                </div>

                <div class="mt-20">
                    <?php if (!Yii::$app->user->isGuest): ?>
                        <a href="<?= \yii\helpers\Url::to(['author/update', 'id' => $model->id]) ?>"
                           class="btn btn-primary">
                            <i class="fas fa-edit"></i> Редактировать
                        </a>

                        <!-- ВСЕГДА показываем кнопку удаления -->
                        <!-- Простая форма без CSRF-токена -->
                        <form action="<?= \yii\helpers\Url::to(['author/delete', 'id' => $model->id]) ?>"
                              method="post"
                              style="display: inline;"
                              onsubmit="return confirm('Вы уверены, что хотите удалить автора \'<?= addslashes($authorName) ?>\'? Все его книги останутся в каталоге, но без этого автора.')">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Удалить
                            </button>
                        </form>
                    <?php endif; ?>

                    <a href="<?= \yii\helpers\Url::to(['site/authors']) ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Назад к списку
                    </a>
                </div>
            </div>
        </div>

        <?php
        $authorBooks = $model->books;
        if (!empty($authorBooks)): ?>
            <div class="card">
                <h3 style="margin-bottom: 20px;">Книги автора</h3>
                <div class="books-container">
                    <?php foreach ($authorBooks as $book): ?>
                        <?php
                        $bookName = $book->book_name ?: 'Книга без названия';
                        ?>
                        <div class="book-card card">
                            <a href="<?= \yii\helpers\Url::to(['book/view', 'id' => $book->id]) ?>"
                               style="text-decoration: none; color: inherit;">
                                <img src="<?= $book->getCoverUrl() ?>"
                                     alt="<?= safeHtml($bookName) ?>"
                                     class="book-cover"
                                     onerror="this.src='<?= Yii::getAlias('@web') ?>/img/default_cover.jpg';">
                            </a>

                            <h3 class="book-title">
                                <a href="<?= \yii\helpers\Url::to(['book/view', 'id' => $book->id]) ?>"
                                   style="color: inherit; text-decoration: none;">
                                    <?= safeHtml($bookName) ?>
                                </a>
                            </h3>

                            <div class="book-meta">
                                <?php if (!empty($book->release_year)): ?>
                                    <span><strong>Год:</strong> <?= date('Y', strtotime($book->release_year)) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

<?php
$this->registerJs(<<<JS
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('delete-author-ajax') || 
        e.target.closest('.delete-author-ajax')) {
        
        e.preventDefault();
        const button = e.target.classList.contains('delete-author-ajax') ? 
                      e.target : e.target.closest('.delete-author-ajax');
        
        const authorId = button.getAttribute('data-id');
        const authorName = button.getAttribute('data-name');
        
        if (!confirm('Вы уверены, что хотите удалить автора "' + authorName + '"?')) {
            return;
        }
        
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        fetch('/author/delete?id=' + authorId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = '/site/authors';
            } else {
                alert(data.message);
                button.innerHTML = originalHtml;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при удалении автора');
            button.innerHTML = originalHtml;
            button.disabled = false;
        });
    }
});
JS);
?>