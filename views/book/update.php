<?php
/** @var yii\web\View $this */
/** @var app\models\Book $model */
/** @var app\models\Authors[] $authors */

$this->title = 'Редактировать книгу: ' . $model->book_name;
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['site/books']];
$this->params['breadcrumbs'][] = ['label' => $model->book_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';

$this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/i18n/ru.js');
?>

    <div class="book-update">
        <h1 class="page-title"><?= htmlspecialchars($this->title) ?></h1>

        <div class="card mb-20">
            <div class="row">
                <div class="col-md-4">
                    <?php if ($model->img_link): ?>
                        <div class="mb-3">
                            <strong>Текущая обложка:</strong>
                            <img src="<?= $model->getCoverUrl() ?>"
                                 alt="<?= htmlspecialchars($model->book_name) ?>"
                                 class="img-fluid rounded mt-2"
                                 style="max-height: 200px; width: auto;">
                            <div class="mt-2 text-muted">
                                <small>Файл: <?= basename($model->img_link) ?></small>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-4" style="background: #f8f9fa; border-radius: 8px;">
                            <i class="fas fa-book" style="font-size: 48px; color: #6c757d;"></i>
                            <p class="mt-2 text-muted">Текущая обложка отсутствует</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <?php $form = \yii\widgets\ActiveForm::begin([
                        'id' => 'book-form',
                        'options' => ['class' => 'form-vertical', 'enctype' => 'multipart/form-data'],
                    ]); ?>

                    <?= $form->field($model, 'book_name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Введите название книги',
                        'class' => 'form-control'
                    ])->label('Название книги *') ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'release_year')->textInput([
                                'type' => 'date',
                                'class' => 'form-control datepicker',
                                'value' => $model->release_year
                            ])->label('Год выпуска *') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'isbn')->textInput([
                                'maxlength' => true,
                                'placeholder' => 'Введите ISBN',
                                'class' => 'form-control'
                            ])->label('ISBN *') ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'description')->textarea([
                        'rows' => 4,
                        'maxlength' => 512,
                        'placeholder' => 'Введите описание книги',
                        'class' => 'form-control'
                    ])->label('Описание *') ?>

                    <?= $form->field($model, 'authorIds')->dropDownList(
                        \yii\helpers\ArrayHelper::map($authors, 'id', 'full_name'),
                        [
                            'multiple' => true,
                            'class' => 'form-control select2',
                            'prompt' => 'Выберите автора(ов)'
                        ]
                    )->label('Авторы') ?>

                    <?= $form->field($model, 'coverFile')->fileInput([
                        'class' => 'form-control-file'
                    ])->label('Новая обложка') ?>

                    <div class="form-text mb-20">
                        <small>Оставьте пустым, чтобы сохранить текущую обложку</small>
                    </div>

                    <div class="form-group mt-20">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Сохранить изменения
                        </button>
                        <a href="<?= \yii\helpers\Url::to(['site/books']) ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Отмена
                        </a>
                        <button type="button"
                                class="btn btn-danger float-right"
                                onclick="deleteBook(<?= $model->id ?>)">
                            <i class="fas fa-trash"></i> Удалить книгу
                        </button>
                    </div>

                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

<?php
$this->registerJs(<<<JS
// Инициализация DatePicker
flatpickr('.datepicker', {
    dateFormat: 'Y-m-d',
    locale: 'ru',
    maxDate: 'today'
});

// Инициализация Select2
$('.select2').select2({
    language: 'ru',
    placeholder: 'Выберите автора(ов)',
    allowClear: true,
    width: '100%'
});

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