<?php


namespace EasySwoole\Phpunit;


use PHPUnit\TextUI\Command;
use Swoole\ExitException;
use Swoole\Timer;
use Swoole\Coroutine\Scheduler;

class Runner
{
    public static function run($noCoroutine = true)
    {
        if($noCoroutine){
            try{
                Command::main(false);
            }catch (\Throwable $throwable){
                /*
                 * 屏蔽swoole exit报错
                 */
                if(!$throwable instanceof ExitException){
                    throw $throwable;
                }
            }finally{
                Timer::clearAll();
            }
        }else{
            $scheduler = new Scheduler();
            $scheduler->add(function() {
                Runner::run();
            });
            $scheduler->start();
        }

    }
}