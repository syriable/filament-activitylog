---
name: test-writer
description: Guidelines for writing Laravel tests with Pest PHP. Activate whenever writing, editing, or reviewing any Pest test.
---

## Pest testing guidelines

### Variable naming

- Variables are named exactly after the class they represent: `$financeDocumentTemplate = FinanceDocumentTemplate::factory()->create()`
- Variables of the same type but with a specific distinction/type can have the type suffixed:

```php
$financeDocumentTemplatePaymentRequest = FinanceDocumentTemplate::factory()
    ->set('type', FinanceDocumentTemplateType::PaymentRequest)
    ->create();

$financeDocumentTemplateInvoice = FinanceDocumentTemplate::factory()
    ->set('type', FinanceDocumentTemplateType::Invoice)
    ->create();
```

- Variables of the same type without significant distinction can have an alphabetically incrementing suffix (NOT a numeric suffix):

Good:
```php
[$bookingA, $bookingB, $bookingC, $bookingD] = Booking::factory(4)
    ->sequence(...)
    ->create()
```
Bad:
```php
[$booking1, $booking2, $booking3, $booking4] = Booking::factory(4)
    ->sequence(...)
    ->create()
```

- In some cases it is allowed for variables to have a suffix if that says more about the object than an alphabetical suffix:

```php
[$currencyEur, $currencyUsd, $currencyChf, $currencyGbp] = Currency::get();
```

### Expectations

- In Pest tests, the result type does not need to be checked using a `->toBeInstanceOf()`.
- Put each expectation on a new line:

Good:
```php
expect($booking)
    ->status->toBe(BookingStatus::Quoted)
    ->legs->toHaveCount(8);
```
Bad:
```php
expect($booking)->status->toBe(BookingStatus::Quoted);
expect($booking)->legs->toHaveCount(8);
```

- NEVER use `and()` to start a new expectation object. Rather, enter a whitespace and start a new `expect()` call:

```php
expect($booking)
    ->status->toBe(BookingStatus::Quoted);

expect($financeDocument)
    ->booking->is($booking)->toBeTrue();
```

- Always format `expect()` calls across multiple lines, with `expect(...)` on the first line and each `->property->assertion()` indented on its own line:

Good:
```php
expect($impressionStage)
    ->value->toBe(1);

expect($booking)
    ->status->toBe(BookingStatus::Quoted)
    ->legs->toHaveCount(8);
```
Bad:
```php
expect($impressionStage->value)->toBe(1);

expect($booking)->status->toBe(BookingStatus::Quoted);
```

- Keep the value passed to `expect()` to a minimum. Always try to keep it as top-level as possible:

Good:
```php
expect($financeDocument)
    ->shippingProvider->toBeNull();

expect($user)
    ->name->toBe('John Doe');
```
Bad:
```php
expect($financeDocument->shippingProvider)
    ->toBeNull();

expect($user->name)->toBe('John Doe');
```

- This also applies to method calls — chain them on the expectation instead of nesting inside `expect()`:

Good:
```php
expect($handler)
    ->shouldReport(new ModelNotFoundException)->toBeTrue();
```
Bad:
```php
expect($handler->shouldReport(new ModelNotFoundException))
    ->toBeTrue();
```

- Exception: when the object uses `__get` (e.g. Spatie `Data` objects) or has an Eloquent accessor that computes a value, Pest's higher-order proxy is bypassed and `->property->toBe()` calls the real property/accessor instead of the expectation. In that case, access the property inside `expect()` directly or use `getRawOriginal()` for Eloquent models:

```php
// `FunnelStage` extends `Data` — `->value` would access the real property, not Pest's proxy.
// So we pass `$impressionStage->value` into `expect()` instead.
expect($impressionStage->value)
    ->toBe(1);
```

```php
// `pipedrive_deal_title` has an Eloquent accessor that computes a fallback value.
// The higher-order proxy would trigger the accessor, so use `getRawOriginal()` to check the raw column.
expect($booking)
    ->getRawOriginal('pipedrive_deal_title')->toBeNull();
```

### Assertions

- Use `->sole()` rather than `->first()` in all situations where there should theoretically only be one record at all times.
- When comparing whether an Eloquent model matches another one, always use the Eloquent `->is()` method:

Good:
```php
$featureValueA = $productA->featureValues()->sole();
$featureValueB = $productB->featureValues()->sole();

expect($featureValueA)
    ->is($featureValueB)->toBeTrue();
```
Bad:
```php
$featureValueA = $productA->featureValues()->first();
$featureValueB = $productB->featureValues()->first();

expect($featureValueA)->not->toBeNull();
expect($featureValueB)->not->toBeNull();
expect($featureValueA->id)->toBe($featureValueB->id);
```

- When asserting a Model relationship, do not assert on the `->related_id->toBe(...)`, but rather assert on the `->related->is($related)->toBeTrue()`.
- When asserting the size of a collection or object, prefer `toHaveCount()`:

Good:
```php
expect($products)
    ->toHaveCount(10);
```
Bad:
```php
expect($products->count())->toBe(10);
```

- When asserting date or datetime/Carbon values, convert them to a date string or date time string:

Good:
```php
expect($passport)
    ->valid_until->toDateString()->toBe(now()->endOfYear()->toDateString());
```
Bad:
```php
expect($passport)
    ->valid_until->eq(now()->endOfYear())->toBeTrue();
```

- When asserting JSON responses, if possible, always assert them via `assertExactJson()` if the full response is available.

### Factories

- When a factory has 2 or more chained methods (except from the final `->create()`), put each chained method call on a new line:

Good:
```php
$quotation = Quotation::factory()
    ->for($booking)
    ->confirmed()
    ->create();
```
Bad:
```php
$quotation = Quotation::factory()->for($booking)->confirmed()->create();
```

- Always put a whitespace between factories:

Good:
```php
$user = User::factory()->create();

$booking = Booking::factory()
    ->for($user)
    ->create();

$quotation = Quotation::factory()
    ->for($booking)
    ->confirmed()
    ->create();
```

- When creating multiple factories under each other, but only one or a few of them are used later on, still assign the output of all factories to an alphabetically suffixed variable:

Good:
```php
$quotationA = Quotation::factory()
    ->for($booking)
    ->confirmed()
    ->create();

$quotationB = Quotation::factory()
    ->confirmed()
    ->create();

// Only `$quotationA` used...
```
Bad:
```php
$quotation = Quotation::factory()
    ->for($booking)
    ->confirmed()
    ->create();

Quotation::factory()
    ->confirmed()
    ->create();
```

### General rules

- Prefer `$model->getKey()` over `$model->id`.
- Never assign action-classes to variables, but always resolve them using the `app()` function and directly execute.
- When using Pest/PHPUnit providers, do NOT give each case a name.
- Use all of Pest's provided functions rather than calling them on `$this`:

Good:
```php
use function Pest\Laravel\travelTo;

travelTo('2025-08-01 00:00:00');
```
Bad:
```php
$this->travelTo('2025-08-01 00:00:00');
```

### Comments in tests

- In test comments, when referring to a variable use the variable name surrounded with backticks:

Good:
```php
// Should have created 2 finance documents (one for `$bookingA` + `$bookingB`, one for `$bookingC`)
```

- Do not add useless comments that merely describe the next line of code.
