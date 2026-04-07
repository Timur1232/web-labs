<?php namespace App\Models\Statistics;

use App\Core\Helpers\MyDateTrait;
use App\Core\Model\ARField;
use App\Core\Model\ActiveRecord;

#[ActiveRecord('statistics')]
class Statistic {
    public function __construct(
        #[ARField('id', ARField::ID_FIELD)]
                                   public ?int    $id           = null,
        #[ARField('datestr')]      public ?string $datestr      = null,
        #[ARField('web_page')]     public ?string $web_page     = null,
        #[ARField('ip_address')]   public ?string $ip_address   = null,
        #[ARField('host_name')]    public ?string $host_name    = null,
        #[ARField('browser_name')] public ?string $browser_name = null,
    ) {}

    public static function current(string $page): self {
        return new self(
            web_page: $page,
            ip_address: $_SERVER['REMOTE_ADDR'],
            host_name: gethostbyaddr($_SERVER['REMOTE_ADDR']),
            browser_name: $_SERVER['HTTP_USER_AGENT'],
        )->with_current_date();
    }

    use MyDateTrait;
}
