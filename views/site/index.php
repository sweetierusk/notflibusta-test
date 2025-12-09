<?php

/** @var yii\web\View $this */

$this->title = 'Главная';
?>

<div class="home-hero card">
    <h1>Добро пожаловать в мир литературы</h1>
    <p>Наш сайт представляет собой каталог книг различных жанров и авторов, и это точно не флибуста! Здесь вы найдете информацию о художественной литературе, научных работах, биографиях и многом другом, но к сожалению, не сможете скачать ни одной книги. Мы собрали для вас лучшие произведения мировой литературы с подробным описанием и информацией об авторах.</p>
    <p>Используйте наш каталог для поиска интересующих книг, знакомства с авторами или анализа статистики публикаций по годам.</p>

    <div class="action-buttons">
        <a href="<?= \yii\helpers\Url::to(['site/books']) ?>" class="btn btn-large btn-primary">
            <i class="fas fa-book-open"></i> Список книг
        </a>
        <a href="<?= \yii\helpers\Url::to(['site/authors']) ?>" class="btn btn-large btn-success">
            <i class="fas fa-user-pen"></i> Список авторов
        </a>
        <a href="<?= \yii\helpers\Url::to(['site/report']) ?>" class="btn btn-large btn-danger">
            <i class="fas fa-chart-bar"></i> Отчёт за год
        </a>
    </div>
</div>

<div class="card">
    <h2 class="page-title">О нашем каталоге</h2>
    <p>Наш каталог содержит более 5000 книг различных жанров и направлений. Мы постоянно обновляем нашу базу данных, добавляя новые произведения и авторов. Вы можете:</p>
    <ul style="padding-left: 20px; margin-top: 10px;">
        <li>Найти книги по интересующей вас тематике</li>
        <li>Познакомиться с биографиями авторов</li>
        <li>Узнать статистику публикаций по годам</li>
        <li>Подписаться на обновления от любимых авторов</li>
    </ul>
</div>

<div class="card mt-20">
    <h2 class="page-title">Последние поступления</h2>
    <div class="books-container" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));">
        <div class="book-card">
            <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=400&q=80" alt="Обложка книги" class="book-cover">
            <h3 class="book-title">Мастер и Маргарита</h3>
            <div class="book-authors"><strong>Автор:</strong> Михаил Булгаков</div>
            <p class="book-description">Культовый роман, сочетающий мистику, сатиру и философскую прозу.</p>
            <div class="book-meta">
                <span><strong>Год:</strong> 2020</span>
            </div>
        </div>

        <div class="book-card">
            <img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w-400&q=80" alt="Обложка книги" class="book-cover">
            <h3 class="book-title">Преступление и наказание</h3>
            <div class="book-authors"><strong>Автор:</strong> Фёдор Достоевский</div>
            <p class="book-description">Глубокое психологическое исследование преступления и раскаяния.</p>
            <div class="book-meta">
                <span><strong>Год:</strong> 2021</span>
            </div>
        </div>

        <div class="book-card">
            <img src="https://images.unsplash.com/photo-1592496431122-2349e0fbc666?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=400&q=80" alt="Обложка книги" class="book-cover">
            <h3 class="book-title">Гарри Поттер и философский камень</h3>
            <div class="book-authors"><strong>Автор:</strong> Джоан Роулинг</div>
            <p class="book-description">Первая книга знаменитой серии о юном волшебнике.</p>
            <div class="book-meta">
                <span><strong>Год:</strong> 2022</span>
            </div>
        </div>
    </div>
</div>