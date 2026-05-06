<?php namespace App\Models;
use App\Core\Helpers\MyDateTrait;
use App\Core\Model\ActiveRecord;
use App\Core\Model\ARField;

#[ActiveRecord('comments')]
final class CommentRecord {
    public function __construct(
        #[ARField('id', ARField::ID_FIELD)]
                                public ?int $id = null,
        #[ARField('blog_id')]   public ?int $blog_id = null,
        #[ARField('datestr')]   public ?string $datestr = null,
        #[ARField('user_name')] public ?string $user_name = null,
        #[ARField('text')]      public ?string $text = null,
    ) {}
    use MyDateTrait;
}
