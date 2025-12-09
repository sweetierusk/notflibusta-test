<?php
/** @var yii\web\View $this */
/** @var app\models\Authors $model */

$this->title = 'Добавить автора';
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['site/authors']];
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="author-create">
    <h1 class="page-title"><?= htmlspecialchars($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <?php $form = \yii\widgets\ActiveForm::begin([
            'id' => 'author-form',
            'options' => ['class' => 'form-vertical'],
        ]); ?>

        <?= $form->field($model, 'full_name')->textInput([
            'maxlength' => true,
            'placeholder' => 'Введите ФИО автора',
            'class' => 'form-control'
        ])->label('ФИО автора *') ?>

        <div class="form-group mt-20">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Сохранить
            </button>
            <a href="<?= \yii\helpers\Url::to(['site/authors']) ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Отмена
            </a>
        </div>

        <?php \yii\widgets\ActiveForm::end(); ?>
    </div>
    </div><?php
