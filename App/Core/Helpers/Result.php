<?php
namespace App\Core\Helpers;

/*
 * @template T
 */
final class Result {
    /**
     * @param T|null $val
     */
    public function __construct(
        public bool $ok,
        public $val = null,
        public ?string $error = null
    ) {}

    /**
     * @param T|null $val
     */
    public static function OK($val = null): self {
        return new self(ok: true, val: $val);
    }

    public static function ERROR(string $error_msg): self {
        return new self(ok: false, error: $error_msg);
    }

    public function prefix(string $prefix): self {
        $this->error = "{$prefix}: {$this->error}";
        return $this;
    }

    public function log(?string $prefix = null): self {
        $prefix = isset($prefix) ? $prefix.': ' : '';
        Log::error($prefix . strval($this->error));
        return $this;
    }

    public static function TODO(string $msg): self {
        return new self(ok: false, error: "[NOT IMPLEMENTED]: {$msg}");
    }

}
