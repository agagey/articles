<?php

namespace controller;

use model\Article;

class Controller
{
    private $viewPath = 'view/';
    private $uploaddir = 'photo/';
    private $title = 'Заголовок';

    /**
     * Главная страница, список статей
     *
     * @return string
     */
    public function index()
    {
        $this->title = 'Список статей';
        $param['title'] = $this->title;
        $param['article_list'] = '';

        $articles = new Article();
        foreach ($articles->getList() as $article)
        $param['article_list'] .= $this->parseContent('_block_article_list', $article);

        return $this->parseContent('list', $param);
    }

    /**
     * Шаблонизация
     *
     * @param string $filename
     * @param array $params
     * @return string
     */
    private function parseContent(string $filename, array $params) : string
    {
        $content = file_get_contents($this->viewPath . $filename . '.html');
        foreach ($params as $name => $param) {
            $content = str_replace('{#' . $name . '#}', $param, $content);
        }

        return $content;
    }

    /**
     * Обёртка для вывода шаблона
     * @param string $content
     * @return string
     */
    public function makeView(string $content = '') : string
    {
        $header = $this->parseContent('header', ['title' => $this->title]);
        $footer = file_get_contents($this->viewPath . 'footer.html');

        return $header . $content . $footer;
    }

    /**
     * Удалить запись
     */
    public function removeArticle()
    {
        $id = $_REQUEST['id'];

        if (is_numeric($id)) {
            $article = new Article();
            if ($article->removeArticle($id)) {
                http_response_code(200);
            } else {
                http_response_code(404);
            }
        } else {
            http_response_code(404);
        }
    }

    /**
     * Страница просмотра статьи
     *
     * @return string
     */
    public function viewArticle()
    {
        $id = $_REQUEST['id'];
        $param = [];

        if (is_numeric($id)) {
            $articles = new Article();
            $article = $articles->getArticle($id);

            $this->title = 'Просмотр';
            $param['title'] = $this->title;
            $param['id'] = $article['id'];
            $param['article_name'] = $article['name'];
            $param['article_text'] = $article['text'];
            $param['article_updated_at'] = $article['updated_at'];
            $param['article_photo'] = $article['photo'];
        }

        return $this->parseContent('view', $param);
    }

    /**
     * Страница редактирования статьи
     *
     * @return string
     */
    public function editArticle()
    {
        $id = $_REQUEST['id'];
        $param = [];

        if (is_numeric($id)) {
            $articles = new Article();
            $article = $articles->getArticle($id);

            $this->title = 'Редактирование статьи';
            $param['title'] = $this->title;
            $param['id'] = $article['id'];
            $param['article_name'] = $article['name'];
            $param['article_text'] = $article['text'];
            $param['article_updated_at'] = $article['updated_at'];
            $param['photo'] = $article['photo'];
        }

        return $this->parseContent('edit', $param);
    }

    /**
     * Сохранить изменённую статью
     *
     * @return string
     */
    public function saveArticle()
    {
        if (isset($_FILES['photo'])) {
            $paramSave['photo'] = md5($_FILES['photo']['name']) . end(explode(".", $_FILES['photo']['name']));
            $uploadFile = $this->uploaddir . $paramSave['photo'];

            move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile);
        } else {
            $paramSave['photo'] = '';
        }

        $paramSave['id'] = $_POST['id'];
        $paramSave['name'] = $_POST['name'];
        $paramSave['text'] = $_POST['text'];
        $paramSave['updated_at'] = time();

        $articles = new Article();
        $articles->saveArticle($paramSave);

        return $this->index();
    }

    /**
     * Страница содания статьи
     *
     * @return string
     */
    public function createArticle()
    {
        // Если есть данные для записи - создаём новую статью
        if (isset($_POST['name'])) {

            if (isset($_FILES['photo'])) {
                $paramSave['photo'] = md5($_FILES['photo']['name']) . end(explode(".", $_FILES['photo']['name']));
                $uploadFile = $this->uploaddir . $paramSave['photo'];

                move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile);
            } else {
                $paramSave['photo'] = '';
            }

            $paramSave['name'] = $_POST['name'];
            $paramSave['text'] = $_POST['text'];
            $paramSave['updated_at'] = time();

            $articles = new Article();
            $articles->createArticle($paramSave);

            return $this->index();
        } else {
            // иначе выводим форму
            $this->title = 'Создание статьи';
            $param['title'] = $this->title;

            return $this->parseContent('new', $param);
        }
    }
}