<?php

namespace App\Core\Model;

class FormValidator {
    /*
     * @param FormValidatorRule[] $errors
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

    public function has_error(FormValidatorRule $rule): bool {
        return in_array($rule, $this->errors);
    }

    public function not_empty(): self {
        return $this->check(
            isset($this->data) && count(trim($this->data)) != 0,
            FormValidatorRule::NotEmpty,
        );
    }

    public function is_integer(): self {
        if ($this->has_error(FormValidatorRule::NotEmpty)) return $this;
        return $this->check(
            filter_var(trim($this->data), FILTER_VALIDATE_INT) !== false,
            FormValidatorRule::IsInteger,
        );
    }

    public function less_than(int $value): self {
        if ($this->has_error(FormValidatorRule::NotEmpty) || $this->has_error(FormValidatorRule::IsInteger))
            return $this;
        return $this->check(
            (int) trim($this->data) < $value,
            FormValidatorRule::IsLess,
        );
    }

    public function greater_than(int $value): self {
        if ($this->has_error(FormValidatorRule::NotEmpty) || $this->has_error(FormValidatorRule::IsInteger))
            return $this;
        return $this->check(
            (int) trim($this->data) > $value,
            FormValidatorRule::IsGreater,
        );
    }

    public function is_email(): self {
        if ($this->has_error(FormValidatorRule::NotEmpty)) return $this;
        return $this->check(
            filter_var($this->data, FILTER_VALIDATE_EMAIL) !== false,
            FormValidatorRule::IsEmail,
        );
    }

    public function custom(bool $exp, mixed $custom_rule): self {
        if (!$exp) {
            $this->custom_errors[] = $custom_rule;
        }
        return $this;
    }

    private function check(bool $exp, FormValidatorRule $rule): self {
        if (!$exp) {
            $this->errors[] = $rule;
        }
        return $this;
    }
}
