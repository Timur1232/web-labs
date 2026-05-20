<?php namespace App\Models;

use App\Core\Helpers\MyDateTrait;
use App\Core\Model\ActiveRecord;
use App\Core\Model\ARField;

#[ActiveRecord('blog_comments')]
final class BlogComment {
    public function __construct(
        #[ARField('id')]      public ?int    $id      = null,
        #[ARField('user_id')] public ?int    $user_id = null,
        #[ARField('blog_id')] public ?int    $blog_id = null,
        #[ARField('datestr')] public ?string $datestr = null,
        #[ARField('text')]    public ?string $text    = null,
    ) {}
    use MyDateTrait;
}
