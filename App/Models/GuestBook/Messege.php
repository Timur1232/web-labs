<?php

namespace App\Models\GuestBook;
use App\Core\Helpers\MyDateTrait;
use App\Core\Model\ARField;
use App\Core\Model\ActiveRecord;

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
    use MyDateTrait;
}
