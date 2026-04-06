<?php
namespace App\Models\Test;

use App\Core\Model\ARField;
use App\Core\Model\ActiveRecord;
use App\Core\Helpers\MyDateTrait;

#[ActiveRecord('test_result')]
final class TestResult {
    public function __construct(
        #[ARField('id', ARField::ID_FIELD)]
                                  public ?int    $id          = null,
        #[ARField('datestr')]     public ?string $datestr     = null,
        #[ARField('fio')]         public ?string $fio         = null,
        #[ARField('lim_answ')]    public ?string $lim_answ    = null,
        #[ARField('series_answ')] public ?string $series_answ = null,
        #[ARField('hard_answ')]   public ?string $hard_answ   = null,
    ) {}

    public const DEFAULT_DB_PATH_SQLITE = 'public/test_results.db';
    use MyDateTrait;

    public function is_correct(): bool {
        return !isset($this->lim_answ)
            && !isset($this->series_answ)
            && !isset($this->hard_answ);
    }
}
