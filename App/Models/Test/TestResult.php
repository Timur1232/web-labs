<?php
namespace App\Models\Test;

use App\Core\Helpers\MyDateTime;
use App\Core\Model\ARField;
use App\Core\Model\ActiveRecord;
use DateTime;

#[ActiveRecord('test_result')]
final class TestResult {
    public function __construct(
        #[ARField('id', ARField::ID_FIELD)]
                                        public ?int    $id                = null,
        #[ARField('datestr')]           public ?string $datestr           = null,
        #[ARField('fio')]               public ?string $fio               = null,
        #[ARField('correct_answers')]   public ?int    $correct_answers   = null,
        #[ARField('incorrent_answers')] public ?int    $incorrent_answers = null,
    ) {}

    public const DEFAULT_DB_PATH_SQLITE = 'public/test_results.db';

    public function with_current_date(): self {
        $this->datestr = MyDateTime::now();
        return $this;
    }

    public function get_date(): ?DateTime {
        return MyDateTime::to_date($this->datestr);
    }

    public function with_date(DateTime $d): self {
        $this->datestr = MyDateTime::from_date($d);
        return $this;
    }
}
