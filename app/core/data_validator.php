<?php

namespace App\Core;

require_once 'app/core/helpers.php';

use App\Core\Helpers\Log;
use App\Core\Helpers\Error;

function default_is_empty(mixed $data): bool {
    if (!isset($data)) return true;
    if (is_string($data)) return strlen(trim($data)) == 0;
    Error::assert(false, 'DataValidator - invalid data type');
}

/*
 * WARNING: By default assumes that data is of type string.
 * Will assert on other data types: to prevent that, provide other predicate with method 'with_empty_fn'.
 */
final class DataValidator {
    /*
     * @param array<Closure(mixed): bool> $rules
     * @param ?array<string, string[]> $dependences
     * @param string[] $errors
     * @param string[] $dependency_errors
     * @param Closure(mixed): bool $is_empty_fn
     */
    private function __construct(
        public mixed $data,
        public array $rules,
        public ?array $dependences,
        public array $errors,
        public array $dependency_errors,
        public \Closure $is_empty_fn,
        public string $is_empty_name,
    ) { }

    public static function for(mixed $data): self {
        return new self($data, [], null, [], [], default_is_empty(...), 'is_empty');
    }

    /*
     * @param Closure(mixed): bool $is_empty_fn
     */
    public function with_empty_fn(\Closure $is_empty_fn, ?string $is_empty_name = null): self {
        $this->is_empty_fn = $is_empty_fn;
        if (isset($is_empty_name)) $this->is_empty_name = $is_empty_name;
        return $this;
    }

    public function is_empty(): bool {
        $pred = $this->is_empty_fn;
        return $pred($this->data);
    }

    /*
     * @param array<Closure(mixed): bool> $rules
     */
    public function with_rules(array $rules): self {
        $this->rules = $rules;
        return $this;
    }

    /*
     * @param array<string, string[]> $dependences
     */
    public function with_dependences(array $dependences): self {
        $this->dependences = $dependences;
        return $this;
    }

    /*
     * TODO: maybe return dfs_errors also
     * @return string[]
     */
    public function collect_errors(): array {
        $this->run();
        return $this->errors;
    }

    public function run(): self {
        if ($this->is_empty()) {
            $this->errors = [$this->is_empty_name];
            $this->dependency_errors = array_keys($this->rules);
            return $this;
        }
        if (isset($this->dependences) && count($this->dependences) != 0) {
            $this->resolve_dendences();
            return $this;
        }
        foreach ($this->rules as $name => $pred) {
            if (!$pred($this->data)) {
                $this->errors[] = $name;
            }
        }
        return $this;
    }

    /*
     * @param array<string, string> $mappings
     * @param string[] $errors
     * @return iterable<string>
     */
    public static function map_error_messeges(array $errors, array $mappings): iterable {
        foreach ($errors as $err) {
            if (array_key_exists($err, $mappings)) {
                yield $mappings[$err];
            }
        }
    }

    /*
     * WARNING: Will Error::assert if has any error.
     * Only for debugging.
     */
    public function debug_validate_dependences(): void {
        /** @var DependencyError[] $invalid */
        $invalid = [];
        foreach ($this->dependences as $rule => $deps) {
            if (!array_key_exists($rule, $this->rules)) {
                $invalid[] = DependencyError::new($rule, 'not in rules set');
                continue;
            }
            foreach ($deps as $dep) {
                if (!array_key_exists($dep, $this->rules)) {
                    $invalid[] = DependencyError::new($dep, 'not in rules set');
                }
                if ($dep === $rule) {
                    $invalid[] = DependencyError::new($rule, "dependency on self");
                } else if (array_key_exists($dep, $this->dependences) && in_array($rule, $this->dependences[$dep])) {
                    $invalid[] = DependencyError::new($rule, "circular denendency for '{$dep}'");
                }
            }
        }
        if (count($invalid) != 0) {
            Log::println_err('invalid dependences:');
            $i = 1;
            foreach ($invalid as $err) {
                Log::println_err("[{$i}] '{$err->rule}': {$err->reason}");
                $i += 1;
            }
            Error::assert(false, 'DataValidator - invalid dependences');
        }
    }

    private function resolve_dendences(): void {
        $visited = [];
        foreach ($this->rules as $name => $pred) {
            if (!array_key_exists($name, $this->dependences)) {
                if (!$pred($this->data)) {
                    $this->errors[] = $name;
                }
                $visited[] = $name;
            }
        }

        $stack = [array_key_first($this->dependences)];

        while (count($stack) != 0) {
            $cur = array_last($stack);
            $visited[] = $cur;

            $breaked = false;
            foreach ($this->dependences[$cur] as $dep) {
                if (in_array($dep, $this->errors) || in_array($dep, $this->dependency_errors)) {
                    $this->dependency_errors[] = $cur;
                    array_pop($stack);
                    $breaked = true;
                    break;
                }
                if (!in_array($dep, $visited)) {
                    $stack[] = $dep;
                    $breaked = true;
                    break;
                }
            }
            if ($breaked) continue;

            $pred = $this->rules[$cur];
            if (!$pred($this->data)) {
                $this->errors[] = $cur;
            }
            array_pop($stack);
        }
    }
}

final class DependencyError {
    private function __construct(
        public string $rule,
        public string $reason,
    ) { }

    public static function new(string $rule, string $reason): self {
        return new self($rule, $reason);
    }
}

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
