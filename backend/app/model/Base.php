<?php


namespace app\model;

use think\Model;

/**
 * 模型基类
 */
class Base extends Model
{

    /**
     * @param array $field
     * @param array $where
     * @param array $append
     * @param bool  $relation
     * @param bool  $order
     * @param bool  $paginate
     */
    public function _lists($field = [], $where = [], $append = [], $relation = false, $order = false, $paginate = false)
    {
        $model = $this;
        //$model = $this;
        if (empty($field)) {
            $field = '*';
        }
        $model = $model->field($field);
        // 查询
        foreach ($where as $key => $item) {
            if ($key == '_search') {
                $model = $model->withSearch(['keywords'], ['keywords' => $item]);
                continue;
            } else if ($key == '_onlytrashed') {
                $model = $model->onlyTrashed();
                continue;
            } else if ($key == '_string') {
                $model = $model->where($item);
                continue;
            } else if ($key == '_withSearch') {
                foreach ($item as $search_key => $search_val) {
                    $model = $model->withSearch([$search_key], [$search_key => $search_val]);
                }
                continue;
            }
            if (is_array($item)) {
//                if (!in_array($item[0], ['in', 'exp', 'like', 'notlike', 'between'])) continue;
                switch (strtolower($item[0])) {
                    case 'in':
                        $model = $model->whereIn($key, $item[1]);
                        break;
                    case 'exp':
                        $model = $model->whereExp($key, $item[1]);
                        break;
                    case 'like':
                        $model = $model->whereLike($key, $item[1]);
                        break;
                    case 'notlike':
                        $model = $model->whereNotLike($key, $item[1]);
                        break;
                    case 'between':
                        $model = $model->whereBetween($key, $item[1]);
                        break;
                    case 'exists':
                        $model = $model->whereExists($item[1]);
                        break;
                    case 'year':
                        $model = $model->whereYear($key, $item[1]);
                        break;
                    case 'time':
                        $model = $model->whereTime($key, $item[1], $item[2]);
                        break;
                    default:
                        $model = $model->where($key, $item[0], $item[1]);
                        break;
                }
            } else if (is_callable($item)) {
                // 回调
            } else {
                $model = $model->where($key, $item);
            }
        }
        // 关联预加载
        if ($relation) {
            // 组合数据
            $with = [];
            $withJoin = [];
            if (is_array($relation)) {
                foreach ($relation as $key => $item) {
                    if (is_string($item) && is_integer($key)) {
                        $value = explode(':', $item);
                        $item = $value[0];
                    } else {
                        $value = explode(':', $key);
                    }
                    $join = $value[1] ?? '';
                    $join = explode('.', $join);
                    $joinStr = $join[0];
                    $joinType = $join[1] ?? 'inner';
                    if (is_callable($item) || is_array($item)) {
                        if ($joinStr == 'join') {
                            $withJoin[$joinType][$value[0]] = $item;
                        } else {
                            $with[] = [$value[0] => $item];
                        }
                    } else {
                        if ($joinStr == 'join') {
                            $withJoin[$joinType][] = $item;
                        } else {
                            $with[] = $item;
                        }
                    }
                }

                if (count($with) > 0) {
                    $model = $model->with($with);
                }
                if (count($withJoin) > 0) {
                    foreach ($withJoin as $key => $item) {
                        $model = $model->withJoin($item, $key);
                    }
                }
            } else {
            }
        }
        // 排序
        if ($order) {
            $model = $model->order($order);
        }
        if ($paginate) {
            $lists = $model->paginate($paginate);
        } else {
            $lists = $model->select();
        }
        // 获取器
        if ($append) {
            $lists = $lists->append($append);
        }

        return $lists;
    }

}