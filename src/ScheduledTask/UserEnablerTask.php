<?php

namespace App\ScheduledTask;

use HalloVerden\ScheduledTaskBundle\Interfaces\AsyncTaskInterface;
use App\Service\UserEnablerService;
use HalloVerden\ScheduledTaskBundle\Entity\SimpleCronExpression;
use HalloVerden\ScheduledTaskBundle\Interfaces\ScheduleInterface;
use HalloVerden\ScheduledTaskBundle\Interfaces\SyncTaskInterface;

class UserEnablerTask implements SyncTaskInterface {

    private $userEnablerService;

    public function __construct(UserEnablerService $userEnablerService) {
        $this->userEnablerService = $userEnablerService;
    }
    
    /**
     * @ScheduledTask(cron="* * * * *") // Add this annotation
     */
    public function getSchedule(): ScheduleInterface {
        return SimpleCronExpression::create('* * * * *'); 
    }

    public function getName(): string {
        return 'user-enabler-task';
    }

    public function getUserEnablerService(): UserEnablerService {
        return $this->userEnablerService;
    }
}
