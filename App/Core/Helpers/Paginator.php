<?php
namespace App\Core\Helpers;

/*
 * @template T
 */
final class Paginator {
    /**
     * @param T[] $items
     */
    public function __construct(
        public array $items,
        public int $per_page = 10,
    ) {}

    /**
     * @param T[] $items
     * @return iterable<T[]>
     */
    public static function paginate_from(array $items, int $per_page = 10): iterable {
        return new self(items: $items, per_page: $per_page)->paginate();
    }

    /**
     * @return iterable<T[]>
     */
    public function paginate(): iterable {
        for ($i = 0; $i < $this->page_count()-1; ++$i) {
            yield array_slice($this->items, $i*$this->per_page, $this->per_page);
        }
        if ($this->page_rem() === 0) {
            yield array_slice($this->items, count($this->items) - $this->per_page);
        } else {
            yield array_slice($this->items, count($this->items) - $this->page_rem());
        }
    }

    public function page_count(): int {
        return (int)(count($this->items) / $this->per_page) + ($this->page_rem() === 0 ? 0 : 1);
    }

    public function page_rem(): int {
        return count($this->items) % $this->per_page;
    }
}
