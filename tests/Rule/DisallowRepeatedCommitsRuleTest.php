<?php
declare(strict_types=1);

namespace Danger\Tests\Rule;

use Danger\Component\Platform\Github\Github;
use Danger\Component\Struct\Commit;
use Danger\Component\Struct\CommitCollection;
use Danger\Component\Struct\Github\PullRequest;
use Danger\Context;
use Danger\Rule\DisallowRepeatedCommitsRule;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class DisallowRepeatedCommitsRuleTest extends TestCase
{
    public function testRuleMatches(): void
    {
        $commit = new Commit();
        $commit->message = 'Test';

        $secondCommit = new Commit();
        $secondCommit->message = 'Test';

        $github = $this->createMock(Github::class);
        $pr = $this->createMock(PullRequest::class);
        $pr->method('getCommits')->willReturn(new CommitCollection([$commit, $secondCommit]));
        $github->pullRequest = $pr;

        $context = new Context($github);

        $rule = new DisallowRepeatedCommitsRule();
        $rule($context);

        static::assertTrue($context->hasFailures());
    }

    public function testRuleNotMatches(): void
    {
        $commit = new Commit();
        $commit->message = 'Test';

        $secondCommit = new Commit();
        $secondCommit->message = 'Test2';

        $github = $this->createMock(Github::class);
        $pr = $this->createMock(PullRequest::class);
        $pr->method('getCommits')->willReturn(new CommitCollection([$commit, $secondCommit]));
        $github->pullRequest = $pr;

        $context = new Context($github);

        $rule = new DisallowRepeatedCommitsRule();
        $rule($context);

        static::assertFalse($context->hasFailures());
    }
}
