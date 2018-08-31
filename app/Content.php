<?php
/**
 * Created by PhpStorm.
 * User: who
 * Date: 8/31/18
 * Time: 3:09 AM
 */

namespace App;

use PhpOffice\PhpWord\Element\Text;

class Content extends Text
{
    public $text;

    public function __construct(Text $text)
    {
        $this->text = $text->getText();
    }
}