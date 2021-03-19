<?php
/**
 * This file is part of Simps
 *
 * @link     https://github.com/simps/mqtt
 * @contact  Lu Fei <lufei@simps.io>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code
 */

declare(strict_types=1);

namespace Simps\MQTTCLI\Handler;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PublishHandler extends AbstractHandler
{
    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        $topic = $this->getTopic();
        $message = $this->getMessage();

        if (empty($topic) && empty($message)) {
            $this->logError('Please set topic and message');
            goto failure;
        }

        try {
            $client = $this->getMqttClient();
            $connect = $client->connect($this->getCleanSession(), $this->genWillData());
            $this->logInfo("connect {$this->getHost()} successfully, recv: " . json_encode($connect));
            $publish = $client->publish($topic, $message, $this->getQos(), $this->getDup(), $this->getRetain());
        } catch (\Throwable $e) {
            $this->logError("publish error: {$e->getMessage()}");
            goto failure;
        }
        if ($publish) {
            $this->logInfo("publish message '{$message}' to '{$topic}' successfully");

            return Command::SUCCESS;
        }
        $this->logError("publish message {$message} to {$topic} failed");

        failure:
        return Command::FAILURE;
    }
}
