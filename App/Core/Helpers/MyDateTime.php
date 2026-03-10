<?php
namespace App\Core\Helpers;

use DateTime;

final class MyDateTime {
    public const FORMAT = 'Ymd-His';

    public static function from_date(DateTime $d): string {
        return $d->format(self::FORMAT);
    }

    public static function now(): string {
        return self::from_date(new DateTime('now'));
    }

    public static function to_date(string $datestr): ?DateTime {
        $d = DateTime::createFromFormat(self::FORMAT, $this->datestr);
        return $d === false ? null : $d;
    }
}
