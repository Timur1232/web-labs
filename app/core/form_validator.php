<?php

namespace App\Core;

enum Rule {
    case NotEmpty;
    case IsInteger;
    case IsLess;
    case IsGreater;
    case IsEmail;
}

class FormValidator {
    /*
     * @param Rule[] $errors
     * @param mixed[] $custom_errors
     */
    protected function __construct(
        public ?string $data,
        public array $errors,
        public array $custom_errors,
    ) { }

    public static function for(?string $data): self {
        return new self($data, [], []);
    }

    public function has_error(Rule $rule): bool {
        return in_array($rule, $this->errors);
    }

    public function not_empty(): self {
        return $this->check(
            isset($this->data) && count(trim($this->data)) != 0,
            Rule::NotEmpty,
        );
    }

    public function is_integer(): self {
        if ($this->has_error(Rule::NotEmpty)) return $this;
        return $this->check(
            filter_var(trim($this->data), FILTER_VALIDATE_INT) !== false,
            Rule::IsInteger,
        );
    }

    public function less_than(int $value): self {
        if ($this->has_error(Rule::NotEmpty) || $this->has_error(Rule::IsInteger))
            return $this;
        return $this->check(
            (int) trim($this->data) < $value,
            Rule::IsLess,
        );
    }

    public function greater_than(int $value): self {
        if ($this->has_error(Rule::NotEmpty) || $this->has_error(Rule::IsInteger))
            return $this;
        return $this->check(
            (int) trim($this->data) > $value,
            Rule::IsGreater,
        );
    }

    public function is_email(): self {
        if ($this->has_error(Rule::NotEmpty)) return $this;
        return $this->check(
            filter_var($this->data, FILTER_VALIDATE_EMAIL) !== false,
            Rule::IsEmail,
        );
    }

    public function custom(bool $exp, mixed $custom_rule): self {
        if (!$exp) {
            $this->custom_errors[] = $custom_rule;
        }
        return $this;
    }

    private function check(bool $exp, Rule $rule): self {
        if (!$exp) {
            $this->errors[] = $rule;
        }
        return $this;
    }
}
