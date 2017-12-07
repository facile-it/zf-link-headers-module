# ZFLinkHeadersModule

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
            'script_mode' => 'preload', // send link headers for scripts
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
            'script_mode' => 'preload', // send link headers for scripts
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

The module will automatically add headers to the response with these links (or similar):

```
Link: </assets/images/logo.png>; rel="preload"; as="image"; media="image/png"
Link: </assets/vendor.css>; rel="preload"; as="style"
Link: </assets/vendor.js>; rel="preload"; as="script"
Link: </assets/next.css>; rel="prefetch"; as="style"
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

The module will automatically add headers to the response with these links:

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

The module will automatically add headers to the response with these links:

```
Link: </script/foo.js>; rel="preload"; as="script"; type="text/javascript"
Link: </script/bar.js>; rel="preload"; as="script"; type="text/foo"
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
