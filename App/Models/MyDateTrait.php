<?php
namespace App\Models;

use App\Core\Helpers\MyDateTime;
use DateTime;

trait MyDateTrait {
    public function get_date(): ?DateTime {
        return MyDateTime::to_date($this->datestr);
    }

    public function with_date(DateTime $date): self {
        $this->datestr = MyDateTime::from_date($date);
        return $this;
    }

    public function with_current_date(): self {
        $this->datestr = MyDateTime::now();
        return $this;
    }

    final public const DEFAULT_DATE_FORMAT = 'd.m.Y H:i';
    public function format(string $fmt = self::DEFAULT_DATE_FORMAT): string {
        return $this->get_date()->format($fmt);
    }
}
