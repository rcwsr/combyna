<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class CurrentCompoundWidgetDefinitionHasChildStaticQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CurrentCompoundWidgetDefinitionHasChildStaticQuery implements BooleanQueryInterface
{
    /**
     * @var string
     */
    private $childName;

    /**
     * @param string $childName
     */
    public function __construct($childName)
    {
        $this->childName = $childName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResult()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Whether the compound widget definition defines a child called "' . $this->childName . '"';
    }

    /**
     * Fetches the name of the child to query the existence of
     *
     * @return string
     */
    public function getChildName()
    {
        return $this->childName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
