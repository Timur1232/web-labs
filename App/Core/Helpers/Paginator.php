<?php
namespace App\Core\Helpers;

/*
 * @template T
 */
final class Paginator {
    public const DEFAULT_PER_PAGE = 10;

    /**
     * @param T[] $items
     * @return iterable<T[]>
     */
    public static function paginate(array $items, int $per_page = self::DEFAULT_PER_PAGE): iterable {
        for ($i = 0; $i < self::page_count($items, $per_page)-1; ++$i) {
            yield array_slice($items, $i*$per_page, $per_page);
        }
        if (self::page_rem($items, $per_page) === 0) {
            yield array_slice($items, count($items) - $per_page);
        } else {
            yield array_slice($items, count($items) - self::page_rem($items, $per_page));
        }
    }

    /**
     * @param T[] $items
     * @return T[]
     */
    public function nth_page(array $items, int $page, int $per_page = self::DEFAULT_PER_PAGE): array {
        return array_slice($items, count($items) - $per_page);
    }

    /**
     * @param T[] $items
     */
    public static function page_count(array $items, int $per_page = self::DEFAULT_PER_PAGE): int {
        return (int)(count($items) / $per_page) + (self::page_rem($items, $per_page) === 0 ? 0 : 1);
    }

    /**
     * @param T[] $items
     */
    public static function page_rem(array $items, int $per_page = self::DEFAULT_PER_PAGE): int {
        return count($items) % $per_page;
    }
}
