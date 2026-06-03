<?php namespace App\Models;
use App\Core\Model\DB_Model;
use App\Core\Helpers\Result;
use App\Core\Model\Data_Validator;
use App\Models\Dto\Test_Result;

final class Test_Model {
    /*
     * @param string[] $user_answers
     * @param string[] $lim_errs
     * @param string[] $series_errs
     * @param string[] $hard_errs
     */
    public function __construct(
        public array $user_answers = [],
        public array $lim_errs    = [],
        public array $series_errs = [],
        public array $hard_errs   = [],
    ) { }

    public function save_results(string $fio): Result {
        $record = new Test_Result(fio: $fio)
            ->with_current_date();
        if (count($this->lim_errs) !== 0) {
            $record->lim_answ = "Ожидалось: 5. Получено: {$this->user_answers['lim']}.";
        }
        if (count($this->series_errs) !== 0) {
            $record->series_answ = "Ожидалось: 2). Получено: {$this->user_answers['series']}.";
        }
        if (count($this->hard_errs) !== 0) {
            $record->hard_answ = "Ожидалось: ???. Получено: {$this->user_answers['hard_one']}.";
        }
        $res = DB_Model::query(Test_Result::insert())
            ->bind_values($record)
            ->execute();
        return $res;
    }

    /*
     * @param string[] $user_answers
     */
    public static function from(array $user_answers): self {
        return new self($user_answers);
    }

    public function check_test(): void {
        $this->lim_errs =
            Data_Validator::for($this->user_answers['lim'])
            ->with_rules([
                'is_int'   => Data_Validator::is_integer(...),
                'solution' => fn($d) => (int)$d == 5])
            ->with_dependences([
                'solution' => ['is_int']])
            ->collect_errors();

        $this->series_errs =
            Data_Validator::for($this->user_answers['series'])
            ->with_rules([
                'solution' => fn($d) => $d === '2'])
            ->collect_errors();

        $this->hard_errs =
            Data_Validator::for($this->user_answers['hard_one'])
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
        return Data_Validator::map_error_messeges($this->lim_errs, [
            'is_empty' => 'Ну и что я должен с этим делать? Нужно вписать что-нибудь, идиот.',
            'is_int'   => "Нужно целое число, а получено {$this->user_answers['lim']}, идиот.",
            'solution' => "Правильный ответ - 5, а получено {$this->user_answers['lim']}, идиот. Это БесПРЕДЕЛ!",
        ]);
    }

    /*
    * @return iterable<string>
    */
    public function get_series_messeges(): iterable {
        return Data_Validator::map_error_messeges($this->series_errs, [
            'is_empty' => 'Ну и что я должен с этим делать? Нужно вписать что-нибудь, идиот.',
            'solution' => "Правильный ответ - 2). Получено {$this->user_answers['series']}, идиот. Ольшанская уже выехала за тобой."
        ]);
    }

    /*
    * @return iterable<string>
    */
    public function get_hard_messeges(): iterable {
        return Data_Validator::map_error_messeges($this->hard_errs, [
            'is_empty' => 'Ну и что я должен с этим делать? Нужно вписать что-нибудь, идиот.',
            'solution' => <<<TEXT
            Правильный ответ -
            Stack trace:
            #0 /home/timur/dev/uni/web/app/core/router.php(95): App\Controllers\Study::check_test()
            #1 /home/timur/dev/uni/web/index.php(28): App\Core\Router->dispatch()
            #2 {main}
            thrown in /home/timur/dev/uni/web/app/controllers/study.php on line 27.
            Получено {$this->user_answers['hard_one']}. Стоит подумать еще раз.
            TEXT
        ]);
    }
}
