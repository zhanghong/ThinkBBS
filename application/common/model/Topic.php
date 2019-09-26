<?php

namespace app\common\model;

use think\Model;

class Topic extends Model
{
    // belongs to user
    public function user()
    {
        return $this->belongsTo('User');
    }

    // belongs to category
    public function category()
    {
        return $this->belongsTo('Category');
    }

    /**
     * 分页查询方法
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-06-20
     * @param    array              $params    请求参数
     * @param    integer            $page_rows 每页显示数量
     * @return   [type]                        分页查询结果
     */
    public static function minePaginate($param = [], $per_page = 20)
    {
        $static = static::with('user,category');
        foreach ($param as $name => $value) {
            if(empty($value)){
                continue;
            }
            switch ($name) {
                case 'category_id':
                    $static = $static->where($name, intval($value));
                    break;
            }
        }

        return $static->paginate($per_page);
    }
}