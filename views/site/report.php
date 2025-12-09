<?php
/** @var yii\web\View $this */
/** @var array $reportData Массив с данными отчета */
/** @var int $year Выбранный год */
/** @var int $totalAuthors Общее количество авторов в отчете */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Книжный каталог - Отчет по авторам';
$this->params['breadcrumbs'][] = $this->title;

// Определяем последние 10 лет для выбора
$currentYear = date('Y');
$years = [];
for ($i = 0; $i < 10; $i++) {
    $years[$currentYear - $i] = $currentYear - $i;
}

// Если год не указан, используем текущий
$selectedYear = $year ?? $currentYear;
?>

    <div class="report-index">
        <h1 class="page-title"><?= Html::encode($this->title) ?></h1>

        <div class="card">
            <div class="card-body">
                <!-- Форма выбора года -->
                <div class="report-controls mb-4">
                    <form method="get" action="<?= Url::to(['site/report']) ?>" id="year-form">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="year-select-container">
                                    <label for="year-select" class="form-label">Выберите год для отчёта:</label>
                                    <select name="year" id="year-select" class="form-select year-select">
                                        <?php foreach ($years as $yearOption): ?>
                                            <option value="<?= $yearOption ?>" <?= $yearOption == $selectedYear ? 'selected' : '' ?>>
                                                <?= $yearOption ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle"></i>
                                    Таблица показывает 10 авторов с наибольшим количеством книг, выпущенных в выбранном году
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Таблица отчета -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover report-table">
                        <thead class="table-dark">
                        <tr>
                            <th width="60">#</th>
                            <th>Автор</th>
                            <th width="150">Количество книг</th>
                            <th width="100">Действия</th>
                        </tr>
                        </thead>
                        <tbody id="report-table-body">
                        <?php if (empty($reportData)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Нет данных за <?= Html::encode($selectedYear) ?> год
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reportData as $index => $author): ?>
                                <tr>
                                    <td>
                                        <span class="rank <?= $index < 3 ? 'rank-' . ($index + 1) : '' ?>">
                                            <?= $index + 1 ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="author-info">
                                            <div class="author-avatar">
                                                <?= Html::encode($author['initials']) ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?= Html::encode($author['name']) ?></h6>
                                                <small class="text-muted">ID: <?= $author['id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success book-count-badge">
                                            <i class="fas fa-book"></i> <?= $author['book_count'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= Url::to(['author/view', 'id' => $author['id']]) ?>"
                                               class="btn btn-outline-info"
                                               title="Просмотр">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= Url::to(['author/update', 'id' => $author['id']]) ?>"
                                               class="btn btn-outline-warning"
                                               title="Редактировать">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Информационная панель -->
                <div id="report-info" class="mt-3">
                    <div class="alert alert-light">
                        <i class="fas fa-chart-bar"></i>
                        <span id="report-summary">
                        <?php if (empty($reportData)): ?>
                            В <strong><?= Html::encode($selectedYear) ?></strong> году не было выпущено книг
                        <?php else: ?>
                            В <strong><?= Html::encode($selectedYear) ?></strong> году <strong><?= count($reportData) ?></strong> авторов выпустили книги
                        <?php endif; ?>
                    </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
// Минимальный JavaScript для отправки формы при изменении года
$this->registerJs(<<<JS
$(document).ready(function() {
    // Автоматическая отправка формы при изменении года
    $('#year-select').change(function() {
        $('#year-form').submit();
    });
    
    // Добавляем небольшой визуальный эффект при загрузке
    $('#year-form').submit(function() {
        $('#report-table-body').html(`
            <tr>
                <td colspan="4" class="text-center text-muted">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    Загрузка данных...
                </td>
            </tr>
        `);
    });
});
JS);
?>