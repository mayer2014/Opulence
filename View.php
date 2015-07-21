<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a basic view
 */
namespace Opulence\Views;
use InvalidArgumentException;

class View implements IView
{
    /** @var string The uncompiled contents of the view */
    protected $contents = "";
    /** @var array The mapping of PHP variable names to their values */
    protected $vars = [];

    /**
     * @param string $contents The contents of the view
     */
    public function __construct($contents = "")
    {
        $this->setContents($contents);
    }

    /**
     * @inheritdoc
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @inheritdoc
     */
    public function getVar($name)
    {
        if(isset($this->vars[$name]))
        {
            return $this->vars[$name];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * @inheritdoc
     */
    public function setContents($contents)
    {
        if(!is_string($contents))
        {
            throw new InvalidArgumentException("Contents are not a string");
        }

        $this->contents = $contents;
    }

    /**
     * @inheritdoc
     */
    public function setVar($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * @inheritdoc
     */
    public function setVars(array $namesToValues)
    {
        foreach($namesToValues as $name => $value)
        {
            $this->setVar($name, $value);
        }
    }
}