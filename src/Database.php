<?php

namespace Azulphp;

use PDO;
use PDOStatement;

class Database
{
    private PDO $connection;
    private false|PDOStatement $statement;

    public function __construct($config, $username = 'root', $password = '')
    {
        $dsn = "mysql:" . http_build_query(data: $config, arg_separator: ';');

        $this->connection = new PDO(
            $dsn,
            $username,
            $password,
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    /**
     * Build the query.
     *
     * @param  string  $query
     * @param  array  $params
     * @param  int|null  $limit
     * @param  int|null  $offset
     * @return $this
     */
    public function query(string $query, array $params = [], ?int $limit = null, ?int $offset = null): static
    {
        $limitStatement = $this->withLimit($query, $limit);
        $offsetStatement = $this->withOffset($query, $offset);

        $this->statement = $this->connection->prepare($query);

        $limitStatement();
        $offsetStatement();

        foreach ($params as $key => $value)
            $this->statement->bindValue(":$key", $value);

        $this->statement->execute();

        return $this;
    }

    /**
     * Get listing of records.
     *
     * @return bool|array
     */
    public function get(): bool|array
    {
       return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get the first record.
     *
     * @return mixed
     */
    public function first(): mixed
    {
       return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve the id of the inserted record.
     *
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Add Limit to the giving query.
     *
     * @param  string  $query
     * @param  int|null  $limit
     * @return callable
     */
    protected function withLimit(string &$query, ?int $limit = null): callable
    {
        if (!is_null($limit)) $query .= ' LIMIT :limit';

        return fn () => !is_null($limit)
            ? $this->statement->bindValue(':limit', $limit, PDO::PARAM_INT)
            : null;
    }

    /**
     * Add Offset to the giving query.
     *
     * @param  string  $query
     * @param  int|null  $offset
     * @return callable
     */
    protected function withOffset(string &$query, ?int $offset = null): callable
    {
        if (!is_null($offset)) $query .= ' OFFSET :offset';

        return fn () => !is_null($offset)
            ? $this->statement->bindValue(':offset', $offset, PDO::PARAM_INT)
            : null;
    }
}