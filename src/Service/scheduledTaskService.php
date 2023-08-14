<?php
namespace App\Service;

use HalloVerden\ScheduledTaskBundle\Annotations\ScheduledTask;

class scheduledTaskService
{
    private $userEnablerService;

    public function __construct(UserEnablerService $userEnablerService)
    {
        $this->userEnablerService = $userEnablerService;
    }

    /**
     * @ScheduledTask(cron="* * * * *")
     */
    public function myScheduledTask()
    {
        $this->userEnablerService->enableUsers();
    }
}
