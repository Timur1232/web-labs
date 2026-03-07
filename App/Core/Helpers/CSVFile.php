<?php
namespace App\Core\Helpers;

final class CSVFile {
    public const DEFAULT_SEPARATOR = ',';

    /*
     * @param string[] $head
     * @param string[][] $rows
     */
    public function __construct(
        public string $file_path,
        public array $head = [],
        public array $rows = [],
        public string $sep = self::DEFAULT_SEPARATOR,
    ) {}

    /*
     * @return Error<self>
     * @param string[]|null $valid_head -- head for validating, pass null for no validation
     */
    public static function open(string $file_path, string $sep = self::DEFAULT_SEPARATOR, array $valid_head = null): Error {
        $handle = fopen($file_path, 'r');
        if ($handle === false) {
            return Error::ERROR(__METHOD__.": unable to open file {$file_path}");
        }
        Defer::d($_, fclose(...), $handle);

        $self = new self($file_path, sep: $sep);
        $err = $self->read_head($handle);
        if (!$err->ok) {
            return $err;
        }
        if (isset($valid_head)) {
            foreach ($valid_head as $vk) {
                if (!in_array($vk, $self->head)) {
                    return Error::ERROR(__METHOD__.": invalid head in file {$file_path}; needed {$valid_head}, but read {$self->head}");
                }
            }
        }
        if (!$self->read_rows($handle)) {
            return Error::ERROR(__METHOD__.": error during reading rows");
        }

        return Error::OK($self);
    }

    /*
     * @param string[] $head
     * @return Error<self>
     */
    public static function open_or_create(string $file_path, array $head, string $sep = self::DEFAULT_SEPARATOR): Error {
        if (count($head) === 0) {
            return Error::ERROR(__METHOD__.": csv head must have at least one item");
        }
        if (file_exists($file_path)) {
            Log::info(__METHOD__.": file {$file_path} exists - openinig instead");
            return self::open($file_path, sep: $sep, valid_head: $head);
        }

        $self = new self($file_path, head: $head, sep: $sep);
        $err = $self->write_all();
        if (!$err->ok) {
            return $err;
        }

        return Error::OK($self);
    }

    /**
     * @param array<string,mixed>[] $rows
     * -- row is array of pairs (name-in-head => string-value)
     */
    public function append(mixed $rows): Error {
        $acc = '';
        foreach ($rows as $row) {
            $new_row = [];
            foreach ($this->head as $k) {
                if (array_key_exists($k, $row)) {
                    $new_row[] = strval($row[$k]);
                } else {
                    $new_row[] = '';
                }
            }
            $acc .= implode($this->sep, str_replace($this->sep, "\\{$this->sep}", $new_row))."\n";
            $this->rows[] = $new_row;
        }
        return $this->write_append($acc);
    }

    /**
     * @param string[] $row
     * @param array<string,mixed> $query
     */
    private function query_cmp(array $row, array $query): bool {
        foreach ($query as $k => $v) {
            $index = array_search($k, $this->head);
            if ($index === false) return false;
            if ($row[$index] !== strval($v)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array<string,mixed> $query
     * -- (name-in-head => string-value)
     * @return Error<array<string,string>[]>
     */
    public function find(array $query): Error {
        $vals = [];
        foreach ($this->rows as $row) {
            if ($this->query_cmp($row, $query)) {
                $vals[] = array_combine($this->head, $row);
            }
        }
        return Error::OK($vals);
    }

    /**
     * @param array<string,mixed> $query
     * -- (name-in-head => string-value)
     * @return Error<int>
     */
    public function delete(array $query): Error {
        $count = 0;
        foreach ($this->rows as $i => $row) {
            if ($this->query_cmp($row, $query)) {
                unset($this->rows[$i]);
                $count++;
            }
        }
        $this->write_all();
        return Error::OK($count);
    }

    /**
     * @param resource $handle
     */
    private function read_head($handle): Error {
        $line = fgets($handle);
        if ($line === false) {
            return Error::ERROR(__METHOD__.": file {$this->file_path} must have head");
        }
        $head = explode($this->sep, trim($line));
        if ($head === false) {
            return Error::ERROR(__METHOD__.": unable to read file head in {$this->file_path}");
        }
        $this->head = $head;
        return Error::OK();
    }

    /**
     * @param resource $handle
     */
    private function read_rows($handle): bool {
        if (count($this->head) === 0) {
            return false;
        }
        $line_n = 1;
        while (($line = fgets($handle)) !== false) {
            $line_n++;
            $row = $this->parse_line($line);
            if ($row === null) {
                Log::error(__METHOD__.": {$this->file_path}:{$line_n}: incorrect format. Skipping.");
                continue;
            }
            $this->rows[] = $row;
        }
        return true;
    }

    /**
     * @return string[]
     */
    private function split_escaped(string $str): array {
        $acc = '';
        $res = [];
        $escape = false;
        foreach (mb_str_split($str, encoding: 'UTF-8') as $ch) {
            if ($ch === '\\') {
                $escape = true;
            } else if ((!$escape && $ch !== $this->sep) || ($escape && $ch === $this->sep)) {
                $acc .= $ch;
                $escape = false;
            } else {
                $res[] = $acc;
                $acc = '';
            }
        }
        $res[] = $acc;
        return $res;
    }

    /*
    * @return ?string[]
    */
    private function parse_line(string $line): ?array {
        $splited = $this->split_escaped(trim($line));
        if (count($splited) !== count($this->head)) return null;
        return $splited;
    }

    // Helper
    private function write_all(): Error {
        $handle = fopen($this->file_path, 'w');
        if ($handle === false) {
            return Error::ERROR(__METHOD__.": unable to open file {$this->file_path}");
        }
        Defer::d($_, fclose(...), $handle);

        if (fputs($handle, implode($this->sep, $this->head)."\n") === false) {
            return Error::ERROR(__METHOD__.": unable to write head to file {$this->file_path}");
        }

        foreach ($this->rows as $row) {
            if (fputs($handle, implode($this->sep, str_replace($this->sep, "\\{$this->sep}", $row))."\n") === false) {
                return Error::ERROR(__METHOD__.": error during writing rows");
            }
        }

        return Error::OK();
    }

    // Helper
    private function write_append(string $str): Error {
        $handle = fopen($this->file_path, 'a');
        if ($handle === false) {
            return Error::ERROR(__METHOD__.": unable to open file {$this->file_path}");
        }
        Defer::d($_, fclose(...), $handle);

        if (fputs($handle, $str) === false) {
            return Error::ERROR(__METHOD__.": unable to append row to file {$this->file_path}");
        }

        return Error::OK();
    }
}
