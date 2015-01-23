<?php
namespace Ivoba\Silex;

use Silex\Application;

class EnvServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $_SERVER['STILEX_FOO'] = 'bar';
        $app = new Application();

        $envOptions = ['env.options' => ['prefix' => 'STILEX',
            'use_dotenv' => function () use ($app) {
                return false;
            }]
        ];
        $app->register(new \Ivoba\Silex\EnvProvider(), $envOptions);
        $app['env.load'];

        $this->assertEquals($envOptions['env.options']['prefix'], $app['env.options']['prefix']);
        $this->assertEquals('bar', $app['foo']);
    }


}