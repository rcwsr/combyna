<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\State\StateInterface;
use Combyna\Component\Ui\State\Store\NullViewStoreStateInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\View\ViewStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetGroupStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\View\PageViewInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetGroupInterface;

/**
 * Interface UiStateFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiStateFactoryInterface
{
    /**
     * Creates a DefinedWidgetState
     *
     * @param DefinedWidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @return WidgetStateInterface
     */
    public function createDefinedWidgetState(
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag
    );

    /**
     * Creates a NullViewStoreState
     *
     * @param string $storeViewName
     * @return NullViewStoreStateInterface
     */
    public function createNullViewStoreState($storeViewName);

    /**
     * Creates a PageViewState
     *
     * @param PageViewInterface $view
     * @param ViewStoreStateInterface $storeState
     * @param WidgetStateInterface $renderedRootWidget
     * @param StaticBagInterface $viewAttributeStaticBag
     * @return ViewStateInterface
     */
    public function createPageViewState(
        PageViewInterface $view,
        ViewStoreStateInterface $storeState,
        WidgetStateInterface $renderedRootWidget,
        StaticBagInterface $viewAttributeStaticBag
    );

    /**
     * Creates a ViewStoreState
     *
     * @param string $storeViewName
     * @param StaticBagInterface $slotStaticBag
     * @return ViewStoreStateInterface
     */
    public function createViewStoreState(
        $storeViewName,
        StaticBagInterface $slotStaticBag
    );

    /**
     * Creates a WidgetGroupState
     *
     * @param WidgetGroupInterface $widgetGroup
     * @return WidgetGroupStateInterface
     */
    public function createWidgetGroupState(
        WidgetGroupInterface $widgetGroup
    );

    /**
     * Creates a WidgetStatePath
     *
     * @param UiStateInterface[] $states
     * @return WidgetStatePathInterface
     */
    public function createWidgetStatePath(array $states);
}
