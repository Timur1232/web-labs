<?php

namespace App\Models;
require_once 'app/core/model/data_validator.php';

use App\Core\Model\DataValidator;


final class CallbackValidator {

    /**
     * @param string[] $form
     * @param string[] $fio_errors
     * @param string[] $gender_errors
     * @param string[] $birthday_errors
     * @param string[] $email_errors
     * @param string[] $phone_errors
     * @param string[] $text_errors
    */
    private function __construct(
        private array $form = [],
        private array $fio_errors = [],
        private array $gender_errors = [],
        private array $birthday_errors = [],
        private array $email_errors = [],
        private array $phone_errors = [],
        private array $text_errors = [],
        private bool $has_any_errors = false,
    ) {}

    /**
     * @param string[] $form
    */
    public static function from(array $form): self {
        return new self($form);
    }

    public function validate_all(): self {
        return $this
            ->validate_fio()
            ->validate_gender()
            ->validate_birthday()
            ->validate_email()
            ->validate_phone()
            ->validate_text();
    }

    public function has_any_error(): bool {
        return $this->has_fio_errors()
            || $this->has_gender_errors()
            || $this->has_birthday_errors()
            || $this->has_email_errors()
            || $this->has_phone_errors()
            || $this->has_text_errors();
    }

    public function validate_by_query(?string $query_f): bool {
        switch ($query_f) {
            case null       : return false;
            case 'fio'      : $this->validate_fio();
            case 'gender'   : $this->validate_gender();
            case 'birthday' : $this->validate_birthday();
            case 'email'    : $this->validate_email();
            case 'phone'    : $this->validate_phone();
            case 'text'     : $this->validate_text();
        };
        return true;
    }

    /**
     * @return iterable<string>
     */
    public function get_errors_by_query(string $query_f): iterable {
        return match ($query_f) {
            'fio'      => $this->get_fio_errors(),
            'gender'   => $this->get_gender_errors(),
            'birthday' => $this->get_birthday_errors(),
            'email'    => $this->get_email_errors(),
            'phone'    => $this->get_phone_errors(),
            'text'     => $this->get_text_errors(),
        };
    }

    public function validate_fio(): self {
        $this->fio_errors =
            DataValidator::for($this->form['fio'])
            ->with_rules(
                [ 'has_3_words' => fn($d) => count(explode(' ', $d)) === 3 ])
            ->collect_errors();
        return $this;
    }

    public function has_fio_errors(): bool {
        return count($this->fio_errors) !== 0;
    }

    /**
    * @return iterable<string>
    */
    public function get_fio_errors(): iterable {
        return DataValidator::map_error_messeges($this->fio_errors, [
            'is_empty'    => 'Введите имя.',
            'has_3_words' => 'Фио должно иметь 3 слова.',
        ]);
    }

    public function validate_gender(): self {
        $this->gender_errors =
            DataValidator::for($this->form['gender'])
            ->with_rules(
                [ 'valid' => fn($d) => $d === 'male' || $d === 'female' ])
            ->collect_errors();
        return $this;
    }

    public function has_gender_errors(): bool {
        return count($this->gender_errors) !== 0;
    }

    /**
    * @return iterable<string>
    */
    public function get_gender_errors(): iterable {
        return DataValidator::map_error_messeges($this->gender_errors, [
            'is_empty' => 'Выберите один из элементов.',
            'valid'    => 'Некорректный гендер.',
        ]);
    }

    public function validate_birthday(): self {
        $this->birthday_errors =
            DataValidator::for($this->form['birthday'])
            ->with_rules([
                'format' => self::check_date_format(...),
                'future' => self::check_date_future(...),
                'valid'  => self::check_date_valid(...)])
            ->with_dependences([
                'future' => [ 'format' ],
                'valid'  => [ 'future' ]])
            ->collect_errors();
        return $this;
    }

    public function has_birthday_errors(): bool {
        return count($this->birthday_errors) !== 0;
    }

    /**
    * @return iterable<string>
    */
    public function get_birthday_errors(): iterable {
        return DataValidator::map_error_messeges($this->birthday_errors, [
            'is_empty' => 'Выберите дату.',
            'format'   => 'Некорректный формат.',
            'future'   => 'Выберите дату в прошлом.',
            'valid'    => 'Вам 0 лет.',
        ]);
    }

    private static function check_date_format(mixed $date): bool {
        $splited = explode('-', $date);
        if (count($splited) != 3) return false;
        foreach ($splited as $num) {
            if (!DataValidator::is_integer($num)) return false;
        }
        [$year, $month, $day] = $splited;
        return checkdate((int) $month, (int) $day, (int) $year);
    }

    private static function check_date_future(mixed $date): bool {
        [$year, $month, $day] = explode('-', $date);
        $now = strtotime('now');
        $date_str = sprintf('%04d-%02d-%02d', (int)$year, (int)$month, (int)$day);
        $chosen = strtotime($date_str);
        return $chosen < $now;
    }

    private static function check_date_valid(mixed $date): bool {
        [$year, $month, $day] = explode('-', $date);
        $now = strtotime('now');
        $chosen = strtotime(sprintf('%04d-%02d-%02d', (int)$year, (int)$month, (int)$day));
        return $now - $chosen > 60*60*24*365;
    }


    public function validate_email(): self {
        $this->email_errors =
            DataValidator::for($this->form['email'])
            ->with_rules(
                [ 'is_email' => DataValidator::is_email(...) ])
            ->collect_errors();
        return $this;
    }

    public function has_email_errors(): bool {
        return count($this->email_errors) !== 0;
    }

    /**
    * @return iterable<string>
    */
    public function get_email_errors(): iterable {
        return DataValidator::map_error_messeges($this->email_errors, [
            'is_empty' => 'Введите почту.',
            'is_email' => 'Некорректный формат почты.',
        ]);
    }

    public function validate_phone(): self {
        $this->phone_errors =
            DataValidator::for($this->form['phone'])
            ->with_rules([
                'start'          => self::check_phone_start(...),
                'no_whitespace'  => self::check_phone_whitespace(...),
                'no_forbid_char' => self::check_phone_forbid_chars(...),
                'nine_eleven'    => self::check_phone_nine_eleven(...)])
            ->with_dependences([
                'nine_eleven' => [ 'start', 'no_whitespace', 'no_forbid_char' ]])
            ->collect_errors();
        return $this;
    }

    public function has_phone_errors(): bool {
        return count($this->phone_errors) !== 0;
    }

    /**
    * @return iterable<string>
    */
    public function get_phone_errors(): iterable {
        return DataValidator::map_error_messeges($this->phone_errors, [
            'is_empty'       => 'Введите номер.',
            'start'          => 'Номер телефона должен начинать с +7 или +3.',
            'no_whitespace'  => 'Номер телефона не должен иметь пробелов.',
            'no_forbid_char' => 'Номер телефона может иметь только цифры и символ \'+\'.',
            'nine_eleven'    => 'Номер телефона должен иметь от 9 до 11 цифр.',
        ]);
    }

    private static function check_phone_start(mixed $phone): bool {
        return str_starts_with($phone, '+7') || str_starts_with($phone, '+3');
    }

    private static function check_phone_whitespace(mixed $phone): bool {
        $len = strlen($phone);
        for ($i = 0; $i < $len; $i += 1) {
            if (ctype_space($phone[$i])) return false;
        }
        return true;
    }

    private static function check_phone_forbid_chars(mixed $phone): bool {
        $len = strlen($phone);
        for ($i = 0; $i < $len; $i += 1) {
            $ch = $phone[$i];
            if ($ch !== '+' && !ctype_digit($ch)) return false;
        }
        return true;
    }

    private static function check_phone_nine_eleven(mixed $phone): bool {
        $len = strlen($phone) - 1;
        return 9 <= $len && $len <= 11;
    }

    public function validate_text(): self {
        $this->text_errors =
            DataValidator::for($this->form['text'])
            ->collect_errors();
        return $this;
    }

    public function has_text_errors(): bool {
        return count($this->text_errors) !== 0;
    }

    /**
    * @return iterable<string>
    */
    public function get_text_errors(): iterable {
        return DataValidator::map_error_messeges($this->text_errors, [
            'is_empty' => 'Введите текст письма.',
        ]);
    }

}
