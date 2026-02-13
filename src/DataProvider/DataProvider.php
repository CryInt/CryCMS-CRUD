<?php
namespace CryCMS\DataProvider;

use CryCMS\CRUDHelper;
use CryCMS\Db;
use CryCMS\Helpers;
use CryCMS\Thing;
use RuntimeException;

class DataProvider
{
    protected Thing $class;

    public int $page = 0;
    public string $pageBase = '/';
    public string $pageHref = '/';

    public array $params = [];
    public array $unUseInHref = [];

    public int $offset = 0;
    public int $limit = 15;

    public int $dataCount = 0;

    public array $filter = [];
    public array $filterSQL = [];
    public array $order = [];

    public array $select = [];
    public array $leftJoin = []; /** ['table', 'as', 'condition'] */
    public array $groupBy = [];

    public const NOT_NULL = 'NOT NULL';

    public function __construct($class)
    {
        if (!class_exists($class)) {
            throw new RuntimeException('Class is not exists');
        }

        $this->class = new $class();
    }

    /** @noinspection PhpUnused */
    public function initDefault(array $request = []): void
    {
        if (!empty($request['filter'])) {
            foreach ($request['filter'] as $key => $value) {
                if (!empty($value) && $this->class::issetField($key)) {
                    $this->filter[CRUDHelper::clean($key)] = CRUDHelper::clean($value);
                }
            }
        }

        $this->page = 0;
        if (!empty($request['page'])) {
            $this->page = (int)$request['page'];
            $this->offset = $this->page * $this->limit;
        }

        $pagerHrefGets = [];

        if (!empty($this->filter)) {
            foreach ($this->filter as $key => $value) {
                if (in_array($key, $this->unUseInHref, true)) {
                    continue;
                }

                $pagerHrefGets['filter[' . $key . ']'] = $value;
            }
        }

        if (!empty($this->params)) {
            foreach ($this->params as $key => $value) {
                $pagerHrefGets[$key] = $value;
            }
        }

        if (!empty($request['order'])) {
            [$orderKey, $orderDirection] = explode(':', $request['order']);
            if (
                !empty($orderKey) &&
                !empty($orderDirection) &&
                $this->class::issetField($orderKey) &&
                in_array($orderDirection, ['asc', 'desc'], true)
            ) {
                $this->order = [];
                $this->order[$orderKey] = mb_strtoupper($orderDirection, 'UTF-8');
                $pagerHrefGets['order'] = $orderKey . ':' . $orderDirection;
            }
        }

        $pagerHrefGets['page'] = '';
        $this->pageHref = $this->pageBase . '?' . http_build_query($pagerHrefGets);
    }

    public function get(): ?array
    {
        [$wheres, $values] = $this->prepareQuery();

        $query = Db::table($this->class::TABLE, 'primary_table')
            ->select($this->select)
            ->where($wheres)
            ->values($values)
            ->offset($this->offset)
            ->limit($this->limit)
            ->orderBy($this->order)
            ->groupBy($this->groupBy);

        $this->makeLeftJoin($query);

        $list = $query->getAll();

        if (!empty($list)) {
            $query = Db::table($this->class::TABLE, 'primary_table')
                ->select(['COUNT(*) AS num'])
                ->where($wheres)
                ->values($values)
                ->limit(1)
                ->groupBy($this->groupBy);

            $this->makeLeftJoin($query);

            $count = $query->getOne();

            $this->dataCount = $count['num'] ?? 0;

            return $this->getClass()::itemsObjects($list);
        }

        return null;
    }

    /** @noinspection PhpUnused */
    public function getFieldsCounts(string $field): array
    {
        [$wheres, $values] = $this->prepareQuery();

        return Db::table($this->class::TABLE)
            ->select([$field, 'COUNT(' . $field . ') AS count'])
            ->where($wheres)
            ->values($values)
            ->groupBy([$field])
            ->getAll();
    }

    public function getClass(): Thing
    {
        return $this->class;
    }

    protected function makeLeftJoin(Db $query): Db
    {
        if (!empty($this->leftJoin)) {
            foreach ($this->leftJoin as $leftJoin) {
                if (
                    array_key_exists('table', $leftJoin) &&
                    array_key_exists('as', $leftJoin) &&
                    array_key_exists('condition', $leftJoin)
                ) {
                    $query->leftJoin($leftJoin['table'], $leftJoin['as'], $leftJoin['condition']);
                }
            }
        }

        return $query;
    }

    protected function prepareQuery(): array
    {
        $wheres = $values = [];

        if (!empty($this->filter)) {
            foreach ($this->filter as $key => $value) {
                if (is_null($value)) {
                    $wheres[] = $key . ' IS NULL';
                    continue;
                }

                if (is_array($value)) {
                    $wheres[] = $key . ' IN (:' . self::cleanKey($key) . ')';
                    $values[self::cleanKey($key)] = $value;
                    continue;
                }

                if ($value === self::NOT_NULL) {
                    $wheres[] = $key . ' IS NOT NULL';
                    continue;
                }

                $compare = '=';

                if (mb_strpos($value, '%', 0, 'UTF-8') !== false) {
                    $compare = 'LIKE';
                }

                if (mb_strpos($value, '!', 0, 'UTF-8') === 0) {
                    $compare = '!=';
                    $value = mb_substr($value, 1, null, 'UTF-8');
                }

                if (mb_strpos($value, '>=', 0, 'UTF-8') === 0) {
                    $compare = '>=';
                    $value = mb_substr($value, 2, null, 'UTF-8');
                }

                if (mb_strpos($value, '>', 0, 'UTF-8') === 0) {
                    $compare = '>';
                    $value = mb_substr($value, 1, null, 'UTF-8');
                }

                if (mb_strpos($value, '<=', 0, 'UTF-8') === 0) {
                    $compare = '<=';
                    $value = mb_substr($value, 2, null, 'UTF-8');
                }

                if (mb_strpos($value, '<', 0, 'UTF-8') === 0) {
                    $compare = '<';
                    $value = mb_substr($value, 1, null, 'UTF-8');
                }

                $wheres[] = $key . ' ' . $compare . ' :' . self::cleanKey($key);
                $values[self::cleanKey($key)] = $value;
            }
        }

        if (!empty($this->filterSQL)) {
            foreach ($this->filterSQL as $filterSQL) {
                if (array_key_exists('condition', $filterSQL) && array_key_exists('values', $filterSQL)) {
                    $wheres[] = $filterSQL['condition'];

                    if (!empty($filterSQL['values'])) {
                        $values = array_merge($values, $filterSQL['values']);
                    }
                }
            }
        }

        return [$wheres, $values];
    }

    protected static function cleanKey(string $field): string
    {
        return str_replace('.', '_', $field);
    }
}
