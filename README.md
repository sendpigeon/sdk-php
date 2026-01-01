# SendPigeon PHP SDK

Official PHP SDK for [SendPigeon](https://sendpigeon.dev) - Transactional Email API.

## Requirements

- PHP 8.1+
- Guzzle 7.0+

## Installation

```bash
composer require sendpigeon/sendpigeon
```

## Quick Start

```php
use SendPigeon\SendPigeon;

$client = new SendPigeon('sk_live_xxx');

$response = $client->send(
    to: 'user@example.com',
    subject: 'Hello',
    html: '<p>Welcome to SendPigeon!</p>'
);

echo "Email sent: " . $response->id;
```

## Configuration

```php
$client = new SendPigeon(
    apiKey: 'sk_live_xxx',
    baseUrl: 'https://api.sendpigeon.dev',  // Custom base URL
    timeout: 30,                             // Request timeout in seconds
    maxRetries: 2,                           // Retry attempts (max 5)
);
```

## Local Development

Use the SendPigeon CLI to catch emails locally:

```bash
# Terminal 1: Start local server
npx @sendpigeon-sdk/cli dev

# Terminal 2: Run your app with dev mode
SENDPIGEON_DEV=true php app.php
```

When `SENDPIGEON_DEV=true`, the SDK routes requests to `localhost:4100` instead of production.

## Sending Emails

### Basic Email

```php
$response = $client->send(
    to: 'user@example.com',
    from: 'hello@yourdomain.com',
    subject: 'Welcome!',
    html: '<h1>Welcome</h1><p>Thanks for signing up!</p>',
    text: 'Welcome! Thanks for signing up.',
);
```

### Multiple Recipients

```php
$response = $client->send(
    to: ['user1@example.com', 'user2@example.com'],
    subject: 'Team Update',
    html: '<p>Important update for the team.</p>',
    cc: 'manager@example.com',
    bcc: ['archive@example.com'],
);
```

### With Template

```php
$response = $client->send(
    to: 'user@example.com',
    templateId: 'tmpl_xxx',
    variables: [
        'name' => 'John',
        'company' => 'Acme Inc',
    ],
);
```

### With Attachments

```php
$response = $client->send(
    to: 'user@example.com',
    subject: 'Your invoice',
    html: '<p>Please find your invoice attached.</p>',
    attachments: [
        [
            'filename' => 'invoice.pdf',
            'content' => base64_encode(file_get_contents('invoice.pdf')),
        ],
    ],
);
```

### Scheduled Email

```php
$response = $client->send(
    to: 'user@example.com',
    subject: 'Reminder',
    html: '<p>Don\'t forget about tomorrow\'s meeting!</p>',
    scheduledAt: '2024-01-15T10:00:00Z',
);
```

### Batch Send (up to 100)

```php
$response = $client->sendBatch([
    ['to' => 'user1@example.com', 'subject' => 'Hello', 'html' => '<p>Hi User 1!</p>'],
    ['to' => 'user2@example.com', 'subject' => 'Hello', 'html' => '<p>Hi User 2!</p>'],
]);

foreach ($response->data as $result) {
    if ($result->status === 'sent') {
        echo "Email {$result->index} sent: {$result->id}\n";
    }
}
```

### Tracking

Enable open/click tracking per email (opt-in):

```php
use SendPigeon\Types\TrackingOptions;

$response = $client->send(
    to: 'user@example.com',
    subject: 'Welcome!',
    html: '<p>Check out our <a href="https://example.com">site</a>!</p>',
    tracking: new TrackingOptions(opens: true, clicks: true),
);

// Response may include warnings if tracking is disabled at org level
if ($response->warnings) {
    print_r($response->warnings);
}
```

Configure organization defaults in Settings → Tracking.

## Email Management

```php
// Get email by ID
$email = $client->emails->get('email_xxx');

// Cancel scheduled email
$cancelled = $client->emails->cancel('email_xxx');
```

## Templates

```php
use SendPigeon\Types\Template;

// Create template
$template = $client->templates->create(
    name: 'welcome',
    subject: 'Welcome, {{name}}!',
    html: '<h1>Hello {{name}}</h1><p>Welcome to {{company}}!</p>',
);

// Get template
$template = $client->templates->get('tmpl_xxx');

// List templates
$result = $client->templates->list();

// Update template
$template = $client->templates->update(
    id: 'tmpl_xxx',
    subject: 'Updated subject',
);

// Delete template
$client->templates->delete('tmpl_xxx');
```

## Domains

```php
// Add domain
$domain = $client->domains->create('mail.yourdomain.com');

// DNS records are returned for setup
foreach ($domain->dnsRecords as $record) {
    echo "{$record->type} {$record->name} -> {$record->value}\n";
}

// Verify domain
$result = $client->domains->verify('dom_xxx');
if ($result->verified) {
    echo "Domain verified!\n";
}

// List domains
$result = $client->domains->list();

// Delete domain
$client->domains->delete('dom_xxx');
```

## API Keys

```php
use SendPigeon\Types\ApiKeyMode;
use SendPigeon\Types\ApiKeyPermission;

// Create API key
$key = $client->apiKeys->create(
    name: 'Production',
    mode: ApiKeyMode::Live,
    permission: ApiKeyPermission::FullAccess,
);

// Save $key->key - only returned once!
echo "API Key: " . $key->key;

// List API keys
$result = $client->apiKeys->list();

// Delete API key
$client->apiKeys->delete('key_xxx');
```

## Webhook Verification

```php
use SendPigeon\Webhooks;

// In your webhook handler
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '';
$timestamp = $_SERVER['HTTP_X_WEBHOOK_TIMESTAMP'] ?? '';

$result = Webhooks::verify(
    payload: $payload,
    signature: $signature,
    timestamp: $timestamp,
    secret: 'whsec_xxx',  // Your webhook secret
    maxAge: 300,          // Max age in seconds
);

if (!$result->valid) {
    http_response_code(401);
    echo $result->error;
    exit;
}

// Handle webhook event
$eventType = $result->payload['type'];
switch ($eventType) {
    case 'email.delivered':
        // Handle delivery
        break;
    case 'email.bounced':
        // Handle bounce
        break;
}

http_response_code(200);
```

### Inbound Email Webhooks

```php
$result = Webhooks::verifyInbound(
    payload: $payload,
    signature: $signature,
    timestamp: $timestamp,
    secret: 'whsec_inbound_xxx',
);
```

## Error Handling

All methods throw `SendPigeonException` on error:

```php
use SendPigeon\Exceptions\SendPigeonException;

try {
    $response = $client->send(
        to: 'user@example.com',
        subject: 'Hello',
    );
} catch (SendPigeonException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->errorCode . "\n";      // 'api_error', 'network_error', 'timeout_error'
    echo "API Code: " . $e->apiCode . "\n";    // API-specific code like 'validation_error'
    echo "Status: " . $e->status . "\n";       // HTTP status code
}
```

## Idempotency

Prevent duplicate sends with idempotency keys:

```php
$response = $client->send(
    to: 'user@example.com',
    subject: 'Order confirmation',
    html: '<p>Your order has been confirmed.</p>',
    idempotencyKey: 'order-12345-confirmation',
);
```

## License

MIT
