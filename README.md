# EventHub PHP SDK
This is a set of PHP classes design to give you easy access to the new Northwestern EventHub & AMQ.

As of writing, this PHP SDK implements methods for all EventHub API calls.

## Installation
This package is available via composer:

```sh
$ composer require northwestern-sysdev/event-hub-php-sdk
```

You will need to contact ET-I&A in order to set up your access to EventHub.

## Examples
Here are some quick examples for using this SDK. For more information about EventHub & its capabilities, please see the documentation on the [Service Registry](https://apiserviceregistry.northwestern.edu/AMQ-Dashboard).

Note that all methods from this SDK can throw `Northwestern\SysDev\SOA\EventHub\Exception\EventHubDown` and `Northwestern\SysDev\SOA\EventHub\Exception\EventHubError` messages. The former indicates a network problem or outage; the latter is something wrong with your usage of EventHub.

### Consumers
You can loop something like the below example code & schedule it in cron to poll the queue for messages.

```php
$message_api = new \Northwestern\SysDev\SOA\EventHub\Message('https://northwestern-dev.apigee.net', 'my api key', new GuzzleHttp\Client);
$topic_name = 'etsysdev.test.queue.name';

try {
    $message = $message_api->readOldest($topic_name); // returns a DeliveredMessage object

    // The ID is useful for moving messages & troubleshooting. The raw message will be a plain text representation, ideal for logging!
    log_stuff_in_my_database($message->getId(), $message->getRawMessage());

    // If you use JSON messages, this will be a PHP associative array. For XML, you'll need to getRawMessage() and parse it yourself.
    $body = $message->getMessage();
    update_my_database($body['some_unique_id_from_the_message'], $body['some_other_info']['a_field']);

    // Should be the last thing you do in your try block
    $message_api->acknowledgeOldest($topic_name);
} catch (\Exception $e) {
    // If we get an error before the acknowledgeOldest call, the message won't be ack'd & removed from the queue.
    // This gives you an opportunity to fix your stuff & try again!
}
```

Just be aware that EventHub supports webhook delivery; it can do HTTP POSTs to your application when it receives messages in real-time. You should evaluate that option before implementing queue polling.

### Publishers
```php
$topic_api = new \Northwestern\SysDev\SOA\EventHub\Topic('https://northwestern-dev.apigee.net', 'my api key', new GuzzleHttp\Client);
$topic_name = 'etsysdev.test.queue.name';

// If you are sending JSON messages, you can build your messages as PHP associative arrays and send those.
$my_message = [
    'id' => 1,
    'important_enterprise_data' => 'Bananas float in water because they are less dense in comparison.',
    'crucial_security_info' => 'Bananas grow on plants that are officially considered an herb.',
];
$message_id = $eh->writeJsonMessage($topic_name, $my_message);

// For XML, you are responsible for building the string & sending the appropriate content type.
$my_message = '<?xml version="1.0" encoding="UTF-8"?><banana><fact>The banana is actually classified as a berry.</fact></banana>'; // but you're using an XML builder -- do whatever to cast to string
$message_id = $eh->writeMessage($topic_name, $my_message, 'application/xml');
```

### Managing Webhooks
EventHub can be configured to deliver messages destined for your application via HTTP POSTs to an API endpoint you've created via webhooks. This is a self-service feature you can configure yourself.

For full details on how this works & the config options, see the [EventHub Webhook documentation](https://apiserviceregistry.northwestern.edu/AMQ/Webhooks).

```php
$webhook_api = new \Northwestern\SysDev\SOA\EventHub\Webhook('https://northwestern-dev.apigee.net', 'my api key', new GuzzleHttp\Client);
$topic_name = 'etsysdev.test.queue.name';

// Create a paused webhook
$details = $webhook_api->create($topic_name, [
    'topicName' => $topic_name,
    'endpoint' => 'https://my-app-dev.northwestern.edu/api/webhook/receive', // the URL in your application
    'contentType' => 'application/json', // desired format for delivered messages
    'active' => false, // start off paused, so no deliveries are made to your app
    'securityTypes' => ['NONE'], // what authentication method(s) need to be done to authenticate w/ your endpoint -- see the webhook documentation for more info
    'webhookSecurity' => [
        ['securityType' => 'NONE']
    ]
]);

// When you're ready, turn the webhook on:
$details = $webhook_api->unpause($topic_name);

// You can adjust any of your settings whenever you need to -- see the EventHub docs for more info
$details = $webhook_api->updateConfig($topic_name, [
    'endpoint' => 'https://my-app-dev.northwestern.edu/api/v2/webhooks',
]);

// Or remove the webhook entirely & go back to polling the queue
$webhook_api->delete($topic_name);
```

## FAQs
### Why do I have to pass in a GuzzleHttp Client?
I've split this package off from a [Laravel-specific one](https://github.com/NIT-Administrative-Systems/SysDev-laravel-soa), and having Guzzle in the constructor makes it easy for me to let Laravel's service container inject the dependency.

Guzzle supports some [cool middleware stuff](http://docs.guzzlephp.org/en/stable/handlers-and-middleware.html), which you may want to set up before giving it to the EventHub SDK.

In fact, this package comes with a re-try middleware for temporary network errors. You can do this instead to get a Guzzle Client that will make three immediate re-try attempts if it's unable to establish a connection to EventHub:

```php
$client = \Northwestern\SysDev\SOA\EventHub\Guzzle\RetryClient::make();
$api = new \Northwestern\SysDev\SOA\EventHub\Webhook('https://northwestern-dev.apigee.net', 'my api key', $client);
```

This won't auto-retry anything that gets an HTTP error code, e.g. `401 Unauthorized` won't trigger a re-try attempt. Feel free to extend the class & adjust `createRetryHandler()` to better suit your needs.

### Do you have more documentation?
Not really. There are PHP docblocks in the code, but they just refer you back to the main EventHub API documentation. This SDK is just a little adapter layer to make using EventHub feel more PHP-y.

### I need help!
The I&A team is the primary contact for EventHub -- I'm an end-user too!

But, if you have questions specifically abou the PHP SDK, you can ask in `#et-sysdev` on the NIT Slack or email nick.evans@northwestern.edu.

## Contributing
Submit a pull request!

If you need to include a local copy of the package for development purposes, adjust your consuming apps' `composer.json` thusly:

```js
{
    // Add this section
    "repositories": [
        {
            "type": "path",
            "url": "/home/vagrant/code/SysDev-EventHub-PHP-SDK"
        }
    ],

    "require": {
        // Any branch that isn't named in a version format can be specified by prefixing
        // it with 'dev-', so this would install the 'my-feature' branch from a local copy of the package.
        "northwestern-sysdev/event-hub-php-sdk": "dev-my-feature"
    },
}
```

You can test the package by running `phpunit && ./vendor/bin/phpstan analyse --level 5 src/`.
