<?php
/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

$this->title = 'Авторизация';
?>

<div class="login-container">
    <div class="card">
        <h1 class="page-title">Авторизация</h1>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= Yii::$app->session->getFlash('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= Yii::$app->session->getFlash('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('info')): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= Yii::$app->session->getFlash('info') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <p class="mb-20">Введите ваши учетные данные</p>

        <?php $form = \yii\widgets\ActiveForm::begin([
            'id' => 'login-form',
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
            'placeholder' => 'Введите логин'
        ]) ?>

        <?= $form->field($model, 'password')->passwordInput([
            'placeholder' => 'Введите пароль'
        ]) ?>

        <div class="form-check">
            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"form-check\">{input} {label}</div>\n{error}",
            ]) ?>
        </div>

        <div class="login-actions">
            <div>
                <a href="<?= \yii\helpers\Url::to(['auth/signup']) ?>" class="register-link">
                    <i class="fas fa-user-plus"></i> Зарегистрироваться
                </a>
            </div>
            <?= \yii\helpers\Html::submitButton('Войти', [
                'class' => 'btn btn-primary',
                'name' => 'login-button'
            ]) ?>
        </div>

        <?php \yii\widgets\ActiveForm::end(); ?>

        <div class="card mt-20">
            <h3>Демо-доступ</h3>
            <p>Для тестирования используйте:</p>
            <ul style="padding-left: 20px; margin-top: 10px;">
                <li><strong>Логин:</strong> admin</li>
                <li><strong>Пароль:</strong> admin123</li>
            </ul>
        </div>
    </div>
</div>