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

use Simps\MQTT\Client;
use Simps\MQTT\Config\ClientConfig;
use Simps\MQTT\Protocol\ProtocolInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractHandler
{
    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    abstract public function handle(InputInterface $input, OutputInterface $output): int;

    public function getProtocolName(): string
    {
        if ($this->getProtocolLevel() === ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1) {
            return ProtocolInterface::MQISDP_PROTOCOL_NAME;
        }

        return ProtocolInterface::MQTT_PROTOCOL_NAME;
    }

    public function getProtocolLevel(): int
    {
        return (int) $this->input->getOption('level');
    }

    public function getUserName(): string
    {
        return (string) $this->input->getOption('username');
    }

    public function getPassword(): string
    {
        return (string) $this->input->getOption('pw');
    }

    public function getClientId(): string
    {
        return $this->input->getOption('id') ?: Client::genClientID();
    }

    public function getHost(): string
    {
        return $this->input->getOption('host');
    }

    public function getPort(): int
    {
        return (int) $this->input->getOption('port');
    }

    public function getCleanSession(): bool
    {
        return (bool) $this->input->getOption('clean-session');
    }

    public function getSSL(): bool
    {
        return (bool) $this->input->getOption('ssl');
    }

    public function getSocketType(): int
    {
        if ($this->getSSL()) {
            return SWOOLE_SOCK_TCP | SWOOLE_SSL;
        }

        return SWOOLE_SOCK_TCP;
    }

    public function getKeepAlive(): int
    {
        return (int) $this->input->getOption('keepalive');
    }

    public function getTopic(): string
    {
        return (string) $this->input->getOption('topic');
    }

    public function getSubTopic(): array
    {
        return $this->input->getOption('topic');
    }

    public function getSubQos(): array
    {
        $qos = $this->input->getOption('qos');

        $subQos = [];
        if ($this->getProtocolLevel() === ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0) {
            foreach ($qos as $item) {
                $subQos[] = ['qos' => (int) $item];
            }
        } else {
            foreach ($qos as $item) {
                $subQos[] = (int) $item;
            }
        }

        return $subQos;
    }

    public function getMessage(): string
    {
        return (string) $this->input->getOption('message');
    }

    public function getQos(): int
    {
        return (int) $this->input->getOption('qos');
    }

    public function getDup(): int
    {
        return (int) $this->input->getOption('dup');
    }

    public function getRetain(): int
    {
        return (int) $this->input->getOption('retain');
    }

    public function getWillMessage(): string
    {
        return (string) $this->input->getOption('will-message');
    }

    public function getWillTopic(): string
    {
        return (string) $this->input->getOption('will-topic');
    }

    public function getWillQos(): int
    {
        return (int) $this->input->getOption('will-qos');
    }

    public function getWillRetain(): int
    {
        return (int) $this->input->getOption('will-retain');
    }

    public function getConnectConfig(): ClientConfig
    {
        $config = new ClientConfig();

        return $config->setUserName($this->getUserName())
            ->setPassword($this->getPassword())
            ->setClientId($this->getClientId())
            ->setKeepAlive($this->getKeepAlive())
            ->setProtocolName($this->getProtocolName())
            ->setProtocolLevel($this->getProtocolLevel())
            ->setSockType($this->getSocketType())
            ->setSwooleConfig($this->getSwooleConfig())
            ->setProperties($this->getProperties('connect'))
            ->setMaxAttempts(0); // Disable auto reconnection
    }

    public function getMqttClient(string $host, int $port, ClientConfig $config): Client
    {
        return new Client($host, $port, $config);
    }

    public function genWillData(): array
    {
        $topic = $this->getWillTopic();
        if (!empty($topic)) {
            return [
                'topic' => $this->getWillTopic(),
                'message' => $this->getWillMessage(),
                'qos' => $this->getWillQos(),
                'retain' => $this->getWillRetain(),
                'properties' => $this->getProperties('will'),
            ];
        }

        return [];
    }

    public function getSwooleConfig(): array
    {
        $path = $this->input->getOption('config-path');
        if ($path && is_file($path)) {
            $content = include $path;

            return $content;
        }

        return [];
    }

    public function getProperties(string $key = ''): array
    {
        $path = $this->input->getOption('properties-path');
        if ($path && is_file($path)) {
            $content = include $path;

            if ($key) {
                return $content[$key] ?? [];
            }

            return $content;
        }

        return [];
    }

    public function getUnsubscribe(): array
    {
        return $this->input->getOption('unsubscribe');
    }

    protected function log($msg): void
    {
        $date = date('Y-m-d H:i:s');
        $this->output->writeln("[{$date}]: {$msg}");
    }

    protected function logInfo($msg): void
    {
        $date = date('Y-m-d H:i:s');
        $this->output->writeln("<info>[{$date}]: {$msg}</info>");
    }

    protected function logError($msg): void
    {
        $date = date('Y-m-d H:i:s');
        $this->output->writeln("<error>[{$date}]: {$msg}</error>");
    }
}
