<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Touch;

use Propel\Runtime\Propel;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\Touch\TouchConfig getConfig()
 */
class TouchDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const PLUGIN_PROPEL_CONNECTION = 'propel connection plugin';

    /**
     * @var string
     */
    public const SERVICE_DATA = 'util data service';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container->set(static::PLUGIN_PROPEL_CONNECTION, function () {
            return Propel::getConnection();
        });

        $container->set(static::SERVICE_DATA, function (Container $container) {
            return $container->getLocator()->utilDataReader()->service();
        });

        return $container;
    }
}
