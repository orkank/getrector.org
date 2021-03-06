<?php

declare(strict_types=1);

namespace Rector\Website\Demo;

use Nette\Utils\Json;
use Rector\Website\Demo\Entity\RectorRun;
use Rector\Website\Demo\Process\RectorProcessRunner;
use Rector\Website\Demo\Utils\FileDiffCleaner;
use function Sentry\capture_exception;
use Symfony\Component\Stopwatch\Stopwatch;
use Throwable;

final class DemoRunner
{
    private RectorProcessRunner $rectorProcessRunner;

    private FileDiffCleaner $fileDiffCleaner;

    public function __construct(RectorProcessRunner $rectorProcessRunner, FileDiffCleaner $fileDiffCleaner)
    {
        $this->rectorProcessRunner = $rectorProcessRunner;
        $this->fileDiffCleaner = $fileDiffCleaner;
    }

    public function runAndPopulateRunResult(RectorRun $rectorRun): void
    {
        $stopwatch = new Stopwatch();
        $rectorProcessStopwatchEvent = $stopwatch->start('rector-process');

        try {
            $runResult = $this->rectorProcessRunner->run($rectorRun->getContent(), $rectorRun->getConfig());
            $fileDiff = $this->createFileDiff($runResult, $rectorRun);

            $rectorRun->success($fileDiff, Json::encode($runResult), $rectorProcessStopwatchEvent);
        } catch (Throwable $throwable) {
            $rectorRun->fail($throwable->getMessage(), $rectorProcessStopwatchEvent);

            // @TODO change to monolog
            // Log to sentry
            capture_exception($throwable);
        }
    }

    private function createFileDiff(array $runResult, RectorRun $rectorRun): string
    {
        $fileDiff = $runResult['file_diffs'][0]['diff'] ?? null;

        if ($fileDiff) {
            /** @var string $fileDiff */
            return $this->fileDiffCleaner->clean($fileDiff);
        }

        return $rectorRun->getContent();
    }
}
