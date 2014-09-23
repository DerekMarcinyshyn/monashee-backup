<?php

namespace spec\Monashee\Backup;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Illuminate\Events\Dispatcher;
use Illuminate\Config\Repository;


class DatabaseSpec extends ObjectBehavior
{
    private $prophet;

    function let(Dispatcher $dispatcher, Repository $repository)
    {
        $this->beConstructedWith($dispatcher, $repository);
        $this->prophet = new \Prophecy\Prophet;
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Monashee\Backup\Database');
    }

    function it_gets_databases()
    {
        $database = $this->prophet->prophesize('Monashee\Backup\Database');
        $database->getDatabases()->willReturn([
            0 => 'homestead'
        ]);
    }
}
