<?php
namespace Robo\Task\Bower;

use Robo\Task\Bower;
use Robo\Task\Shared\TaskInterface;
use Robo\Task\Shared\CommandInterface;

/**
 * Bower Install
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskBowerInstall()->run();
 *
 * // prefer dist with custom path
 * $this->taskBowerInstall('path/to/my/bower')
 *      ->noDev()
 *      ->run();
 * ?>
 * ```
 */
class Install extends Base implements TaskInterface, CommandInterface
{
    protected $action = 'install';

    public function run()
    {
        $this->printTaskInfo('Install Bower packages: ' . $this->arguments);
        return $this->executeCommand($this->getCommand());
    }
}