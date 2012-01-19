# WordPress Plugin Class
## Usage
Dynamic classes (i.e. objects instantiated with `new`) should use
`Rotor_WPPlugin`. Static classes (i.e. those with static methods) should use
`Rotor_WPPlugin_Static`

See `example-dynamic.php` and `example-static.php` for examples.

## Prefixed Methods
These classes also include support for automatically hooking methods prefixed
with `action_` or `filter_`. Simply call `parent::__construct(true)` for
dynamic classes or `self::register_hooks()` for a static class.

Please note: to use custom priorities, you must use the PHPDoc tags instead.