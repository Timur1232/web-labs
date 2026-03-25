<?php
namespace App\Models;

use App\Core\Helpers\MyDateTrait;
use App\Core\Model\ARField;
use App\Core\Model\ActiveRecord;

#[ActiveRecord('blogs')]
final class BlogRecord {
    public function __construct(
        #[ARField('id', ARField::ID_FIELD)]
                                 public ?int $id            = null,
        #[ARField('datestr')]    public ?string $datestr    = null,
        #[ARField('author')]     public ?string $author     = null,
        #[ARField('title')]      public ?string $title      = null,
        #[ARField('image_path')] public ?string $image_path = null,
        #[ARField('text')]       public ?string $text       = null,
    ) {}
    use MyDateTrait;
}
