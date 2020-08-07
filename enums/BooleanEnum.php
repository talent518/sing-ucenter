<?php
/**
 * Created by PhpStorm.
 * User: 张冰
 * Date: 2019/9/10
 * Time: 16:15
 */

namespace app\enums;


class BooleanEnum extends Enum
{
    const 是 = 1;
    const 否 = 0;

    function getDisplayNames()
    {
        return [self::否 => '不是',
            self::是 => '是的'];
    }

    const IS_DEL_FALSE = 0;
    const IS_DEL_TRUE = 1;
}