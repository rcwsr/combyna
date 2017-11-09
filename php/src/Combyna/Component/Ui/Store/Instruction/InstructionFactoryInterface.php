<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Instruction;

/**
 * Interface InstructionFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface InstructionFactoryInterface
{
    /**
     * Creates a new ViewStoreInstructionList
     *
     * @param ViewStoreInstructionInterface[] $instructions
     * @return ViewStoreInstructionListInterface
     */
    public function createViewStoreInstructionList(array $instructions);
}