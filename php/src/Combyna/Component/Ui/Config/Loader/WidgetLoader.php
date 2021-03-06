<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader;

use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Component\Trigger\Config\Loader\TriggerLoaderInterface;
use Combyna\Component\Ui\Config\Act\WidgetGroupNode;
use Combyna\Component\Ui\Config\Act\WidgetNode;
use InvalidArgumentException;

/**
 * Class WidgetLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetLoader implements WidgetLoaderInterface
{
    const GROUP_NAME = 'group';

    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var ExpressionBagLoaderInterface
     */
    private $expressionBagLoader;

    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @var TriggerLoaderInterface
     */
    private $triggerLoader;

    /**
     * @var WidgetCollectionLoaderInterface
     */
    private $widgetCollectionLoader;

    /**
     * @param ConfigParser $configParser
     * @param ExpressionLoaderInterface $expressionLoader
     * @param ExpressionBagLoaderInterface $expressionBagLoader
     * @param WidgetCollectionLoaderInterface $widgetCollectionLoader
     * @param TriggerLoaderInterface $triggerLoader
     */
    public function __construct(
        ConfigParser $configParser,
        ExpressionLoaderInterface $expressionLoader,
        ExpressionBagLoaderInterface $expressionBagLoader,
        WidgetCollectionLoaderInterface $widgetCollectionLoader,
        TriggerLoaderInterface $triggerLoader
    ) {
        $this->configParser = $configParser;
        $this->expressionBagLoader = $expressionBagLoader;
        $this->expressionLoader = $expressionLoader;
        $this->triggerLoader = $triggerLoader;
        $this->widgetCollectionLoader = $widgetCollectionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadWidget(array $widgetConfig, EnvironmentNode $environmentNode)
    {
        $type = $widgetConfig['type'];
        $attributeExpressionBag = $this->expressionBagLoader->load(
            isset($widgetConfig['attributes']) ?
                $widgetConfig['attributes'] :
                []
        );
        $childWidgets = isset($widgetConfig['children']) ?
            $this->widgetCollectionLoader->loadWidgets(
                $widgetConfig['children'],
                $this,
                $environmentNode
            ) :
            [];
        $visibilityExpressionNode = isset($widgetConfig['visible']) ?
            $this->expressionLoader->load($widgetConfig['visible']) :
            null;
        $tagNames = $this->configParser->getOptionalElement(
            $widgetConfig,
            'tags',
            'widget tags',
            [],
            'array'
        );
        $triggerNodes = $this->triggerLoader->loadCollection(
            $this->configParser->getOptionalElement(
                $widgetConfig,
                'triggers',
                'widget triggers',
                [],
                'array'
            )
        );

        if ($type === self::GROUP_NAME) {
            return new WidgetGroupNode(
                $childWidgets,
                $visibilityExpressionNode,
                $this->buildTagMap($tagNames)
            );
        }

        $parts = explode('.', $type, 2);

        if (count($parts) < 2) {
            throw new InvalidArgumentException(
                'Widget definition type must be in format <library>.<name>, "' . $type . '" given'
            );
        }

        list($libraryName, $widgetDefinitionName) = $parts;

        if ($libraryName === LibraryInterface::CORE) {
            // Core widget definitions eg. `group` or `text` must be used unprefixed,
            // eg. `group` rather than `core.group`
            throw new InvalidArgumentException(
                'Core widgets may not be used directly: tried to use "' . $type . '". ' .
                'Did you mean to use "' . $widgetDefinitionName . '"?'
            );
        }

        $widgetDefinitionNode = $environmentNode->getWidgetDefinition($libraryName, $widgetDefinitionName);

        return new WidgetNode(
            $widgetDefinitionNode,
            $attributeExpressionBag,
            $childWidgets,
            $triggerNodes,
            $visibilityExpressionNode,
            $this->buildTagMap($tagNames)
        );
    }

    /**
     * Builds up an associative array to speed up lookups
     *
     * @param string[] $tagNames
     * @return array
     */
    private function buildTagMap(array $tagNames)
    {
        $tags = [];

        foreach ($tagNames as $tagName) {
            $tags[$tagName] = true;
        }

        return $tags;
    }
}
