<?php

namespace EightPoints\Bundle\GuzzleBundle;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\EightPointsGuzzleExtension;
use EightPoints\Bundle\GuzzleBundle\DependencyInjection\Compiler\EventHandlerCompilerPass;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @version   1.0
 * @since     2013-10
 */
class EightPointsGuzzleBundle extends Bundle
{
    /** @var \EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin[] */
    protected $plugins = [];

    /**
     * @param \EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin[] $plugins
     */
    public function __construct(array $plugins = [])
    {
        foreach ($plugins as $plugin) {
            $this->registerPlugin($plugin);
        }
    }

    /**
     * Build EightPointsGuzzleBundle
     *
     * @version 1.0
     * @since   2013-10
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        foreach ($this->plugins as $plugin) {
            $plugin->build($container);
        }

        $container->addCompilerPass(new EventHandlerCompilerPass());
    }

    /**
     * Overwrite getContainerExtension
     *  - no naming convention of alias needed
     *  - extension class can be moved easily now
     *
     * @version 1.1
     * @since   2013-12
     *
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface The container extension
     */
    public function getContainerExtension() : ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new EightPointsGuzzleExtension($this->plugins);
        }

        return $this->extension;
    }

    /**
     * @inheritdoc
     */
    public function boot()
    {
        foreach ($this->plugins as $plugin) {
            $plugin->boot();
        }
    }

    /**
     * @param \EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin $plugin
     *
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     *
     * @return void
     */
    protected function registerPlugin(EightPointsGuzzleBundlePlugin $plugin)
    {
        // Check plugins name duplication
        foreach ($this->plugins as $registeredPlugin) {
            if ($registeredPlugin->getPluginName() === $plugin->getPluginName()) {
                throw new InvalidConfigurationException(sprintf(
                    'Trying to connect two plugins with same name: %s',
                    $plugin->getPluginName()
                ));
            }
        }

        $this->plugins[] = $plugin;
    }
}
