<?php

namespace srag\DIC\DIC;

use ILIAS\DI\Container;
use srag\DIC\Database\DatabaseDetector;
use srag\DIC\Database\DatabaseInterface;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\DIC
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractDIC implements DICInterface
{

    /**
     * @var Container
     */
    protected $dic;


    /**
     * @inheritDoc
     */
    public function __construct(Container &$dic)
    {
        $this->dic = &$dic;
    }


    /**
     * @inheritDoc
     */
    public function database() : DatabaseInterface
    {
        return DatabaseDetector::getInstance($this->databaseCore());
    }
}
