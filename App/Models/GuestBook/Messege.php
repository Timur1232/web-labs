<?php

namespace App\Models\GuestBook;
use App\Core\Helpers\MyDateTime;
use App\Core\Model\ARField;
use App\Core\Model\ActiveRecord;
use DateTime;

#[ActiveRecord('guest_book')]
final class Messege {
    public function __construct(
        #[ARField('datestr', ARField::ID_FIELD)]
                            public ?string $datestr = null,
        #[ARField('fio')]   public ?string $fio   = null,
        #[ARField('email')] public ?string $email   = null,
        #[ARField('text')]  public ?string $text    = null,
    ) {}

    public const DB_PATH = 'public/messeges.inc';

    public function get_date(): ?DateTime {
        return MyDateTime::to_date($this->datestr);
    }

    public function with_date(DateTime $date): self {
        $this->datestr = MyDateTime::from_date($date);
        return $this;
    }

    public function with_current_date(): self {
        $this->datestr = MyDateTime::now();
        return $this;
    }
}
