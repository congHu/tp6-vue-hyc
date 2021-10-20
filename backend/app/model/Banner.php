<?php

namespace app\model;

use app\model\Base;
use think\model\concern\SoftDelete;

class Banner extends Base
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

}
