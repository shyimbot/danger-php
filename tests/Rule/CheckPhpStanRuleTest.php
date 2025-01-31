<?php
declare(strict_types=1);

namespace Danger\Tests\Rule;

use Danger\Context;
use Danger\Platform\Github\Github;
use Danger\Rule\CheckPhpStanRule;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CheckPhpStanRuleTest extends TestCase
{
    public function testValid(): void
    {
        $github = $this->createMock(Github::class);
        $context = new Context($github);

        $rule = new CheckPhpStanRule();
        $rule($context);

        static::assertFalse($context->hasFailures());
    }

    public function testInvalid(): void
    {
        $github = $this->createMock(Github::class);
        $context = new Context($github);

        $path = dirname(__DIR__, 2) . '/src/Test.php';

        file_put_contents($path, '<?php str_contains(new ArrayObject());');

        $rule = new CheckPhpStanRule();
        $rule($context);

        static::assertTrue($context->hasFailures());

        \unlink($path);
    }
}
