<?php

namespace spec\Monashee\Backup;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Illuminate\Events\Dispatcher;
use Illuminate\Config\Repository;

class DumpSpec extends ObjectBehavior
{
    private $prophet;

    function let(Dispatcher $dispatcher, Repository $repository)
    {
        $this->beConstructedWith($dispatcher, $repository);
        $this->prophet = new \Prophecy\Prophet;
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Monashee\Backup\Dump');
    }

    function it_creates_a_database_and_saves_to_storage_filesystem()
    {
        $database = $this->prophet->prophesize('Monashee\Backup\Dump');
        $database->backup('homestead')->willReturn(true);
    }
}
