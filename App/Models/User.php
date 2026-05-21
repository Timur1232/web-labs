<?php namespace App\Models;

use App\Core\Model\AR_Field;
use App\Core\Model\Active_Record;
use App\Core\Model\AR_Reflect;
use App\Core\Model\DB_Model;
use App\Core\Model\DB_Type;

#[Active_Record('users')]
final class User {
    public function __construct(
        #[AR_Field('login')]         public ?string $login         = null,
        #[AR_Field('fio')]           public ?string $fio           = null,
        #[AR_Field('email')]         public ?string $email         = null,
        #[AR_Field('password_hash')] public ?string $password_hash = null,
        #[AR_Field('is_admin')]      public ?bool   $is_admin      = null,
    ) {}

    public static function select_login(): string {
        $columns = AR_Reflect::comma_separated_columns_string(self::class);
        return match(DB_Model::$current_db) {
            DB_Type::SQLITE => <<<SQL
                select {$columns} from users where login = :login
            SQL,
            DB_Type::MYSQL => <<<SQL
                select {$columns} from users where login = :login
            SQL
        };
    }
}
