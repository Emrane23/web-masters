<?php 
namespace App\ScheduledTask ;
use HalloVerden\ScheduledTaskBundle\Interfaces\TaskHandlerInterface;

class UserEnablerTaskHandler implements TaskHandlerInterface {

    public function __invoke(UserEnablerTask $task) {
        $userEnablerService = $task->getUserEnablerService();
        $userEnablerService->enableUsers(); // Appel de la méthode pour activer les utilisateurs
    }
}
