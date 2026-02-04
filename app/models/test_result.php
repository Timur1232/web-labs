<?php

namespace App\Models;

require_once 'app/core/data_validator.php';

use App\Core\DataValidator;

final class TestModel {

    /*
     * @param string[] $lim_errs
     * @param string[] $series_errs
     * @param string[] $hard_errs
     */
    private function __construct(
        public array $lim_errs    = [],
        public array $series_errs = [],
        public array $hard_errs   = [],
    ) { }

    public static function default(): self {
        return new self();
    }

    /*
    * @param string[] $answers
    */
    public function check_test(array $answers): void {
        $this->lim_errs =
            DataValidator::for($answers['lim'])
            ->with_rules([
                'is_int'   => DataValidator::is_integer(...),
                'solution' => fn($d) => (int)$d == 5])
            ->with_dependences([
                'solution' => ['is_int']])
            ->collect_errors();

        $this->series_errs =
            DataValidator::for($answers['series'])
            ->with_rules([
                'solution' => fn($d) => $d === 'answ2'])
            ->collect_errors();

        $this->hard_errs =
            DataValidator::for($answers['hard_one'])
            ->with_rules([
                'solution' => fn($d) => $d !== '4'])
            ->collect_errors();
    }

    public function has_errors(): bool {
        return $this->has_lim_errors()
            || $this->has_series_errors()
            || $this->has_hard_errors();
    }

    public function has_lim_errors(): bool {
        return count($this->lim_errs) != 0;
    }

    public function has_series_errors(): bool {
        return count($this->series_errs) != 0;
    }

    public function has_hard_errors(): bool {
        return count($this->hard_errs) != 0;
    }

    /*
    * @return iterable<string>
    */
    public function get_lim_messeges(): iterable {
        return DataValidator::map_error_messeges($this->lim_errs, [
            'is_empty' => 'Ну и что я должен с этим делать',
            'is_int'   => 'Нужно целое число',
            'solution' => 'БесПРЕДЕЛ!',
        ]);
    }

    /*
    * @return iterable<string>
    */
    public function get_series_messeges(): iterable {
        return DataValidator::map_error_messeges($this->series_errs, [
            'is_empty' => 'Ну и что я должен с этим делать',
            'solution' => 'Ольшанская выехала за тобой'
        ]);
    }

    /*
    * @return iterable<string>
    */
    public function get_hard_messeges(): iterable {
        return DataValidator::map_error_messeges($this->hard_errs, [
            'is_empty' => 'Ну и что я должен с этим делать',
            'solution' => 'Стоит подумать еще раз'
        ]);
    }
}
