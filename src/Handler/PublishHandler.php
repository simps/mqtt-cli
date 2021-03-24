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

use Simps\MQTT\Hex\ReasonCode;
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
            $this->logInfo("Connect {$this->getHost()} successfully, recv: ");
            $this->log(json_encode($connect));
            if ($this->getConnectConfig()->isMQTT5()) {
                $this->logInfo("Connect Reason Code: {$connect['code']}, Reason: " . ReasonCode::getReasonPhrase($connect['code']));
            }
            $publish = $client->publish($topic, $message, $this->getQos(), $this->getDup(), $this->getRetain(), $this->getProperties('publish'));
        } catch (\Throwable $e) {
            $this->logError("Publish error, {$e->getMessage()}");
            goto failure;
        }

        if (is_array($publish)) {
            $this->logInfo("Publish message '{$message}' to '{$topic}', recv: ");
            $this->log(json_encode($publish));
            if ($this->getConnectConfig()->isMQTT5()) {
                $this->logInfo("Publish Reason Code: {$publish['code']}, Reason: " . ReasonCode::getReasonPhrase($publish['code']));
                if ($publish['code']) {
                    goto failure;
                }
            }

            return Command::SUCCESS;
        }

        if ($publish) {
            $this->logInfo("Publish message '{$message}' to '{$topic}' successfully");

            return Command::SUCCESS;
        }
        $this->logError("publish message {$message} to {$topic} failed");

        failure:
        return Command::FAILURE;
    }
}
