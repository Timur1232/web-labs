<?php namespace App\Models;
use App\Core\Helpers\My_Date_Trait;
use App\Core\Model\Active_Record;
use App\Core\Model\AR_Field;
use App\Core\Model\AR_Reflect;
use App\Core\Model\DB_Model;
use App\Core\Model\DB_Type;

#[Active_Record('comments')]
final class Comment_Record {
    public function __construct(
        #[AR_Field('id')]        public ?int    $id        = null,
        #[AR_Field('blog_id')]   public ?int    $blog_id   = null,
        #[AR_Field('datestr')]   public ?string $datestr   = null,
        #[AR_Field('user_name')] public ?string $user_name = null,
        #[AR_Field('text')]      public ?string $text      = null,
    ) {}

    public static function select_blog_id(): string {
        $cols = AR_Reflect::comma_separated_columns_string(self::class);
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => "select {$cols} from comments where blog_id = :blog_id order by datestr",
            DB_Type::MYSQL  => "select {$cols} from comments where blog_id = :blog_id order by datestr",
        };
    }

    public static function insert(): string {
        $cols = AR_Reflect::comma_separated_columns_string(self::class);
        $binds = AR_Reflect::comma_separated_binding_string(self::class);
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => "insert into comments ({$cols}) values ({$binds})",
            DB_Type::MYSQL  => "insert into comments ({$cols}) values ({$binds})",
        };
    }

    use My_Date_Trait;
}
