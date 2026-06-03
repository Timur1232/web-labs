<?php namespace App\Models\Dto;
use App\Core\Helpers\My_Date_Trait;
use App\Core\Model\AR_Field;
use App\Core\Model\Active_Record;
use App\Core\Model\DB_Model;
use App\Core\Model\DB_Type;
use App\Models\Common_Sql\Common_Sql;
use App\Models\Common_Sql\Order;

// TODO: take most basic queries like select, insert, update and delete on one table and move to some common sql

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
        $sql = Common_Sql::select(self::class, where: 'login = :login');
        return match(DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }

    public static function insert(): string {
        $sql = Common_Sql::insert(self::class);
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }
}

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
        $sql = Common_Sql::select(self::class, order_by: Order::desc('datestr'));
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }

    public static function select_id(): string {
        $sql = Common_Sql::select(self::class, where: 'id = :id');
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }

    public static function insert(): string {
        $sql = Common_Sql::insert(self::class);
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }

    public static function insert_many(): string {
        $sql = Common_Sql::insert(self::class);
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }

    public static function update(): string {
        $sql = Common_Sql::update(self::class, where: 'id = :id');
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }

    use My_Date_Trait;
}

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
        $sql = Common_Sql::select(self::class, where: 'blog_id = :blog_id', order_by: Order::desc('datestr'));
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }

    public static function insert(): string {
        $sql = Common_Sql::insert(self::class);
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }

    use My_Date_Trait;
}

#[Active_Record('test_result')]
final class Test_Result {
    public function __construct(
        #[AR_Field('id')]
                                  public ?int    $id          = null,
        #[AR_Field('datestr')]     public ?string $datestr     = null,
        #[AR_Field('fio')]         public ?string $fio         = null,
        #[AR_Field('lim_answ')]    public ?string $lim_answ    = null,
        #[AR_Field('series_answ')] public ?string $series_answ = null,
        #[AR_Field('hard_answ')]   public ?string $hard_answ   = null,
    ) {}

    public const DEFAULT_DB_PATH_SQLITE = 'public/test_results.db';
    use My_Date_Trait;

    public static function insert(): string {
        $sql = Common_Sql::insert(self::class);
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }

    public static function select_all(): string {
        $sql = Common_Sql::select(self::class, order_by: Order::desc('datestr'));
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }

    public function is_correct(): bool {
        return !isset($this->lim_answ)
            && !isset($this->series_answ)
            && !isset($this->hard_answ);
    }
}

#[Active_Record('guest_book')]
final class Messege {
    public function __construct(
        #[AR_Field('datestr')] public ?string $datestr = null,
        #[AR_Field('fio')]     public ?string $fio     = null,
        #[AR_Field('email')]   public ?string $email   = null,
        #[AR_Field('text')]    public ?string $text    = null,
    ) {}

    public const DB_PATH = 'public/messeges.inc';
    use My_Date_Trait;
}

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
        $sql = Common_Sql::insert(self::class);
        return match(DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }

    public static function select_all(): string {
        $sql = Common_Sql::select(self::class, order_by: Order::desc('datestr'));
        return match (DB_Model::$current_db) {
            DB_Type::SQLITE => $sql,
            DB_Type::MYSQL  => $sql,
        };
    }

    use My_Date_Trait;
}

final class Photo_Item {
    public function __construct(
        public string $filename,
        public string $alt,
        public string $title,
        public string $label,
    ) { }

    public static function from(string $filename, string $alt, string $title, string $label): self {
        return new self($filename, $alt, $title, $label);
    }
}

