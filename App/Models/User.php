<?php namespace App\Models;

use App\Core\Model\ARField;
use App\Core\Model\ActiveRecord;

#[ActiveRecord('users')]
final class User {
    public function __construct(
        #[ARField('login', ARField::ID_FIELD)]
                                    public ?string $login         = null,
        #[ARField('fio')]           public ?string $fio           = null,
        #[ARField('email')]         public ?string $email         = null,
        #[ARField('password_hash')] public ?string $password_hash = null,
        #[ARField('is_admin')]      public ?bool   $is_admin      = null,
    ) {}
}
