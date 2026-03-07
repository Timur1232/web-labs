<?php

namespace App\Models\GuestBook;
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

    public const DATE_TIME_FORMAT = 'Ymd-His';

    public function get_date(): ?DateTime {
        return DateTime::createFromFormat(self::DATE_TIME_FORMAT, $this->datestr);
    }

    public function with_date(DateTime $date): self {
        $this->datestr = $date->format(self::DATE_TIME_FORMAT);
        return $this;
    }

    public function with_current_date(): self {
        $date = new DateTime('now');
        $this->with_date($date);
        return $this;
    }
}
