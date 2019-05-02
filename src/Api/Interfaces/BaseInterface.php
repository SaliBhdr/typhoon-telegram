<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:47 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Interfaces;


interface BaseInterface
{
    public function setParams(array $params = []);

    public function getParams() : array;

}