<?php

namespace Keepsuit\LaravelTemporal\Testing;

trait WithTemporalWorker
{
    protected function setUpWithTemporalWorker()
    {
        /** @var TemporalTestingEnvironment|null $temporalEnvironment */
        $temporalEnvironment = $GLOBALS['_temporal_environment'] ?? null;

        if ($temporalEnvironment !== null) {
            return;
        }

        $temporalEnvironment = TemporalTestingEnvironment::create();

        $temporalEnvironment->start(onlyWorker: true);

        $GLOBALS['_temporal_environment'] = $temporalEnvironment;

        register_shutdown_function(function () use ($temporalEnvironment) {
            $temporalEnvironment->stop();
            $GLOBALS['_temporal_environment'] = null;
        });
    }
}
