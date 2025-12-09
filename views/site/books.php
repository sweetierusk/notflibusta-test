<?php
/** @var yii\web\View $this */
/** @var app\models\Book[] $books */

use yii\widgets\ActiveForm;

$this->title = 'Книжный каталог - Книги';

// Функция для безопасного вывода
function safeHtml($value, $default = '') {
    return $value !== null ? htmlspecialchars((string)$value) : $default;
}
?>

    <h1 class="page-title">Каталог книг</h1>

    <div class="card mb-20">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p>Всего книг в каталоге: <strong><?= count($books) ?></strong></p>
                <?php if (!Yii::$app->user->isGuest): ?>
                    <p class="text-muted" style="margin-top: 5px;">
                        <i class="fas fa-user-check"></i> Вы авторизованы как: <strong><?= Yii::$app->user->identity->username ?? 'Пользователь' ?></strong>
                    </p>
                <?php endif; ?>
            </div>

            <div style="display: flex; gap: 10px; align-items: center;">
                <div class="search-box">
                    <input type="text" placeholder="Поиск книг..." id="book-search">
                    <i class="fas fa-search"></i>
                </div>

                <?php if (!Yii::$app->user->isGuest): ?>
                    <a href="<?= \yii\helpers\Url::to(['book/create']) ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Добавить книгу
                    </a>
                <?php else: ?>
                    <a href="<?= \yii\helpers\Url::to(['auth/login']) ?>" class="btn btn-outline-primary"
                       title="Для добавления книг необходимо авторизоваться">
                        <i class="fas fa-sign-in-alt"></i> Войти для добавления
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="books-container" id="books-container">
        <?php if (empty($books)): ?>
            <div class="card">
                <p style="text-align: center; padding: 40px; color: #7f8c8d;">
                    <i class="fas fa-book" style="font-size: 48px; margin-bottom: 20px; display: block;"></i>
                    В каталоге пока нет книг
                    <?php if (!Yii::$app->user->isGuest): ?>
                        <br>
                        <a href="<?= \yii\helpers\Url::to(['book/create']) ?>" class="btn btn-success mt-20">
                            <i class="fas fa-plus"></i> Добавить первую книгу
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
            <?php foreach ($books as $book): ?>
                <?php
                // Подготавливаем данные с проверкой на null
                $bookName = $book->book_name ?: 'Книга без названия';
                $authorsString = $book->getAuthorsString() ?: 'Автор не указан';
                $description = $book->description ?: 'Описание отсутствует';
                $trimmedDescription = mb_strimwidth($description, 0, 200, '...');
                ?>

                <div class="book-card card" id="book-<?= $book->id ?>">
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

                    <div class="book-authors">
                        <strong>Автор:</strong> <?= safeHtml($authorsString) ?>
                    </div>

                    <p class="book-description">
                        <?= safeHtml($trimmedDescription) ?>
                    </p>

                    <div class="book-meta">
                        <?php if (!empty($book->isbn)): ?>
                            <span><strong>ISBN:</strong> <?= safeHtml($book->isbn) ?></span>
                        <?php endif; ?>

                        <?php if (!empty($book->release_year)): ?>
                            <span><strong>Год:</strong> <?= date('Y', strtotime($book->release_year)) ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if (!Yii::$app->user->isGuest): ?>
                        <div class="book-actions mt-10" style="display: flex; gap: 10px; border-top: 1px solid #eee; padding-top: 10px;">
                            <a href="<?= \yii\helpers\Url::to(['book/update', 'id' => $book->id]) ?>"
                               class="btn btn-sm btn-outline-primary" style="flex: 1;">
                                <i class="fas fa-edit"></i> Редактировать
                            </a>

                            <?php $form = ActiveForm::begin([
                                'action' => ['book/delete', 'id' => $book->id],
                                'method' => 'post',
                                'options' => [
                                    'class' => 'form-inline',
                                    'style' => 'flex: 1; margin: 0;',
                                    'onsubmit' => 'return confirm("Вы уверены, что хотите удалить книгу \\"' . addslashes($bookName) . '\\"?")'
                                ]
                            ]); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger" style="width: 100%;">
                                <i class="fas fa-trash"></i> Удалить
                            </button>
                            <?php ActiveForm::end(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php
// Для AJAX удаления (альтернативный вариант)
$this->registerJs(<<<JS
// Удаление книги через AJAX (если форма не работает)
document.addEventListener('click', function(e) {
    // Обработка кнопок удаления (если они остались)
    const deleteBtn = e.target.closest('.delete-book-ajax');
    if (deleteBtn) {
        e.preventDefault();
        
        const bookId = deleteBtn.getAttribute('data-id');
        const bookName = deleteBtn.getAttribute('data-name');
        const card = deleteBtn.closest('.book-card');
        
        if (!confirm('Вы уверены, что хотите удалить книгу "' + bookName + '"?')) {
            return;
        }
        
        const originalHtml = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        deleteBtn.disabled = true;
        
        // Отправляем DELETE запрос
        fetch('/book/delete?id=' + bookId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]') ? 
                               document.querySelector('meta[name="csrf-token"]').content : ''
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Плавно скрываем карточку
                if (card) {
                    card.style.transition = 'opacity 0.3s, transform 0.3s';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    
                    setTimeout(() => {
                        card.remove();
                        // Обновляем счетчик книг
                        updateBooksCount();
                    }, 300);
                } else {
                    // Если нет карточки, перезагружаем страницу
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Ошибка при удалении книги');
                deleteBtn.innerHTML = originalHtml;
                deleteBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка сети при удалении книги');
            deleteBtn.innerHTML = originalHtml;
            deleteBtn.disabled = false;
        });
    }
});

// Функция обновления счетчика книг
function updateBooksCount() {
    const books = document.querySelectorAll('.book-card');
    const countElement = document.querySelector('p:has(strong)');
    if (countElement) {
        countElement.innerHTML = 'Всего книг в каталоге: <strong>' + books.length + '</strong>';
    }
}

// Поиск книг на клиенте
const bookSearch = document.getElementById('book-search');
if (bookSearch) {
    bookSearch.addEventListener('input', function(e) {
        const searchTerm = this.value.toLowerCase().trim();
        const bookCards = document.querySelectorAll('.book-card');
        let visibleCount = 0;
        
        bookCards.forEach(card => {
            const title = card.querySelector('.book-title').textContent.toLowerCase();
            const description = card.querySelector('.book-description').textContent.toLowerCase();
            const authors = card.querySelector('.book-authors').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || description.includes(searchTerm) || authors.includes(searchTerm)) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Обновляем счетчик при поиске
        const countElement = document.querySelector('p:has(strong)');
        if (countElement && searchTerm) {
            countElement.innerHTML = 'Найдено книг: <strong>' + visibleCount + '</strong>';
        } else if (countElement) {
            countElement.innerHTML = 'Всего книг в каталоге: <strong>' + bookCards.length + '</strong>';
        }
    });
}

// Обработчик для ссылок с data-method (если используется)
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