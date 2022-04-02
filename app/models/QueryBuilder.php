<?php
namespace App\Models;

use Aura\SqlQuery\QueryFactory;
use PDO;

class QueryBuilder
{
    private $pdo;
    private $queryFactory;


    public function __construct(PDO $pdo, QueryFactory $queryFactory)
    {
        $this->pdo = $pdo;
        $this->queryFactory = $queryFactory;
    }

    /**
     * Получаем все записи из переданной таблицы
     *
     * @param $table string
     * @return array
     */

    public function selectAll($table) {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from($table);
        $sth = $this->prepareAndExecute($select);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Получаем запись по id
     *
     * @param $table string
     * @param $id int
     * @return array
     */

    public function selectOne($id, $table) {
        $select = $this->queryFactory->newSelect();
        $select
            ->cols(['*'])
            ->from($table)
            ->where('id = :id')
            ->bindValue('id', $id);
        $sth = $this->prepareAndExecute($select);
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Получаем запись по id
     *
     * @param $table string
     * @param $id int
     * @return array
     */

    public function selectOneByEmail($email, $table) {
        $select = $this->queryFactory->newSelect();
        $select
            ->cols(['*'])
            ->from($table)
            ->where('email = :email')
            ->bindValue('email', $email);
        $sth = $this->prepareAndExecute($select);
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Редактируем запись по id
     *
     * @param $table string
     * @param $id int
     * @param $data array
     */

    public function update($id, $data, $table) {
        $update = $this->queryFactory->newUpdate();
        $update
            ->table($table)
            ->cols($data)
            ->where('id = :id')
            ->bindValue('id', $id);
        $sth = $this->prepareAndExecute($update);
    }

    /**
     * Вставляем запись в таблицу
     *
     * @param $table string
     * @param $data array
     */

    public function insert($data, $table) {
        $insert = $this->queryFactory->newInsert();
        $insert
            ->into($table)
            ->cols($data);
        $sth = $this->prepareAndExecute($insert);
    }

    /**
     * Удаляем запись из таблицы
     *
     * @param $table string
     * @param $id int
     */

    public function delete($id, $table)
    {
        $delete = $this->queryFactory->newDelete();
        $delete
            ->from($table)
            ->where('id = :id')
            ->bindValue('id', $id);
        $sth = $this->prepareAndExecute($delete);
    }

    /**
     * Подготавливаем и исполняем
     *
     * @param $command object
     * @return object
     */

    public function prepareAndExecute($command) {
        $sth = $this->pdo->prepare($command->getStatement());
        $sth->execute($command->getBindValues());
        return $sth;
    }

}