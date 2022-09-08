<?php

namespace Keepsuit\LaravelTemporal\Testing\Fakes;

use Illuminate\Support\Str;
use Keepsuit\LaravelTemporal\Testing\TemporalMocker;
use Temporal\Client\WorkflowClient;
use Temporal\Internal\Client\WorkflowProxy;
use Temporal\Workflow\WorkflowExecution;
use Temporal\Workflow\WorkflowRunInterface;
use Temporal\Workflow\WorkflowStub as WorkflowStubConverter;

class FakeWorkflowClient extends WorkflowClient
{
    /**
     * @param  WorkflowProxy  $workflow
     */
    public function start($workflow, ...$args): WorkflowRunInterface
    {
        $workflowStub = WorkflowStubConverter::fromWorkflow($workflow);

        $workflowMock = $this->getTemporalMocker()->getWorkflowResult($workflowStub->getWorkflowType());

        if (! ($workflowMock instanceof \Closure)) {
            return parent::start($workflow, ...$args);
        }

        $this->getTemporalMocker()->recordWorkflowDispatch($workflowStub->getWorkflowType(), $args);

        $execution = new WorkflowExecution(Str::uuid(), Str::uuid());

        $workflowStub->setExecution($execution);

        $result = $workflowMock->__invoke(...$args);

        return new FakeWorkflowRun($workflowStub, $result);
    }

    protected function getTemporalMocker(): TemporalMocker
    {
        return app(TemporalMocker::class);
    }
}
