<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2019 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\SftpAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;
use League\Flysystem\Sftp\SftpAdapter;

class SftpAdapterFactoryTest extends TestCase
{
    /**
     * @var \ReflectionProperty
     */
    protected $property;

    /**
     * @var \ReflectionMethod
     */
    protected $method;

    public function setup()
    {
        $class = new \ReflectionClass(SftpAdapterFactory::class);
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $sm = Bootstrap::getServiceManager();
        $factory = new SftpAdapterFactory();

        $adapter = $factory($sm, 'sftp_default');

        $this->assertInstanceOf(SftpAdapter::class, $adapter);
    }

    /**
     * @dataProvider validateConfigProvider
     */
    public function testValidateConfig(
        $options,
        $expectedOptions = false,
        $expectedException = false,
        $expectedExceptionMessage = false
    ) {
        $factory = new SftpAdapterFactory($options);

        if ($expectedException) {
            $this->expectException($expectedException);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $this->method->invokeArgs($factory, []);

        if (\is_array($expectedOptions)) {
            $this->assertEquals($expectedOptions, $this->property->getValue($factory));
        }
    }

    public function validateConfigProvider(): array
    {
        return [
            [
                [],
                false,
                'UnexpectedValueException',
                "Missing 'host' as option",
            ],
            [
                ['host' => 'foo'],
                false,
                'UnexpectedValueException',
                "Missing 'port' as option",
            ],
            [
                ['host' => 'foo', 'port' => 'foo'],
                false,
                'UnexpectedValueException',
                "Missing 'username' as option",
            ],
            [
                ['host' => 'foo', 'port' => 'foo', 'username' => 'foo'],
                false,
                'UnexpectedValueException',
                "Missing either 'password' or 'privateKey' as option",
            ],
            [
                ['host' => 'foo', 'port' => 'foo', 'username' => 'foo', 'password' => 'foo'],
                ['host' => 'foo', 'port' => 'foo', 'username' => 'foo', 'password' => 'foo'],
            ],
            [
                ['host' => 'foo', 'port' => 'foo', 'username' => 'foo', 'privateKey' => 'foo'],
                ['host' => 'foo', 'port' => 'foo', 'username' => 'foo', 'privateKey' => 'foo'],
            ],
        ];
    }
}
