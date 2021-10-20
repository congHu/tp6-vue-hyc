<?php

namespace app\model;

use app\model\Base;
use think\model\concern\SoftDelete;

class Image extends Base
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

}
