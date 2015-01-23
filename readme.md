# DotenvServiceProvider

A [dotenv](https://github.com/vlucas/phpdotenv) ServiceProvider for [Silex](http://silex.sensiolabs.org)

## Caution: this is highly overengineered!

This will set all Env vars that have a given prefix, default is *SILEX_*, to $app.  
You can pass a function to detect if you want to run dotenv load as well to load vars from an .env file.

The functionality replaces mainly something like this:

    $app['environment'] = getenv('SILEX_ENV') ? getenv('SILEX_ENV') : 'dev';
    if($app['environment'] === 'dev'){
        \Dotenv::load();
    }
    $app['debug'] = getenv('SILEX_DEBUG') ? getenv('SILEX_DEBUG') : false;
    $app['this'] = getenv('this') ? getenv('this') : 'that';
    //...
    \Dotenv::required();
    
About 10 LOC vs ca. 110 LOC + autoload, yiah  
So its actually a bit overdressed for the party, but anyway ;)  
Some goodies might be legit as getenv, $_ENV & $_SERVER support.

## Usage

    $app->register(new \Ivoba\Silex\EnvProvider(), ['env.options' => ['prefix' => 'MYPREFIX',
        'use_dotenv' => function () use ($app) {
            return $app['env'] === 'dev';
        }]
    ]);
    $app['env.load'];
    
## TODO
- add default values via options
- add required values via options
- add allowed values per var via options





