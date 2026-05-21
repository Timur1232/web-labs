<?php namespace App\Core\Model;
use App\Core\Helpers\Result;
use PDO;
use PDOStatement;
use Pdo\Mysql;
use Pdo\Sqlite;

enum DB_Type {
    case SQLITE;
    case MYSQL;
}

final class DB_Stmt {
    public function __construct(
        public ?PDOStatement $stmt = null,
    ) {}

    /**
    * @return Result<self>
    */
    public function execute(): Result {
        if (is_null($this->stmt)) return Result::ERROR('pdo statement is null');
        $res = $this->stmt->execute();
        if ($res === false) return Result::ERROR('error during executing, info: '.print_r($this->stmt->errorInfo(), true));
        return Result::OK($this);
    }

    public function rows_count(): int {
        if (is_null($this->stmt)) return 0;
        return $this->stmt->rowCount();
    }

    /**
    * @return Result<array<string, string>>
    */
    public function fetch(): Result {
        if (is_null($this->stmt)) return Result::ERROR('pdo statement is null');
        $ret = $this->stmt->fetch(PDO::FETCH_ASSOC);
        if ($ret === false) return Result::ERROR('error during executing, info: '.print_r($this->stmt->errorInfo(), true));
        return Result::OK($ret);
    }

    /**
    * @return Result<array<int, array<string, string>>>
    */
    public function fetch_all(): Result {
        if (is_null($this->stmt)) return Result::ERROR('pdo statement is null');
        return Result::OK($this->stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param mixed|array<int|string,mixed> $data
     * @return Result<self>
     */
    public function bind_values(mixed $data): Result {
        if (is_null($this->stmt)) return Result::ERROR('pdo statement is null');

        $vals = [];
        if (is_array($data)) {
            $vals = $data;
        } else {
            $ar = AR_Reflect::from($data::class);
            if (is_null($ar)) return Result::ERROR('unable to reflect on data, type: ' . gettype($data));
            foreach ($ar->fields_to_columns_array() as $field => $column) {
                $vals[$column] = $data->$field;
            }
        }

        $count = 1;
        foreach ($vals as $index => $val) {
            if (is_int($index)) {
                if (!$this->stmt->bindValue($count, $val)) {
                    return Result::ERROR('unable to bind value with index = '.print_r($index, true));
                }
                $count += 1;
            } else if (is_string($index)) {
                if (!$this->stmt->bindValue($index, $val)) {
                    return Result::ERROR('Unable to bind value with index = '.print_r($index, true));
                }
            } else {
                return Result::ERROR('Incorrect index type: '.print_r($index, true));
            }
        }
        return Result::OK($this);
    }
}

final class DB_Model {
    public static PDO $conn;
    public static DB_Type $current_db;

    public static function my_sql_connect(string $conn_string, ?string $username = null, ?string $password = null, ?array $options = null): void {
        self::$conn = new Mysql($conn_string, $username, $password, $options);
        self::$current_db = DB_Type::MYSQL;
    }

    public static function sqlite_connect(string $db_path): void {
        self::$conn = new Sqlite("sqlite:{$db_path}");
        self::$current_db = DB_Type::SQLITE;
    }

    public static function query(string $sql): ?DB_Stmt {
        $stmt = self::$conn->prepare($sql);
        if ($stmt === false) return null;
        return new DB_Stmt($stmt);
    }

    public static function begin_transaction(): bool {
        return self::$conn->beginTransaction();
    }

    public static function commit(): bool {
        return self::$conn->commit();
    }

    public static function roll_back(): bool {
        return self::$conn->rollBack();
    }

    private function __construct() {}
}
