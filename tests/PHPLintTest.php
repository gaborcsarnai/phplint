<?php
/**
 *
 */
class PHPLintTest extends PHPUnit_Framework_TestCase {
    /**
     * @return array
     */
    public function matchDataProvider() {
        return [
            ['Hello World!', '/wontfound/', 0],
            ['No capture', '/^No capture/', 1],
            ['No capture, no anchor', '/no anchor/', 1],
            ['Empty capture', '/^(.*)Empty/', 1],
            ['Empty capture', '/^(.+)Empty/', 0],
            ['Non-empty capture', '/^(.*)capture/', 11]
        ];
    }

    /**
     * Tests if {@see PHPLint::addRule()} returns the object instance also if
     * it has accepted the new rule.
     */
    public function testAddRuleMethod() {
        $linter = new PHPLint;
        $this->assertSame($linter, $linter->addRule('Don\'t say hello', '/^Hello/'));
        $this->assertSame(
            $linter->checkLine('Hello'),
            [['Don\'t say hello', 1, 1]]
        );
    }

    /**
     * Tests if {@see PHPLint::addRules()} returns the object instance also if
     * it has accepted all new rules.
     */
    public function testAddRulesMethod() {
        $linter = new PHPLint;
        $this->assertSame($linter, $linter->addRules([
            '/^Hello/' => 'Don\'t say hello',
            '/^Jump/' => 'Jump They Say'
        ]));
        $this->assertSame(
            $linter->checkLine('Hello'),
            [['Don\'t say hello', 1, 1]]
        );
        $this->assertSame(
            $linter->checkLine('Jump'),
            [['Jump They Say', 1, 1]]
        );
    }

    /**
     * @param string $line
     * @param string $pattern
     * @param int $result
     * @dataProvider matchDataProvider
     */
    public function testMatchMethod($line, $pattern, $result) {
        $this->assertSame((new PHPLint)->match($line, $pattern), $result);
    }

    /**
     * Basic tests of {@see PHPLint::checkLine()}: return value type & argument
     * defaults.
     */
    public function testCheckLineMethod() {
        $linter = new PHPLint;
        $this->assertSame($linter->checkLine(''), []);
        $this->assertSame(
            $linter->checkLine('if('),
            [['Expected space after control structure keyword', 1, 3]]
        );
        $this->assertSame(
            $linter->checkLine('if(', 5),
            [['Expected space after control structure keyword', 5, 3]]
        );
    }

    /**
     * Tests if {@see PHPLint::__invoke()} returns the same result as
     * {@see PHPLint::process()}.
     */
    public function testInvokeMagicMethod() {
        $linter = new PHPLint;
        $this->assertTrue($linter(__FILE__) === $linter->process(__FILE__));
    }

    /**
     * Tests if {@see PHPLint::process()} return type is array.
     */
    public function testProcessMethod() {
        $linter = new PHPLint;
        $this->assertTrue(is_array($linter->process(__FILE__)));
    }

    /**
     * Tests functional wrapper.
     */
    public function testWrapper() {
        // Testing if wrapper returns the same value as the OOP version.
        $this->assertTrue(phplint(__FILE__) == (new PHPLint)->process(__FILE__));

        // Testing file open error.
        $this->assertFalse(phplint(''));
    }
}
