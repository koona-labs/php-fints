<?php

namespace Abiturma\PhpFints\Models;

use Abiturma\PhpFints\Misc\HasAttributes;

/**
 * Class AbstractModel
 * @package Abiturma\PhpFints
 */
abstract class AbstractModel
{
    use HasAttributes;

    /**
     * AbstractModel constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }
}
