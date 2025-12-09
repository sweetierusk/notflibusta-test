<?php
/** @var yii\web\View $this */
/** @var app\models\Book $model */
/** @var app\models\Authors[] $authors */

$this->title = 'Добавить книгу';
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['site/books']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/i18n/ru.js');
?>

    <div class="book-create">
        <h1 class="page-title"><?= htmlspecialchars($this->title) ?></h1>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger">
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        <?php endif; ?>

        <div class="card">
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
                        'placeholder' => 'Выберите дату выпуска'
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
                'rows' => 5,
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
            ])->label('Обложка книги') ?>

            <div class="form-text mb-20">
                <small>Разрешенные форматы: JPG, PNG, GIF. Максимальный размер: 2MB</small>
            </div>

            <div class="form-group mt-20">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Сохранить
                </button>
                <a href="<?= \yii\helpers\Url::to(['site/books']) ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Отмена
                </a>
            </div>

            <?php \yii\widgets\ActiveForm::end(); ?>
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
JS);
?>