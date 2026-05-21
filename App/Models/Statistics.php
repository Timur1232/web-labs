<?php namespace App\Models\Statistics;

use App\Core\Helpers\My_Date_Trait;
use App\Core\Model\AR_Field;
use App\Core\Model\Active_Record;
use App\Core\Model\AR_Reflect;
use App\Core\Model\DB_Model;
use App\Core\Model\DB_Type;

#[Active_Record('statistics')]
class Statistic {
    public function __construct(
        #[AR_Field('id')]           public ?int    $id           = null,
        #[AR_Field('datestr')]      public ?string $datestr      = null,
        #[AR_Field('web_page')]     public ?string $web_page     = null,
        #[AR_Field('ip_address')]   public ?string $ip_address   = null,
        #[AR_Field('host_name')]    public ?string $host_name    = null,
        #[AR_Field('browser_name')] public ?string $browser_name = null,
    ) {}

    public static function current(string $page): self {
        return new self(
            web_page: $page,
            ip_address: $_SERVER['REMOTE_ADDR'],
            host_name: gethostbyaddr($_SERVER['REMOTE_ADDR']),
            browser_name: $_SERVER['HTTP_USER_AGENT'],
        )->with_current_date();
    }

    public static function insert(): string {
        $columns = AR_Reflect::comma_separated_columns_string(self::class);
        $bindings = AR_Reflect::comma_separated_binding_string(self::class);
        return match(DB_Model::$current_db) {
            DB_Type::SQLITE => <<<SQL
                insert into statistics ({$columns}) values ({$bindings})
            SQL,
            DB_Type::MYSQL => <<<SQL
                insert into statistics ({$columns}) values ({$bindings})
            SQL,
        };
    }

    use My_Date_Trait;
}
