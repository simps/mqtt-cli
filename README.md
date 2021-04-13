# PHPMQTT CLI

```
  ____  _   _ ____  __  __  ___ _____ _____
 |  _ \| | | |  _ \|  \/  |/ _ \_   _|_   _|
 | |_) | |_| | |_) | |\/| | | | || |   | |
 |  __/|  _  |  __/| |  | | |_| || |   | |
 |_|   |_| |_|_|   |_|  |_|\__\_\|_|   |_|
```

## Install

```bash
composer require simps/mqtt-cli
```

## Usage

### Publish

```bash
$ php vendor/bin/mqtt publish --help
Description:
  Publishing simple messages

Usage:
  publish [options]

Options:
  -H, --host[=HOST]                        Specify the host to connect to [default: "localhost"]
  -P, --port[=PORT]                        Connect to the port specified [default: 1883]
  -t, --topic=TOPIC                        The MQTT topic on which to publish the message
  -m, --message=MESSAGE                    Send a single message from the command line
  -i, --id[=ID]                            The id to use for this client [default: ""]
      --qos[=QOS]                          Specify the quality of service to use for the message, from 0, 1 and 2 [default: 0]
      --dup[=DUP]                          If the DUP flag is set to 0, it indicates that this is the first occasion that the Client or Server has attempted to send this PUBLISH packet [default: 0]
  -r, --retain[=RETAIN]                    If the RETAIN flag is set to 1 in a PUBLISH packet sent by a Client to a Server, the Server MUST replace any existing retained message for this topic and store the Application Message [default: 0]
  -u, --username[=USERNAME]                Provide a username to be used for authenticating with the broker
  -p, --pw[=PW]                            Provide a password to be used for authenticating with the broker
  -c, --clean-session[=CLEAN-SESSION]      Setting the 'clean session' flag [default: true]
  -l, --level=LEVEL                        MQTT Protocol level [default: 4]
  -k, --keepalive[=KEEPALIVE]              The number of seconds between sending PING commands to the broker for the purposes of informing it we are still connected and functioning [default: 0]
      --will-topic[=WILL-TOPIC]            The topic on which to send a Will, in the event that the client disconnects unexpectedly
      --will-message[=WILL-MESSAGE]        Specify a message that will be stored by the broker and sent out if this client disconnects unexpectedly
      --will-qos[=WILL-QOS]                The QoS to use for the Will [default: 0]
      --will-retain[=WILL-RETAIN]          If given, if the client disconnects unexpectedly the message sent out will be treated as a retained message [default: 0]
  -S, --ssl[=SSL]                          Enable SSL encryption [default: false]
      --config-path[=CONFIG-PATH]          Setting the Swoole config file path
      --properties-path[=PROPERTIES-PATH]  Setting the Properties config file path
  -h, --help                               Display help for the given command. When no command is given display help for the list command
  -q, --quiet                              Do not output any message
  -V, --version                            Display this application version
      --ansi                               Force ANSI output
      --no-ansi                            Disable ANSI output
  -n, --no-interaction                     Do not ask any interactive question
  -v|vv|vvv, --verbose                     Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  An MQTT version 3.1/3.1.1/5.0 client for publishing simple messages
```

### Subscribe

```bash
$ php vendor/bin/mqtt subscribe --help
Description:
  Subscribing to topics

Usage:
  subscribe [options]

Options:
  -H, --host[=HOST]                        Specify the host to connect to [default: "localhost"]
  -P, --port[=PORT]                        Connect to the port specified [default: 1883]
  -i, --id[=ID]                            The id to use for this client [default: ""]
      --qos=QOS                            Specify the quality of service to use for the message, from 0, 1 and 2 (multiple values allowed)
  -t, --topic=TOPIC                        The MQTT topic to subscribe to (multiple values allowed)
  -u, --username[=USERNAME]                Provide a username to be used for authenticating with the broker
  -p, --pw[=PW]                            Provide a password to be used for authenticating with the broker
  -c, --clean-session[=CLEAN-SESSION]      Setting the 'clean session' flag [default: true]
  -l, --level=LEVEL                        MQTT Protocol level [default: 4]
  -k, --keepalive[=KEEPALIVE]              The number of seconds between sending PING commands to the broker for the purposes of informing it we are still connected and functioning [default: 0]
      --will-topic[=WILL-TOPIC]            The topic on which to send a Will, in the event that the client disconnects unexpectedly
      --will-message[=WILL-MESSAGE]        Specify a message that will be stored by the broker and sent out if this client disconnects unexpectedly
      --will-qos[=WILL-QOS]                The QoS to use for the Will [default: 0]
      --will-retain[=WILL-RETAIN]          If given, if the client disconnects unexpectedly the message sent out will be treated as a retained message [default: 0]
  -S, --ssl[=SSL]                          Enable SSL encryption [default: false]
      --config-path[=CONFIG-PATH]          Setting the Swoole config file path
      --properties-path[=PROPERTIES-PATH]  Setting the Properties config file path
  -U, --unsubscribe[=UNSUBSCRIBE]          Topics that need to be unsubscribed (multiple values allowed)
  -h, --help                               Display help for the given command. When no command is given display help for the list command
  -q, --quiet                              Do not output any message
  -V, --version                            Display this application version
      --ansi                               Force ANSI output
      --no-ansi                            Disable ANSI output
  -n, --no-interaction                     Do not ask any interactive question
  -v|vv|vvv, --verbose                     Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  An MQTT version 3.1/3.1.1/5.0 client for subscribing to topics
```

### Path

There are two config: `--config-path` and `--properties-path`, you need to specify the path to the config file.

- `--config-path`

```php
// config.php

return [
    'open_mqtt_protocol' => true,
    'package_max_length' => 2 * 1024 * 1024,
];
```

- `--properties-path`

```php
// properties.php

return [
    'publish' => [
        'topic_alias' => 1,
        'message_expiry_interval' => 12,
    ],
    'will' => [
        'will_delay_interval' => 60,
        'message_expiry_interval' => 60,
    ],
];
```