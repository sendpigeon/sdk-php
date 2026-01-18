# Changelog

## 0.5.0

- Add Contacts API (`$client->contacts->list()`, `create`, `batch`, `get`, `update`, `delete`, `unsubscribe`, `resubscribe`, `stats`, `tags`)
- Add Broadcasts API (`$client->broadcasts->list()`, `create`, `get`, `update`, `delete`, `send`, `schedule`, `cancel`, `test`, `recipients`, `analytics`)
- Broadcast targeting: `includeTags` and `excludeTags` options

## 0.4.0

- Per-email tracking: `tracking: new TrackingOptions(opens: true, clicks: true)` in send requests
- Added `TrackingOptions` type
- Response `warnings` field for non-fatal issues (e.g., tracking disabled at org level)
- Updated `TrackingDefaults` to use `trackingEnabled` master toggle

## 0.3.1

- Added `SENDPIGEON_DEV` env var support for local development
- Simplified `send()` and `sendBatch()` using `array_filter()`
- Internal refactoring, no breaking changes

## 0.3.0

- Add Suppressions API (`$client->suppressions->list()`, `$client->suppressions->delete()`)

## 0.2.0

- Add `email.opened` and `email.clicked` webhook events
- Add typed `WebhookPayload` and `WebhookPayloadData` classes
- Add `getTypedPayload()` method to `WebhookVerifyResult`
- Add webhook event constants

## 0.1.0

- Initial release
- Send emails (single + batch)
- Templates API
- Domains API
- API Keys API
- Webhook signature verification
