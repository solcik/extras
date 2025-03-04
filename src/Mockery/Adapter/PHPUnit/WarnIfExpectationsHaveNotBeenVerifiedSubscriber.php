<?php

declare(strict_types=1);

namespace Solcik\Mockery\Adapter\PHPUnit;

use LogicException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;

class WarnIfExpectationsHaveNotBeenVerifiedSubscriber implements FinishedSubscriber
{
    public function notify(Finished $event): void
    {
        try {
            // The self() call is used as a sentinel. Anything that throws if
            // the container is closed already will do.
            Mockery::self();
        } catch (LogicException) {
            return;
        }

        EventFacade::emitter()->testConsideredRisky(
            $event->test(),
            sprintf(
                'Mockery\'s expectations have not been verified. Make sure that \Mockery::close() is called at the end of the test. ' .
                'Consider using trait \'%s\' or extending class \'%s\'.',
                MockeryPHPUnitIntegration::class,
                MockeryTestCase::class,
            )
        );
    }
}
