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
namespace Simps\MQTTCLI\Handler;

use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Protocol\ProtocolInterface;
use Simps\MQTT\Protocol\Types;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SubscribeHandler extends AbstractHandler
{
    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        $topic = $this->getSubTopic();
        $qos = $this->getSubQos();

        if (empty($topic)) {
            $this->logError('The topic you need to subscribe to cannot be empty');
            goto failure;
        }

        $topic_num = count($topic);
        $qos_num = count($qos);
        $config = $this->getConnectConfig();

        if ($topic_num !== 0 && $qos_num === 0) {
            $qos_data = ProtocolInterface::MQTT_QOS_0;
            if ($config->isMQTT5()) {
                $qos_data = ['qos' => $qos_data];
            }
            $qos = array_fill(0, $topic_num, $qos_data);
            $qos_num = $topic_num;
        }

        if ($topic_num !== $qos_num) {
            $this->logError("The number of topics to subscribe to and the number of qos do not match. topic[{$topic_num}], qos[{$qos_num}]");
            goto failure;
        }

        $subscribe = array_combine($topic, $qos);

        try {
            $client = $this->getMqttClient($this->getHost(), $this->getPort(), $config);
            $connect = $client->connect($this->getCleanSession(), $this->genWillData());
            $this->logInfo("Connect {$this->getHost()} successfully, recv: ");
            $this->log(json_encode($connect));
            if ($config->isMQTT5()) {
                $this->logInfo("Connect Reason Code: {$connect['code']}, Reason: " . ReasonCode::getReasonPhrase($connect['code']));
            }

            // unsubscribe
            $un_subscribe = $this->getUnsubscribe();
            if (!empty($un_subscribe)) {
                $un_subscribe_res = $client->unSubscribe($un_subscribe, $this->getProperties('unsubscribe'));
                $this->log(json_encode($un_subscribe_res));

                $unsub_ack_data = array_combine($topic, $un_subscribe_res['codes']);
                foreach ($unsub_ack_data as $key => $code) {
                    $this->logInfo("UnSubscribe [{$key}], Reason Code: {$code}, Reason: " . ReasonCode::getReasonPhrase($code));
                }

                if (empty($subscribe)) {
                    return Command::SUCCESS;
                }
            }

            // subscribe
            $sub_ack = $client->subscribe($subscribe, $this->getProperties('subscribe'));
            $this->log(json_encode($sub_ack));
            if (is_array($sub_ack)) {
                if ($sub_ack['type'] === Types::SUBACK) {
                    $sub_ack_data = array_combine($topic, $sub_ack['codes']);
                    foreach ($sub_ack_data as $key => $code) {
                        $this->logInfo("Subscribe [{$key}], Reason Code: {$code}, Reason: " . ReasonCode::getReasonPhrase($code, $code <= ReasonCode::GRANTED_QOS_2));
                    }
                }
                if (isset($sub_ack['code'])) {
                    $client->close();
                    $this->logError('Subscribe error, ' . ReasonCode::getReasonPhrase($sub_ack['code']));
                    goto failure;
                }
            }

            $timeSincePing = time();
            while (true) {
                $buffer = $client->recv();
                if ($buffer && $buffer !== true) {
                    $this->log(json_encode($buffer));
                    $event = $this->getEvent();
                    if (!empty($event) && class_exists($event)) {
                        (new EventDispatcher())->dispatch(new $event($client, $input, $output));
                    }
                }
                if ($timeSincePing <= (time() - $client->getConfig()->getKeepAlive())) {
                    $buffer = $client->ping();
                    if ($buffer) {
                        $timeSincePing = time();
                        $this->logInfo('Send ping success');
                    }
                }
            }
        } catch (\Throwable $e) {
            $client->close();
            $this->logError("Subscribe error, {$e->getMessage()}");
        }

        failure:
        return Command::FAILURE;
    }
}
