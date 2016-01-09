<?php

namespace Krlove\Generator\Model;

/**
 * Class Property
 * @package Krlove\Generator\Model
 */
class Property
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $modifier;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var bool
     */
    protected $virtual;

    /**
     * @param string $name
     * @param string $type
     * @return Property
     */
    public static function createVirtualProperty($name, $type)
    {
        return new self($name, null, $type, null, true);
    }

    /**
     * Property constructor.
     * @param string|null $name
     * @param string|null $modifier
     * @param string|null $type
     * @param string|null $value
     * @param bool|false $virtual
     */
    public function __construct($name = null, $modifier = null, $type = null, $value = null, $virtual = false)
    {
        $this->setName($name);
        $this->setModifier($modifier);
        $this->setType($type);
        $this->setValue($value);
        $this->setVirtual($virtual);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * @param string $modifier
     *
     * @return $this
     */
    public function setModifier($modifier)
    {
        $this->modifier = $modifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isVirtual()
    {
        return $this->virtual;
    }

    /**
     * @param boolean $virtual
     *
     * @return $this
     */
    public function setVirtual($virtual)
    {
        $this->virtual = $virtual;

        return $this;
    }
}
