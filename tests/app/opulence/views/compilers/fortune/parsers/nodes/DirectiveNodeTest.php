<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the directive node
 */
namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

class DirectiveNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the "is a" methods
     */
    public function testIsAMethods()
    {
        $node = new DirectiveNode();
        $this->assertFalse($node->isComment());
        $this->assertTrue($node->isDirective());
        $this->assertFalse($node->isDirectiveName());
        $this->assertFalse($node->isExpression());
        $this->assertFalse($node->isSanitizedTag());
        $this->assertFalse($node->isUnsanitizedTag());
    }
}