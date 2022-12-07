<?php

namespace src;

use PDO;
use Exception;
use src\Enums\QueryTypeEnum;
use src\Tools\ValidateTools;
use src\Exceptions\DatabaseExceptions\QueryTypeException;
use src\Exceptions\DatabaseExceptions\DatabaseConnectException;

class Database
{
    private ?\PDO $conn;

    private function connectDb(): void
    {
        try {
            $this->conn = new \PDO(
                'mysql:'
                . 'host=' . DATABASE_SERVER . ';'
                . 'dbname=' . DATABASE_DATABASE . ';'
                . 'charset=' . DATABASE_CHARSET,
                DATABASE_USER,
                DATABASE_PASS,
                array(PDO::ATTR_PERSISTENT => true)
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } catch (Exception $e) {
            throw new DatabaseConnectException($e->getMessage());
        }
    }

    public function select(string $query, array $params = null): array
    {
        try {
            $query = trim($query);
            ValidateTools::validateQueryType(QueryTypeEnum::SELECT, $query);
            $this->connectDb();
            $conn = $this->conn->prepare($query);
            $this->executeQueryConn($conn, $params);
            $results = $conn->fetchAll(PDO::FETCH_ASSOC);
            $this->disconnectDb();
            return $results;
        }catch (Exception $e) {
            throw new QueryTypeException ($e->getMessage());
        }
    }

    public function insert(string $query, $params = null): void
    {
        try {
            $query = trim($query);
            ValidateTools::validateQueryType(QueryTypeEnum::INSERT, $query);
            $this->connectDb();
            $conn = $this->conn->prepare($query);
            $this->executeQueryConn($conn, $params);
            $this->disconnectDb();
        } catch (Exception $e) {
            throw new QueryTypeException ($e->getMessage());
        }
    }

    public function update(string $query, array $params = []): void
    {
        try {
            $query = trim($query);
            ValidateTools::validateQueryType(QueryTypeEnum::UPDATE, $query);
            $this->connectDb();
            $conn = $this->conn->prepare($query);
            $this->executeQueryConn($conn, $params);
            $this->disconnectDb();
        } catch (Exception $e) {
            throw new QueryTypeException ($e->getMessage());
        }
    }

    public function executeQueryConn($conn, $params)
    {
        if ($params) {
            return $conn->execute($params);
        }
        return $conn->execute();
    }

    private function disconnectDb(): void
    {
        $this->conn = null;
    }
}