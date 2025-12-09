<?php
/** @var yii\web\View $this */

/** @var app\models\Authors[] $authors */

use yii\widgets\ActiveForm;

$this->title = 'Книжный каталог - Авторы';

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
                                    <?= htmlspecialchars($authorName) ?>
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
                    <!-- КНОПКА ПОДПИСКИ -->
                    <button class="subscribe-btn"
                            data-author-id="<?= $author->id ?>"
                            data-author-name="<?= htmlspecialchars($authorName) ?>">
                        <i class="fas fa-bell"></i> Подписаться
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php
// JavaScript для работы с кнопками подписки
$subscribeUrl = \yii\helpers\Url::to(['subscribe/subscribe']);

$this->registerJs(<<<JS
$(document).on('click', '.subscribe-btn', function(e) {
    e.preventDefault();
    
    var authorId = $(this).data('author-id');
    var authorName = $(this).data('author-name');
    
    console.log('Подписка на автора:', authorName, 'ID:', authorId);
    
    // Заполняем поля в попапе
    $('#author-name').val(authorName);
    
    // Удаляем старое поле author-id если есть
    $('#author-id').remove();
    
    // Создаем новое скрытое поле
    var authorIdInput = $('<input>')
        .attr({
            type: 'hidden',
            id: 'author-id',
            name: 'author_id'
        })
        .val(authorId);
    
    $('#subscribe-form').prepend(authorIdInput);
    
    // Показываем попап с анимацией
    $('#subscribe-popup').fadeIn(300);
    
    // Фокус на поле телефона
    setTimeout(function() {
        $('#phone').focus();
    }, 100);
});

// Отправка формы подписки
$(document).on('submit', '#subscribe-form', function(e) {
    e.preventDefault();
    
    var authorId = $('#author-id').val();
    var phone = $('#phone').val();
    
    console.log('Отправка данных:', {author_id: authorId, phone: phone});
    
    if (!authorId || !phone) {
        alert('Заполните все поля');
        return false;
    }
    
    var submitBtn = $(this).find('button[type="submit"]');
    var originalText = submitBtn.html();
    
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Отправка...');
    submitBtn.prop('disabled', true);
    
    // Отправляем данные на сервер
    $.ajax({
        url: '{$subscribeUrl}',
        method: 'POST',
        data: {
            author_id: authorId,
            phone: phone
        },
        dataType: 'json',
        success: function(response) {
            console.log('Ответ сервера:', response);
            if (response.success) {
                alert(response.message);
                $('#subscribe-popup').fadeOut(300);
                $('#subscribe-form')[0].reset();
                $('#author-id').remove();
            } else {
                alert('Ошибка: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error, 'Status:', status);
            
            if (xhr.responseText) {
                // Проверяем, что вернул сервер
                if (xhr.responseText.indexOf('<!DOCTYPE html>') !== -1) {
                    console.error('Server returned HTML instead of JSON');
                    alert('Ошибка: проверьте контроллер SubscribeController и маршрутизацию');
                } else {
                    console.error('Response:', xhr.responseText.substring(0, 200));
                }
            }
            
            alert('Ошибка сети или сервера');
        },
        complete: function() {
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        }
    });
});
JS
);

// JavaScript для поиска авторов
$this->registerJs(<<<JS
$('#author-search').on('input', function() {
    var searchTerm = $(this).val().toLowerCase();
    var visibleCount = 0;
    
    $('.author-card').each(function() {
        var authorName = $(this).find('.author-name').text().toLowerCase();
        
        if (authorName.indexOf(searchTerm) !== -1) {
            $(this).show();
            visibleCount++;
        } else {
            $(this).hide();
        }
    });
    
    // Обновляем счетчик
    var countElement = $('p:has(strong)').first();
    if (searchTerm) {
        countElement.html('Найдено авторов: <strong>' + visibleCount + '</strong>');
    } else {
        countElement.html('Всего авторов в каталоге: <strong>' + $('.author-card').length + '</strong>');
    }
});
JS
);

// JavaScript для обработки удаления авторов
$this->registerJs(<<<JS
$(document).on('submit', 'form[action*="author/delete"]', function(e) {
    var authorName = $(this).closest('.author-card').find('.author-name').text().trim() || 'автора';
    
    if (!confirm('Вы уверены, что хотите удалить автора "' + authorName + '"?')) {
        e.preventDefault();
        return false;
    }
    
    // Индикатор загрузки
    var submitBtn = $(this).find('button[type="submit"]');
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i>');
    submitBtn.prop('disabled', true);
});
JS
);
?>