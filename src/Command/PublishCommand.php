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

namespace Simps\MQTTCLI\Command;

use Simps\MQTTCLI\Handler\PublishHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PublishCommand extends Command
{
    protected static $defaultName = 'publish';

    protected function configure()
    {
        $this->setDescription('Publishing simple messages')
            ->setHelp('An MQTT version 3.1/3.1.1/5.0 client for publishing simple messages')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('host', 'H', InputOption::VALUE_OPTIONAL, 'Specify the host to connect to', 'localhost'),
                    new InputOption('port', 'P', InputOption::VALUE_OPTIONAL, 'Connect to the port specified', 1883),
                    new InputOption('topic', 't', InputOption::VALUE_REQUIRED, 'The MQTT topic on which to publish the message'),
                    new InputOption('message', 'm', InputOption::VALUE_REQUIRED, 'Send a single message from the command line'),
                    new InputOption('id', 'i', InputOption::VALUE_OPTIONAL, 'The id to use for this client', ''),
                    new InputOption('qos', null, InputOption::VALUE_OPTIONAL, 'Specify the quality of service to use for the message, from 0, 1 and 2', 0),
                    new InputOption('dup', null, InputOption::VALUE_OPTIONAL, 'If the DUP flag is set to 0, it indicates that this is the first occasion that the Client or Server has attempted to send this PUBLISH packet', 0),
                    new InputOption('retain', 'r', InputOption::VALUE_OPTIONAL, 'If the RETAIN flag is set to 1 in a PUBLISH packet sent by a Client to a Server, the Server MUST replace any existing retained message for this topic and store the Application Message', 0),
                    new InputOption('username', 'u', InputOption::VALUE_OPTIONAL, 'Provide a username to be used for authenticating with the broker'),
                    new InputOption('pw', 'p', InputOption::VALUE_OPTIONAL, 'Provide a password to be used for authenticating with the broker'),
                    new InputOption('clean-session', 'c', InputOption::VALUE_OPTIONAL, "Setting the 'clean session' flag", true),
                    new InputOption('level', 'l', InputOption::VALUE_REQUIRED, 'MQTT Protocol level', 4),
                    new InputOption('keepalive', 'k', InputOption::VALUE_OPTIONAL, 'The number of seconds between sending PING commands to the broker for the purposes of informing it we are still connected and functioning', 0),
                    new InputOption('will-topic', null, InputOption::VALUE_OPTIONAL, 'The topic on which to send a Will, in the event that the client disconnects unexpectedly'),
                    new InputOption('will-message', null, InputOption::VALUE_OPTIONAL, 'Specify a message that will be stored by the broker and sent out if this client disconnects unexpectedly'),
                    new InputOption('will-qos', null, InputOption::VALUE_OPTIONAL, 'The QoS to use for the Will', 0),
                    new InputOption('will-retain', null, InputOption::VALUE_OPTIONAL, 'If given, if the client disconnects unexpectedly the message sent out will be treated as a retained message', 0),
                    new InputOption('ssl', 'S', InputOption::VALUE_OPTIONAL, 'Enable SSL encryption', false),
                    new InputOption('config-path', null, InputOption::VALUE_OPTIONAL, 'Setting the Swoole config file path'),
                    new InputOption('properties-path', null, InputOption::VALUE_OPTIONAL, 'Setting the Properties config file path'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return (new PublishHandler())->handle($input, $output);
    }
}
