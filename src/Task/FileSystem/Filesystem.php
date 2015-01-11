<?php
namespace Robo\Task\FileSystem;

use Robo\Result;
use Robo\Task\Shared\Stackable;
use Symfony\Component\Filesystem\Filesystem as sfFileSystem;
use Robo\Task\Shared\TaskInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Wrapper for [Symfony FileSystem](http://symfony.com/doc/current/components/filesystem.html) Component.
 * Comands are executed in stack and can be stopped on first fail with `stopOnFail` option.
 *
 * ``` php
 * <?php
 * $this->taskFileSystemStack()
 *      ->mkdir('logs')
 *      ->touch('logs/.gitignore')
 *      ->chgrp('www', 'www-data')
 *      ->symlink('/var/log/nginx/error.log', 'logs/error.log')
 *      ->run();
 *
 * // one line
 * taskFileSystem::_touch('.gitignore');
 * taskFileSystem::_mkdir('logs');
 *
 * ?>
 * ```
 *
 * Class FileSystemStackTask
 * @package Robo\Task
 */
class Filesystem implements TaskInterface
{
    use \Robo\Output;
    use Stackable;

    protected $stack = [];

    protected $stopOnFail = false;

    public function mkdir($dir)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function touch($file)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function copy($from, $to, $force = false)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function chmod($file, $permissions, $umask = 0000, $recursive = false)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function remove($file)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function rename($from, $to)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function symlink($from, $to)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function mirror($from, $to)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function chgrp($file, $group)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function chown($file, $user)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function run()
    {
        $fs = new sfFileSystem();
        $code = 0;
        foreach ($this->stack as $action) {
            $command = array_shift($action);
            if (!method_exists($fs, $command)) {
                continue;
            }
            $this->printTaskInfo("$command " . json_encode($action));
            try {
                call_user_func_array([$fs, $command], $action);
            } catch (IOExceptionInterface $e) {
                if ($this->stopOnFail) {
                    return Result::error($this, $e->getMessage(), $e->getPath());
                }
                $code = 1;
                $this->printTaskInfo("<error>" . $e->getMessage() . "</error>");
            }
        }
        return new Result($this, $code);
    }

}