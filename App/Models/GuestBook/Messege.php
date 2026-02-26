<?php

namespace App\Models\GuestBook;
use App\Core\Model\ARField;
use App\Core\Model\ActiveRecord;
use DateTime;

#[ActiveRecord('guest_book')]
final class Messege {
    #[ARField('date')] public DateTime $date;
    #[ARField('sname')] public string $sname;
    #[ARField('fname')] public string $fname;
    #[ARField('surname')] public string $surname;
    #[ARField('email')] public string $email;
    #[ARField('text')] public string $text;
}
