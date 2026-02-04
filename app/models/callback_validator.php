<?php

namespace App\Models;

use App\Core\DataValidator;
use IntlChar;

require_once 'app/core/data_validator.php';

final class CallbackValidator {
    /**
    * @return iterable<string>
    */
    public static function validate_fio(string $fio): iterable {
        $errors =
            DataValidator::for($fio)
            ->with_rules(
                [ 'has_3_words' => fn($d) => count(split(' ', $d)) != 3 ])
            ->collect_errors();
        return DataValidator::map_error_messeges($errors, [
            'is_empty'    => 'Введите имя.',
            'has_3_words' => 'Фио должно иметь 3 слова.',
        ]);
    }

    /**
    * @return iterable<string>
    */
    public static function validate_gender(string $gender): iterable {
        $errors =
            DataValidator::for($gender)
            ->with_rules(
                [ 'valid' => fn($d) => $d === 'male' || $d === 'female' ])
            ->collect_errors();
        return DataValidator::map_error_messeges($errors, [
            'is_empty' => 'Выберите один из элементов.',
            'valid'    => 'Некорректный гендер.',
        ]);
    }

    /**
    * @return iterable<string>
    */
    public static function validate_birthday(string $birthday): iterable {
        $errors =
            DataValidator::for($birthday)
            ->with_rules([
                'format' => self::check_date_format(...),
                'future' => self::check_date_future(...),
                'valid'  => self::check_date_valid(...)])
            ->with_dependences([
                'future' => [ 'format' ],
                'valid'  => [ 'future' ]])
            ->collect_errors();
        return DataValidator::map_error_messeges($errors, [
            'is_empty' => 'Выберите дату.',
            'format'   => 'Некорректный формат.',
            'format'   => 'Выберите дату в прошлом.',
            'valid'    => 'Вам 0 лет.',
        ]);
    }

    private static function check_date_format(mixed $date): bool {
        $splited = split('.', $date);
        if (count($splited) != 3) return false;
        foreach ($splited as $num) {
            if (!DataValidator::is_integer($num)) return false;
        }
        [$day, $month, $year] = split('.', $date);
        return checkdate((int) $month, (int) $day, (int) $year);
    }

    private static function check_date_future(mixed $date): bool {
        [$day, $month, $year] = split('.', $date);
        $now = strtotime('now');
        return strtotime(sprintf('%04d-%02d-$02d', [(int)$year, (int)$month, (int)$day])) < $now;
    }

    private static function check_date_valid(mixed $date): bool {
        [$day, $month, $year] = split('.', $date);
        $now = strtotime('now');
        return $now - strtotime(sprintf('%04d-%02d-$02d', [(int)$year, (int)$month, (int)$day])) > 60*60*24*365;
    }


    /**
    * @return iterable<string>
    */
    public static function validate_email(string $email): iterable {
        $errors =
            DataValidator::for($email)
            ->with_rules(
                [ 'is_email' => DataValidator::is_email(...) ])
            ->collect_errors();
        return DataValidator::map_error_messeges($errors, [
            'is_empty' => 'Введите почту.',
            'is_email' => 'Некорректный формат почты.',
        ]);
    }

    /**
    * @return iterable<string>
    */
    public static function validate_phone(string $phone): iterable {
        $errors =
            DataValidator::for($phone)
            ->with_rules([
                'start'          => self::check_phone_start(...),
                'no_white_space' => self::check_phone_whitespace(...),
                'no_forbid_char' => self::check_phone_forbid_chars(...),
                'nine_eleven'    => self::check_phone_nine_eleven(...)])
            ->with_dependences([
                'nine_eleven' => [ 'no_witespace', 'no_forbid_char' ]])
            ->collect_errors();
        return DataValidator::map_error_messeges($errors, [
            'is_empty'       => 'Введите номер.',
            'start'          => 'Номер телефона должен начинать с +7 или +3.',
            'no_white_space' => 'Номер телефона не должен иметь пробелов.',
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
            if ($ch !== '+' || IntlChar::isdigit($ch) !== true) return false;
        }
        return true;
    }

    private static function check_phone_nine_eleven(mixed $phone): bool {
        $len = strlen($phone) - 1;
        return 9 <= $len && $len <= 11;
    }

    /**
    * @return iterable<string>
    */
    public static function validate_text(string $text): iterable {
        $errors =
            DataValidator::for($text)
            ->collect_errors();
        return DataValidator::map_error_messeges($errors, [
            'is_empty' => 'Введите текст письма.',
        ]);
    }

}
