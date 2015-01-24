# DotenvServiceProvider

A [dotenv](https://github.com/vlucas/phpdotenv) ServiceProvider for [Silex](http://silex.sensiolabs.org)

#### Caution: this is highly overengineered!

This will set all Env vars that have a given prefix, default is *SILEX_*, to $app as parameters.  
You can pass a function to detect if you want to run dotenv load as well to load vars from an .env file.

The functionality replaces mainly something like this:

    $app['env'] = getenv('SILEX_ENV') ? getenv('SILEX_ENV') : 'dev';
    if($app['env'] === 'dev'){
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
Register the Service:
 
    $app->register(new \Ivoba\Silex\EnvProvider(), ['env.options' => ['prefix' => 'MYPREFIX',
        'use_dotenv' => function () use ($app) {
            return $app['env'] === 'dev';
        },
        'dotenv_dir' => __DIR__ . '/../../../..',
        'var_config' => []]
    ]);
    $app['env.load'];
    
Yo can add *default*, *required* and *allowed* config options for each var.  

    $envOptions = ['env.options' => ['var_config' => ['hoo' => [EnvProvider::CONFIG_KEY_ALLOWED => 'this'],
                                                      'zack' => [EnvProvider::CONFIG_KEY_REQUIRED => true],
                                                      'zip' => [EnvProvider::CONFIG_KEY_DEFAULT => 'zippi']]]];
    $app->register(new \Ivoba\Silex\EnvProvider(), $envOptions);
    $app['env.load'];





