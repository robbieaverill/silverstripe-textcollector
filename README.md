# Text Collector

This text collector uses an [abstract syntax tree parser](https://github.com/nikic/PHP-Parser) to parse the PHP
files in your SilverStripe modules and collect translatable text source strings from them.

## Requirements

* PHP ^7.1
* SilverStripe ^4.0

## Installation

```
composer require --dev silverstripe/textcollector
```

## Example

Assuming you write your English source strings in i18n translation tags, the text collector can detect and parse these.
Take the following example:

```php
public function updateCMSFields(FieldList $fields)
{
    $fields->addFieldToTab('Root.Main', TextField::create('MyField', _t(__CLASS__ . '.MY_FIELD', 'My field'));
}
```

The text collector will identify the `_t(__CLASS__ . '.MY_FIELD', 'My field')` part of this code, resolve the magic
constant and return the source translation as an array:

```
[
    'MyVendor\\MyPackage\\MyPageExtension.MY_FIELD' => 'My field'
]
```

### More examples

For the sake of these examples, assume the current class is called `MyClass` and exists in the root namespace. Comments
will also be collected, and pluralisations will be split up.

| Input | Output |
| --- | --- |
| `_t(__CLASS__ . '.KEY', 'Value')` | `['MyClass.KEY' => 'Value']` |
| `_t(self::class . '.KEY', 'Value')` | `['MyClass.KEY' => 'Value']` |
| `_t(static::class . '.KEY', 'Value')` | `['MyClass.KEY' => 'Value']` |
| `_t(SiteTree::class . '.KEY', 'Value')` | `['SilverStripe\\CMS\\Model\\SiteTree.KEY' => 'Value']` |
| `_t('MyClass.KEY', 'Value')` | `['MyClass.KEY' => 'Value']` |
| `_t('MyVendor\\MyPackage\\MyClass.KEY', 'Value')` | `['MyVendor\\MyPackage\\MyClass.KEY' => 'Value']` |
| `_t('MyClass.KEY', 'Value with {placeholder}', ['placeholder' => 'corn'])` | `['MyClass.KEY' => 'Value with {placeholder']`|
| `_t('MyClass.KEY', 'Tree', 'In the context of a plant')` | `['MyClass.KEY' => ['default' => 'Tree', 'comment' => 'In the context of a plant']]` |

Since markdown tables don't allow me to put pipes into the table values, here's the pluralisation example on its own:

```php
_t('MyClass.KEY', 'Tree|{count} trees', 'In the context of plants');

// ['MyClass.KEY' => [
//     'one' => 'Tree',
//     'other' => '{count} trees',
//     'comment' => 'In the context of plants'
// ]
```

For more information on i18n capabilities in SilverStripe, please see [the i18n documentation](https://docs.silverstripe.org/en/4/developer_guides/i18n/).

## Using it

For now, apply [this horrible patch](https://gist.github.com/robbieaverill/b2fcb86b53ed6d9450a1fd0b33adad60) to framework
and run `vendor/bin/sake dev/tasks/i18nTextCollectorTask` as usual.

## Uncollectable nodes

The following AST node types are uncollectable, since the AST is compiled at parse time so runtime information is not
available:

* Variables in either keys or values
* `static::class` (although we convert it to `self::class` for now)
* `parent::class`
