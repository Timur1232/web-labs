<?php namespace App\Models;
use App\Core\Helpers\My_Date_Trait;
use App\Core\Model\AR_Field;
use App\Core\Model\Active_Record;
use App\Core\Model\AR_Reflect;
use App\Core\Model\DB_Model;
use App\Core\Model\DB_Type;

#[Active_Record('blogs')]
final class Blog_Record {
    public function __construct(
        #[AR_Field('id')]         public ?int $id            = null,
        #[AR_Field('datestr')]    public ?string $datestr    = null,
        #[AR_Field('author')]     public ?string $author     = null,
        #[AR_Field('title')]      public ?string $title      = null,
        #[AR_Field('image_path')] public ?string $image_path = null,
        #[AR_Field('text')]       public ?string $text       = null,
    ) {}

    public static function select_all(): string {
        $cols = AR_Reflect::comma_separated_columns_string(self::class);
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => "select {$cols} from blogs order by datestr desc",
            DB_Type::MYSQL  => "select {$cols} from blogs order by datestr desc",
        };
    }

    public static function select_id(): string {
        $cols = AR_Reflect::comma_separated_columns_string(self::class);
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => "select {$cols} from blogs where id = :id order by datestr desc",
            DB_Type::MYSQL  => "select {$cols} from blogs where id = :id order by datestr desc",
        };
    }

    use My_Date_Trait;
}
