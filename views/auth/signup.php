<?php
/** @var yii\web\View $this */
/** @var app\models\SignupForm $model */

$this->title = 'Регистрация';
?>

<div class="login-container">
    <div class="card">
        <h1 class="page-title">Регистрация</h1>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= Yii::$app->session->getFlash('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <p class="mb-20">Создайте учетную запись для доступа к функциям сайта</p>

        <?php $form = \yii\widgets\ActiveForm::begin([
            'id' => 'signup-form',
            'options' => ['class' => 'login-form'],
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'labelOptions' => ['class' => 'form-label'],
                'inputOptions' => ['class' => 'form-control'],
                'errorOptions' => ['class' => 'text-error'],
            ],
        ]); ?>

        <?php if ($model->hasErrors()): ?>
            <div class="form-errors">
                <ul>
                    <?php foreach ($model->getErrors() as $errors): ?>
                        <?php foreach ($errors as $error): ?>
                            <li><?= \yii\helpers\Html::encode($error) ?></li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?= $form->field($model, 'username')->textInput([
            'autofocus' => true,
            'placeholder' => 'Придумайте логин'
        ]) ?>

        <?= $form->field($model, 'password')->passwordInput([
            'placeholder' => 'Придумайте пароль'
        ]) ?>

        <?= $form->field($model, 'password_repeat')->passwordInput([
            'placeholder' => 'Повторите пароль'
        ]) ?>

        <div class="form-group mt-20">
            <?= \yii\helpers\Html::submitButton('Зарегистрироваться', [
                'class' => 'btn btn-primary',
                'name' => 'signup-button',
                'style' => 'width: 100%;'
            ]) ?>
        </div>

        <?php \yii\widgets\ActiveForm::end(); ?>

        <div class="text-center mt-20">
            <p>Уже есть аккаунт? <a href="<?= \yii\helpers\Url::to(['auth/login']) ?>">Войти</a></p>
        </div>
    </div>
</div>