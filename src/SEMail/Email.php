<?php
/**
     * Created by PhpStorm.
     * User: ssgonchar
     * Date: 18.01.2016
     * Time: 5:29
     */

namespace SSGonchar\FastModel\SEMail;

use SSGonchar\FastModel\SEModel\Model;

/**
 * Class Email
 * @package SSGonchar\FastModel\SEMail
 */
class Email extends Model
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct('emails');
    }

    public function getList()
    {
        return $this->SelectList();
    }

    public function get($id)
    {
        return $this->Select($id);
    }
}