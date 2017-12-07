# ZFLinkHeadersModule

[![Travis](https://img.shields.io/travis/facile-it/zf-link-headers-module.svg)](https://travis-ci.org/facile-it/zf-link-headers-module)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/facile-it/zf-link-headers-module.svg)](https://scrutinizer-ci.com/g/facile-it/zf-link-headers-module/)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/facile-it/zf-link-headers-module.svg)](https://scrutinizer-ci.com/g/facile-it/zf-link-headers-module/)
[![GitHub release](https://img.shields.io/github/release/facile-it/zf-link-headers-module.svg)](https://github.com/facile-it/zf-link-headers-module)


This module will automatically sends `Link` HTTP headers supporting 
[resource hints](https://www.w3.org/TR/resource-hints/)
and [preload](https://www.w3.org/TR/preload/), via HTTP headers.

## Currently supported hints

From [`OptionsInterface`](src/OptionsInterface.php):

```php
const MODE_PRELOAD = 'preload';
const MODE_PREFETCH = 'prefetch';
const MODE_DNS_PREFETCH = 'dns-prefetch';
const MODE_PRECONNECT = 'preconnect';
const MODE_PRERENDER = 'prerender';
```

## Configuration

This is the default configuration and options can be overridden in your configuration.

```php
return [
    'facile' => [
        'zf_link_headers_module' => [
            'stylesheet_enabled' => false, // send link headers for stylesheet links
            'stylesheet_mode' => 'preload', // resource hint for stylesheet links
            'script_enabled' => false, // send link headers for scripts
            'script_mode' => 'preload', // resource hint for script links
            'http2_push_enabled' => true, // if disabled, a "nopush" attributed will be added to disable HTTP/2 push 
        ],
    ],
];
```


## Example

### Configuration:

Default configuration:

```php
return [
    'facile' => [
        'zf_link_headers_module' => [
            'stylesheet_enabled' => false, // send link headers for stylesheet links
            'stylesheet_mode' => 'preload', // resource hint for stylesheet links
            'script_enabled' => false, // send link headers for scripts
            'script_mode' => 'preload', // resource hint for script links
            'http2_push_enabled' => true, // if disabled, a "nopush" attributed will be added to disable HTTP/2 push 
        ],
    ],
];
```

### Template

In your template:

```phtml
<!DOCTYPE html>
<html lang="it">
    <head>
        <?= 
        $this->headLink(['rel' => 'preload', 'as' => 'image',  'href' => $this->basePath() . $this->asset('assets/images/logo.png'), 'media' => 'image/png')
             ->headLink(['rel' => 'preload', 'as' => 'style',  'href' => $this->basePath() . $this->asset('assets/vendor.css')])
             ->headLink(['rel' => 'preload', 'as' => 'script', 'href' => $this->basePath() . $this->asset('assets/vendor.js')])
             // prefetch (low priority) resources required in th next pages
             ->headLink(['rel' => 'prefetch', 'as' => 'style', 'href' => $this->basePath() . $this->asset('assets/next.css')])
             // do not send preload headers
             ->prependStylesheet($this->basePath() . $this->asset('assets/vendor.css'))
             ->prependStylesheet($this->basePath() . $this->asset('assets/vendor.js'))
        ?>
        <?=
        $this->headScript()
            ->prependFile('/script/foo.js')
        ?>
    </head>
    <body>
        <!-- your content here -->
   
        <script type="text/javascript" src="<?= $this->basePath() . $this->asset('assets/vendor.js') ?>"></script>
    </body>
</html>
```

### Response headers

The module will automatically add a Link header to the response:

```
Link: </assets/images/logo.png>; rel="preload"; as="image"; media="image/png",
  </assets/vendor.css>; rel="preload"; as="style",
  </assets/vendor.js>; rel="preload"; as="script",
  </assets/next.css>; rel="prefetch"; as="style"
```

You should notice that resource `/script/foo.js` is not in headers, because it wasn't
included in preload head links.


## Automatically sends stylesheets preload

Enabling `stylesheet_enabled` mode in your configuration, you can avoid inserting preload links 
for all your styles.
 
### Configuration:

```php
return [
    'facile' => [
        'zf_link_headers_module' => [
            'stylesheet_enabled' => true, // send link headers for stylesheet links 
        ],
    ],
];
```

You can optionally change the `stylesheet_mode`
(supported modes are vailable as constants in [`OptionsInterface`](src/OptionsInterface.php)) 
to use on stylesheets.

### Template

In your template:

```phtml
<!DOCTYPE html>
<html lang="it">
    <head>
        <?= 
        $this->prependStylesheet($this->basePath() . $this->asset('assets/vendor.css'))
        ?>
    </head>
    <body>
        <!-- your content here -->
    </body>
</html>
```

### Response headers

The module will automatically add a Link header to the response:

```
Link: </assets/vendor.css>; rel="preload"; as="style"; type="text/css"; media="screen"
```


## Automatically sends scripts preload

Enabling `script_enabled` mode in your configuration, you can avoid inserting preload links 
for all your scripts.
 
### Configuration:

```php
return [
    'facile' => [
        'zf_link_headers_module' => [
            'script_enabled' => true, // send link headers for script links 
        ],
    ],
];
```

You can optionally change the `script_mode`
(supported modes are vailable as constants in [`OptionsInterface`](src/OptionsInterface.php)) 
to use on stylesheets.

### Template

In your template:

```phtml
<!DOCTYPE html>
<html lang="it">
    <head>
        <?=
        $this->headScript()
            ->prependFile('/script/foo.js')
            ->prependFile('/script/bar.js', 'text/foo')
        ?>
    </head>
    <body>
        <!-- your content here -->
    </body>
</html>
```

### Response headers

The module will automatically add a Link header to the response:

```
Link: </script/foo.js>; rel="preload"; as="script"; type="text/javascript",
  </script/bar.js>; rel="preload"; as="script"; type="text/foo"
```


## Disable HTTP/2 push

Using HTTP/2, sending preload link headers the web server will push contents to the browser
when the page is requested.

This isn't always necessary, because browsers can cache the contents and pushing them 
can increase bandwidth usage with no significant performance.

You can disable push setting `http2_push_enabled` configuration option to `false`.
This will add a [`nopush`](https://w3c.github.io/preload/#x3-3-server-push-http-2) attribute
that indicates to an HTTP/2 push capable server that the resource hould not be pushed 
(e.g. the origin may have additional information indicating that it may already be in cache).
