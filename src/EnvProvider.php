<?php
namespace Ivoba\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;

class EnvProvider implements ServiceProviderInterface
{

    const CONFIG_KEY_DEFAULT = 'default';
    const CONFIG_KEY_REQUIRED = 'required';
    const CONFIG_KEY_ALLOWED = 'allowed';

    /*
    'use_dotenv' => function($app){return ($app['env' !== 'prod'])},
    1. run EnvProvider, set all given vars to app
    2. if usedotenv, run dotenv and run envprovider again
    3. @todo apply options, check for required, set default etc
     */

    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app['env.default_options'] = [
            'prefix' => 'SILEX',
            'use_dotenv' => function () {
                return true;
            },
            'dotenv_dir' => __DIR__ . '/../../../..', //vendor/ivoba/dotenv-service-provider/src
            'var_config' => [] //todo apply
        ];

        $app['env.load'] = $app->share(function ($app) {
            if (!isset($app['env.options'])) {
                $app['env.options'] = [];
            }
            $app['env.options'] = array_merge($app['env.default_options'], $app['env.options']);

            $this->addEnvVarsToApp($app);
            if ($app['env.options']['use_dotenv']()) {
                \Dotenv::load($app['env.options']['dotenv_dir']);
                //again pls
                $this->addEnvVarsToApp($app);
            }

            $this->applyConfigs($app, $app['env.options']['var_config']);
        });

    }

    /**
     * applies config options to expected vars
     *
     * @param Application $app
     * @param $config
     */
    protected function applyConfigs(Application $app, $config)
    {
        foreach ($config as $varName => $options) {
            if (isset($options[self::CONFIG_KEY_DEFAULT])) {
                if (isset($app[$varName]) === false) {
                    $app[$varName] = $options[self::CONFIG_KEY_DEFAULT];
                }
            }

            if (isset($options[self::CONFIG_KEY_REQUIRED])) {
                if ($options[self::CONFIG_KEY_REQUIRED]) {
                    \Dotenv::required($varName);
                }
            }

            if (isset($options[self::CONFIG_KEY_ALLOWED])) {
                \Dotenv::required($varName, $options[self::CONFIG_KEY_ALLOWED]);
            }

        }

    }

    /**
     * collect vars and sets them to the DIC
     *
     * @param Application $app
     */
    protected function addEnvVarsToApp(Application $app)
    {
        $hasPrefix       = function ($elem) use ($app) {
            return strpos($elem, $app['env.options']['prefix'] . '_') !== false;
        };
        $arrayFilterKeys = function ($input, $callback) {
            if (!is_array($input)) {
                trigger_error('array_filter_key() expects parameter 1 to be array, ' . gettype($input) . ' given', E_USER_WARNING);
                return null;
            }

            if (empty($input)) {
                return $input;
            }

            $filteredKeys = array_filter(array_keys($input), $callback);
            if (empty($filteredKeys)) {
                return array();
            }

            $input = array_intersect_key(array_flip($filteredKeys), $input);

            return $input;
        };

        $envVars = $arrayFilterKeys($_ENV, $hasPrefix);
        $envVars = array_merge($arrayFilterKeys($_SERVER, $hasPrefix), $envVars);
        foreach ($envVars as $envVar => $empty) {
            $var = \Dotenv::findEnvironmentVariable($envVar);
            if ($var) {
                $key       = strtolower(str_replace($app['env.options']['prefix'] . '_', '', $envVar));
                $app[$key] = $var;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function boot(Application $app)
    {
    }

}
