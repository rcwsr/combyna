<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\ExpressionLanguage;

use hafriedlander\Peg\Parser\Basic;

/**
 * Class PegExpressionParser
 *
 * A generated PEG parser for parsing the Combyna expression language
 *
 * NB: This class will be modified by php-peg and output to PegExpressionParser.php in the dist directory,
 *     by `composer run build:expression-parser`.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PegExpressionParser extends Basic
{
    /*!* PegExpressionParser
    BooleanLiteral: value:/\b(true|false)\b/ >
    NumberLiteral: value:/\d+(\.\d+)?/ >
    StringLiteral: "\'" value:/[^']* / "\'" >
    Variable: variableName:/[\w_]+/ > !/[.(:]/ >
    ElementList: Expression > ("," > Expression >)*
        function Expression(&$result, $sub)
        {
            // Called for each matched element
            $result['elements'][] = $sub['node'];
        }
    List: "[" > elements:(:ElementList?) > "]" >
        function elements(&$result, $sub)
        {
            $result['elements'] = isset($sub['ElementList']) ? $sub['ElementList']['elements'] : [];
        }
    Value:
            (
                NumberLiteral |
                BooleanLiteral |
                List |
                StringLiteral |
                Builtin |
                Function |
                Variable
            ) > |
            '(' > Expression > ')' >
        function NumberLiteral(&$result, $sub) {
            $result['node'] = [
                'type' => 'number',
                'number' => (float) $sub['value']['text']
            ];
        }
        function BooleanLiteral(&$result, $sub) {
            $result['node'] = [
                'type' => 'boolean',
                'boolean' => ($sub['value']['text'] === 'true')
            ];
        }
        function List(&$result, $sub) {
            $result['node'] = [
                'type' => 'list',
                'elements' => $sub['elements']
            ];
        }
        function StringLiteral(&$result, $sub) {
            $result['node'] = [
                'type' => 'text',
                'text' => $sub['value']['text']
            ];
        }
        function Builtin(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Function(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Variable(&$result, $sub) {
            $result['node'] = [
                'type' => 'variable',
                'variable' => $sub['variableName']['text']
            ];
        }
        function Expression(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
    Multiplication: '*' > operand:Value >
    Division: '/' > operand:Value >
    Product: Value > ( Multiplication | Division ) *
        function Value(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Multiplication(&$result, $sub) {
            $result['node'] = $this->buildBinaryArithmeticExpression(
                $result['node'],
                '*',
                $sub['operand']['node']
            );
        }
        function Division(&$result, $sub) {
            $result['node'] = $this->buildBinaryArithmeticExpression(
                $result['node'],
                '/',
                $sub['operand']['node']
            );
        }
    Addition: '+' > operand:Product >
    Subtraction: '-' > operand:Product >
    Sum: Product > ( Addition | Subtraction ) *
        function Product(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Addition(&$result, $sub) {
            $result['node'] = $this->buildBinaryArithmeticExpression(
                $result['node'],
                '+',
                $sub['operand']['node']
            );
        }
        function Subtraction(&$result, $sub) {
            $result['node'] = $this->buildBinaryArithmeticExpression(
                $result['node'],
                '-',
                $sub['operand']['node']
            );
        }
    Append: '~' > operand:Sum >
    Concatenation: Sum > ( Append ) *
        function Sum(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Append(&$result, $sub) {
            if (isset($result['node']['type']) && $result['node']['type'] === 'concatenation') {
                $result['node']['list']['elements'][] = $sub['operand']['node'];
            } else {
                $result['node'] = [
                    'type' => 'concatenation',
                    'list' => [
                        'type' => 'list',
                        'elements' => [
                            $result['node'],
                            $sub['operand']['node']
                        ]
                    ]
                ];
            }
        }
    Equal: '=' > operand:Concatenation >
    Unequal: '<>' > operand:Concatenation >
    CaseInsensitivelyEqual: '~=' > operand:Concatenation >
    CaseInsensitivelyUnequal: '~<>' > operand:Concatenation >
    Comparison: Concatenation > ( Equal | Unequal | CaseInsensitivelyEqual | CaseInsensitivelyUnequal ) *
        function Concatenation(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Equal(&$result, $sub) {
            $result['node'] = $this->buildComparisonExpression(
                $result['node'],
                '=',
                $sub['operand']['node']
            );
        }
        function Unequal(&$result, $sub) {
            $result['node'] = $this->buildComparisonExpression(
                $result['node'],
                '<>',
                $sub['operand']['node']
            );
        }
        function CaseInsensitivelyEqual(&$result, $sub) {
            $result['node'] = $this->buildComparisonExpression(
                $result['node'],
                '~=',
                $sub['operand']['node']
            );
        }
        function CaseInsensitivelyUnequal(&$result, $sub) {
            $result['node'] = $this->buildComparisonExpression(
                $result['node'],
                '~<>',
                $sub['operand']['node']
            );
        }
    NamedArgument: parameterName:/[\w_]+/ > ":" > Expression >
        function parameterName(&$result, $sub)
        {
            $result['parameterName'] = $sub['text'];
        }
        function Expression(&$result, $sub)
        {
            $result['argument'] = $sub['node'];
        }
    PositionalArgument: Expression >
        function Expression(&$result, $sub)
        {
            $result['argument'] = $sub['node'];
        }
    PositionalArgumentList: PositionalArgument > ("," > PositionalArgument >)*
        function PositionalArgument(&$result, $sub)
        {
            // Called for each matched argument
            $result['arguments'][] = $sub['argument'];
        }
    NamedArgumentList: NamedArgument > ("," > NamedArgument >)*
        function NamedArgument(&$result, $sub)
        {
            // Called for each matched argument
            $result['arguments'][$sub['parameterName']] = $sub['argument'];
        }
    Builtin: (builtinName:/[\w_]+/) >
            "(" >
            (
                (PositionalArgumentList > "," > NamedArgumentList) |
                NamedArgumentList |
                PositionalArgumentList
            )? >
            ")" >
        function builtinName(&$result, $sub)
        {
            $result['node'] = [
                'type' => 'builtin',
                'name' => $sub['text'],
                'positional-arguments' => [],
                'named-arguments' => []
            ];
        }
        function PositionalArgumentList(&$result, $sub)
        {
            $result['node']['positional-arguments'] = $sub['arguments'];
        }
        function NamedArgumentList(&$result, $sub)
        {
            $result['node']['named-arguments'] = $sub['arguments'];
        }
    Function: locator:((libraryName:/[\w_]+/) "." (functionName:/[\w_]+/)) > "(" > NamedArgumentList? ")" >
        function locator(&$result, $sub)
        {
            $result['node'] = [
                'type' => 'function',
                'library' => $sub['libraryName']['text'],
                'name' => $sub['functionName']['text'],
                'arguments' => []
            ];
        }
        function NamedArgumentList(&$result, $sub)
        {
            $result['node']['arguments'] = $sub['arguments'];
        }
    Expression: > Comparison
        function Comparison(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
    */

    // Comparison just happens to currently be the expression with the highest precedence,
    // which is why the top-level Expression references it

    /**
     * @param string $left
     * @param string $operator
     * @param string $right
     * @return array
     */
    private function buildBinaryArithmeticExpression($left, $operator, $right)
    {
        return [
            'type' => 'binary-arithmetic',
            'operator' => $operator,
            'left' => $left,
            'right' => $right
        ];
    }

    /**
     * @param string $left
     * @param string $operator
     * @param string $right
     * @return array
     */
    private function buildComparisonExpression($left, $operator, $right)
    {
        return [
            'type' => 'comparison',
            'operator' => $operator,
            'left' => $left,
            'right' => $right
        ];
    }
}
