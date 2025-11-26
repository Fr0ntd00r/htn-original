<?php
/**
 * Lightweight compatibility layer that provides the old mysql_* API on top of PDO.
 * This keeps the existing codebase working on PHP 8.3 while enabling
 * connection handling with modern MariaDB/MySQL drivers.
 */

class LegacyMySQL
{
    public static ?\PDO $pdo = null;
    public static string $lastError = '';
    public static ?string $currentDb = null;
    public static string $host = '127.0.0.1';
    public static string $username = 'root';
    public static string $password = '';
    public static ?int $port = 3306;
    public static string $charset = 'utf8mb4';

    public static function configure(
        string $host,
        string $username,
        string $password,
        ?int $port = 3306,
        string $charset = 'utf8mb4'
    ): void {
        self::$host = $host;
        self::$username = $username;
        self::$password = $password;
        self::$port = $port;
        self::$charset = $charset;
    }

    public static function connect(?string $dbName = null): ?\PDO
    {
        if (self::$pdo !== null && self::$currentDb === $dbName) {
            return self::$pdo;
        }

        $dsn = 'mysql:host=' . self::$host;
        if (self::$port !== null) {
            $dsn .= ';port=' . self::$port;
        }
        if (!empty($dbName)) {
            $dsn .= ';dbname=' . $dbName;
        }
        $dsn .= ';charset=' . self::$charset;

        try {
            self::$pdo = new \PDO(
                $dsn,
                self::$username,
                self::$password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            self::$currentDb = $dbName;
            self::$lastError = '';
        } catch (\PDOException $e) {
            self::$lastError = $e->getMessage();
            self::$pdo = null;
        }

        return self::$pdo;
    }
}

function mysql_connect(?string $host = null, ?string $username = null, ?string $password = null)
{
    LegacyMySQL::configure(
        $host ?? LegacyMySQL::$host,
        $username ?? LegacyMySQL::$username,
        $password ?? LegacyMySQL::$password,
        LegacyMySQL::$port,
        LegacyMySQL::$charset
    );

    return LegacyMySQL::connect(LegacyMySQL::$currentDb);
}

function mysql_select_db(string $database)
{
    return LegacyMySQL::connect($database) !== null;
}

function mysql_db_query(string $database, string $query)
{
    if (!mysql_select_db($database)) {
        return false;
    }

    return mysql_query($query);
}

function mysql_query(string $query)
{
    $pdo = LegacyMySQL::connect(LegacyMySQL::$currentDb);
    if ($pdo === null) {
        return false;
    }

    try {
        return $pdo->query($query);
    } catch (\PDOException $e) {
        LegacyMySQL::$lastError = $e->getMessage();
        return false;
    }
}

function mysql_error(): string
{
    return LegacyMySQL::$lastError;
}

function mysql_num_rows($result): int
{
    if ($result instanceof \PDOStatement) {
        return $result->rowCount();
    }

    return 0;
}

function mysql_fetch_assoc($result)
{
    if ($result instanceof \PDOStatement) {
        return $result->fetch(\PDO::FETCH_ASSOC);
    }

    return false;
}

function mysql_fetch_array($result)
{
    if ($result instanceof \PDOStatement) {
        return $result->fetch(\PDO::FETCH_BOTH);
    }

    return false;
}

function mysql_result($result, int $row, $field)
{
    if (!($result instanceof \PDOStatement)) {
        return false;
    }

    $data = $result->fetchAll(\PDO::FETCH_ASSOC);
    return $data[$row][$field] ?? false;
}

function mysql_insert_id()
{
    $pdo = LegacyMySQL::connect(LegacyMySQL::$currentDb);
    return $pdo ? $pdo->lastInsertId() : false;
}

function mysql_escape_string($string): string
{
    $pdo = LegacyMySQL::connect(LegacyMySQL::$currentDb);
    if ($pdo === null) {
        return addslashes($string);
    }

    return trim($pdo->quote((string) $string), "'");
}
