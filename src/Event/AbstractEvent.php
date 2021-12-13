<?php

declare(strict_types=1);
/**
 * This file is part of Simps.
 *
 * @link     https://github.com/simps/mqtt-cli
 * @contact  Lu Fei <lufei@simps.io>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Simps\MQTTCLI\Event;

use Simps\MQTT\Client;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    /** @var Client */
    protected $client;

    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    public function __construct(Client $client, InputInterface $input, OutputInterface $output)
    {
        $this->client = $client;
        $this->input = $input;
        $this->output = $output;
        $this->handle();
    }

    abstract public function handle();
}
