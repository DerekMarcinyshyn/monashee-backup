<?php

namespace spec\Monashee\Backup;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Illuminate\Events\Dispatcher;
use Illuminate\Config\Repository;

class UploadS3Spec extends ObjectBehavior
{
    private $prophet;

    function let(Dispatcher $dispatcher, Repository $repository)
    {
        $this->beConstructedWith($dispatcher, $repository);
        $this->prophet = new \Prophecy\Prophet;
    }

    function it_can_upload_to_s3()
    {
        $s3 = $this->prophet->prophesize('Monashee\Backup\UploadS3');
        $s3->uploadToS3()->willReturn(true);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Monashee\Backup\UploadS3');
    }
}
