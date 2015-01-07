<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the array list parser
 */
namespace RDev\Console\Requests\Parsers;

class ArrayListTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArrayList The parser to use in tests */
    private $parser = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->parser = new ArrayList();
    }

    /**
     * Test not passing arguments
     */
    public function testNotPassingArguments()
    {
        $request = $this->parser->parse([
            "name" => "foo",
            "options" => ["--name=dave", "-r"]
        ]);
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue("r"));
        $this->assertEquals("dave", $request->getOptionValue("name"));
    }

    /**
     * Test not passing the command name
     */
    public function testNotPassingCommandName()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->parser->parse([
            "foo" => "bar"
        ]);
    }

    /**
     * Test not passing options
     */
    public function testNotPassingOptions()
    {
        $request = $this->parser->parse([
            "name" => "foo",
            "arguments" => ["bar"]
        ]);
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals(["bar"], $request->getArgumentValues());
        $this->assertEquals([], $request->getOptionValues());
    }

    /**
     * Tests parsing arguments and options
     */
    public function testParsingArgumentsAndOptions()
    {
        $request = $this->parser->parse([
            "name" => "foo",
            "arguments" => ["bar"],
            "options" => ["--name=dave", "-r"]
        ]);
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals(["bar"], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue("r"));
        $this->assertEquals("dave", $request->getOptionValue("name"));
    }

    /**
     * Test passing the command name
     */
    public function testPassingCommandName()
    {
        $request = $this->parser->parse([
            "name" => "mycommand"
        ]);
        $this->assertEquals("mycommand", $request->getCommandName());
    }
}