<?php

namespace model;

use mysqli;

class Article
{
    private $mysqli = null;
    private $tableName = 'article';
    private $photoPath = 'photo/';

    /**
     * Инициализация работы с БД
     */
    function __construct()
    {
        include('config.php');

        // соедения с БД
        $this->mysqli = new mysqli ($host, $user, $pass, $dbname);

        // вывод ошибки соединения
        if ($this->mysqli->connect_error) {
            die ('Ошибка подключения к БД: ' . $this->mysqli->connect_error);
        }
    }

    /**
     * Закрыть соединение
     */
    function __destruct()
    {
        $this->mysqli->close();
    }

    /**
     * Получить список всех статей
     */
    function getList()
    {
        $list = [];

        if ($result = $this->mysqli->query('SELECT * FROM ' . $this->tableName . ' ORDER BY id ASC;')) {
            while ($row = $result->fetch_assoc()) {
                $list[] = [
                    'id' => $row['id'],
                    'name' => iconv("UTF-8","cp1251", $row['name']),
                    'text' => $row['text'],
                    'updated_at' => date('D, d M Y H:i:s', $row['updated_at']),
                    'photo' => $row['photo'],
                ];
            }
        }

        return $list;
    }

    /**
     * Удалить запись
     */
    function removeArticle($id)
    {
        if (is_numeric($id)) {
            $result = $this->mysqli->query('DELETE FROM ' . $this->tableName . ' WHERE id = ' . $id);

            if ($result) {
                return true;
            }
        }

        return false;
    }

    /**
     * Получить статью по ID
     *
     * @param null $id
     * @return array
     */
    public function getArticle($id = null) : array
    {
        $article = [];

        if (is_numeric($id)) {
            if ($result = $this->mysqli->query('SELECT * FROM ' . $this->tableName . ' WHERE id = ' . $id . ' ORDER BY id ASC LIMIT 1;')) {
                while ($row = $result->fetch_assoc()) {
                    $article = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'text' => $row['text'],
                        'updated_at' => date('D, d M Y H:i:s', $row['updated_at']),
                        'photo' => $this->photoPath . $row['photo'],
                    ];
                }
            }
        }

        return $article;
    }

    /**
     * Обновить статью в БД
     *
     * @param array $param
     * @return bool|\mysqli_result
     */
    public function saveArticle(array $param)
    {
        $result = $this->mysqli->query('UPDATE ' . $this->tableName . ' SET
            name = "' . $param['name'] . '",
            text = "' . $param['text'] . '",
            updated_at = ' . $param['updated_at'] . ',
            photo = "' . $param['photo'] . '"
        WHERE id = ' . $param['id']);

        return $result;
    }

    /**
     * Создать новую запись статьи в БД
     *
     * @param array $param
     * @return bool|\mysqli_result
     */
    public function createArticle(array $param)
    {
        $result = $this->mysqli->query('INSERT INTO ' . $this->tableName . ' VALUES(
            NULL,
            "' . $param['name'] . '",
            "' . $param['text'] . '",
            ' . $param['updated_at'] . ',
            "' . $param['photo'] . '"
        )');

        return $result;
    }
}