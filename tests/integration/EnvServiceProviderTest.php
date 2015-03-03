<?php
namespace Ivoba\Silex;

use Silex\Application;

class EnvServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $_SERVER['STILEX_FOO'] = 'bar';
        $app                   = new Application();

        $envOptions = ['env.options' => ['prefix' => 'STILEX',
            'use_dotenv' => function () use ($app) {
                return false;
            },
            'var_config' => ['hoo' => [EnvProvider::CONFIG_KEY_DEFAULT => 'haa']]]
        ];
        $app->register(new \Ivoba\Silex\EnvProvider(), $envOptions);
        $app['env.load'];

        $this->assertEquals($envOptions['env.options']['prefix'], $app['env.options']['prefix']);
        $this->assertEquals('bar', $app['foo']);
        $this->assertEquals('haa', $app['hoo']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequired()
    {
        $app        = new Application();
        $envOptions = ['env.options' => ['var_config' => ['hoo' => [EnvProvider::CONFIG_KEY_REQUIRED => true]]]];
        $app->register(new \Ivoba\Silex\EnvProvider(), $envOptions);
        $app['env.load'];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAllowed()
    {
        $_SERVER['SILEX_HOO'] = 'that';
        $app                  = new Application();
        $envOptions           = ['env.options' => ['var_config' => ['hoo' => [EnvProvider::CONFIG_KEY_ALLOWED => 'this']]]];
        $app->register(new \Ivoba\Silex\EnvProvider(), $envOptions);
        $app['env.load'];
    }

    public function testTypeCast()
    {
        $_ENV['SILEX_FLOAT'] = '23.234234';
        $_ENV['SILEX_BOOL']  = 'true';
        $_ENV['SILEX_INT']    = '23';
        $app                  = new Application();
        $envOptions           = ['env.options' =>
            ['use_dotenv' => function () use ($app) {
                return false;
            },
                'var_config' => ['int' => [EnvProvider::CONFIG_KEY_CAST => EnvProvider::CAST_TYPE_INT],
                    'bool' => [EnvProvider::CONFIG_KEY_CAST => EnvProvider::CAST_TYPE_BOOLEAN],
                    'float' => [EnvProvider::CONFIG_KEY_CAST => EnvProvider::CAST_TYPE_FLOAT]]]];
        $app->register(new \Ivoba\Silex\EnvProvider(), $envOptions);
        $app['env.load'];

        $this->assertEquals(23, $app['int']);
        $this->assertEquals(true, $app['bool']);
        $this->assertEquals(23.234234, $app['float']);
        $this->assertInternalType('int', $app['int']);
        $this->assertInternalType('boolean', $app['bool']);
        $this->assertInternalType('float', $app['float']);
    }


}