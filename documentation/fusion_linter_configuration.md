# Neos Fusion Linter Configuration

This document provides a guide on configuring the Neos Fusion Linter. The linter helps maintain coding standards by enforcing rules related to Neos and generic coding practices.

## Generic Rules

### Empty Block

*Description:* Complains about empty prototypes.
The class `EmptyBlockRule` ensures that Fusion prototypes do not contain empty blocks.

*Rule Class:* `Vette\Neos\CodeStyle\Rules\Generic\EmptyBlockRule`

*Error Message:* `Empty block found`

*Examples:*

````
prototype(My.Custom.Component) {} // Empty block error
````

````
prototype(My.Custom.Component) {

}  // Empty block with whitespace error
````

### Operator Spacing

*Description:* Expects exactly 1 space before and after operators.
The class `OperatorSpacingRule` ensures consistent spacing around operators in Neos Fusion code.

*Rule Class:* `Vette\Neos\CodeStyle\Rules\Generic\OperatorSpacingRule`

*Error Messages:* `Expecting exactly 1 space before operator` / `Expecting exactly 1 space after operator`

*Example:*
````
prototype(My.Custom.Component) {
        example1 = '...'
        example2 ='...' // Error: Incorrect spacing around the '=' operator
}
````

### Block Spacing

*Description:* Expects exactly 1 space before opening brace.
The `BlockSpacingRule` class ensures consistent spacing before opening braces `{` and after closing braces `}` in Neos Fusion code.

*Rule Class:* `Vette\Neos\CodeStyle\Rules\Generic\BlockSpacingRule`

*Error Message:* `Expecting exactly 1 space before opening brace`

*Examples:*
````
prototype(My.Custom.Component){ // Error: Incorrect spacing around the '{' operator
    ...
}
````
````
prototype(My.Custom.Component)    { // Error: Incorrect spacing around the '{' operator
    ...
}
````

### Eel Spacing

*Description:* Expects no space after EEL start and before EEL end.
The `EelSpacingRule` class ensures consistent spacing around Eel expressions in Neos Fusion code.

*Rule Class:* `Vette\Neos\CodeStyle\Rules\Generic\EelSpacingRule`

*Error Message:* `Expecting no space after EEL start` / `Expecting no space before EEL end`


*Examples:*
````
example = ${  '...'} // Error: Incorrect spacing after EEL start
````
````
example = ${'...'   } // Error: Incorrect spacing before EEL end
````

## Neos Rules

### Prototype Name Prefix

*Description:* Ensures the prototype name starts with one of the specified prefixes.
The `PrototypeNamePrefixRule` class ensures that Neos Fusion prototype names adhere to a specific prefix convention.

*Rule Class:* `Vette\Neos\CodeStyle\Rules\Neos\PrototypeNamePrefixRule`

*Error Message:* `Prototype name should start with: Content, Document, Component, Helper, Presentation, Integration`

*Options:*

`ignorePackages`: List of packages to ignore.

`validPrefixes`: List of valid prefixes.

*Configuration:*

The PrototypeNamePrefixRule can be configured in neos-code-style YAML configuration file. Adjust the severity level, ignored packages, and valid prefixes based on the project requirements.
[source,yaml]
````
rules:
  prototypeNamePrefix:
    options:
      ignorePackages: ['Neos.Neos', 'Neos.Fusion', 'Neos.Seo']
      validPrefixes: ['Content', 'Document', 'Component', 'Helper', 'Presentation', 'Integration']
````
*Examples:*
````
prototype(Paessler.PaesslerCom:Content.Button) { // Correct usage
    ...
}
````
````
prototype(Paessler.PaesslerCom:Example.Button) {
    // Error: Prototype name should start with one of the 'validPrefixes'
}
````

### Only Includes in Root File

*Description:* The `OnlyIncludesInRootRule` class ensures that only the Root.fusion file includes other files exclusively

*Rule Class:* `Vette\Neos\CodeStyle\Rules\Neos\OnlyIncludesInRootRule`

*Error Message:* `The Root.fusion file should only include other files`


*Examples*
````
An example of correct usage with only includes in the root file:

// Root.fusion

include: **/*.fusion
````

### One Prototype Per File

*Description:* The class `OnePrototypePerFileRule` ensures that each Fusion file contains only a single prototype definition.

*Rule Class:* `Vette\Neos\CodeStyle\Rules\Neos\OnePrototypePerFileRule`

*Error Message:* `Expecting only 1 prototype definition per file`

*Example:*
````
// Component.fusion

prototype(My.Package:Component1) {
    // Prototype definition 1
}

prototype(My.Package:Component2) {
    // Error: Only one prototype definition per file is allowed.
    // Prototype definition 2
}
````

### Fluid Template
*Description:* This class `FluidTemplateRule` is designed to identify and report instances where Fluid templates are used instead of AFX syntax in Neos Fusion,

*Rule Class:* `Vette\Neos\CodeStyle\Rules\Neos\FluidTemplateRule`

*Error Message:* `AFX should be used instead of Fluid templates`


### Node Properties

*Description:* The `NodePropertiesRule` ensures that node properties are accessed individually using `q(node).property()`.

*Rule Class:* `Vette\Neos\CodeStyle\Rules\Neos\NodePropertiesRule`

*Error Message:* `Node properties should only be accessed individually via "q(node).property()"`


*Examples:*
````
${node.properties()}  // Error

${documentNode.properties()} // Error

${site.properties()} // Error

${q(node).property('title')} // Correct
````

### Context in First Level

*Description:* The `ContextInFirstLevelRule` class ensures that the `@context` directive is not used in the first level of a prototype in Neos Fusion.

*Rule Class:* `Vette\Neos\CodeStyle\Rules\Neos\ContextInFirstLevelRule`

*Error Message:* `No @context should be used in the first level of a prototype`


*Examples:*
````
// Incorrect example

prototype(My.Package:Example) {
    // Incorrect usage of @context in the first level
    @context {
        someContext = 'someValue'
    }
    // ...
}
````
````
// Correct example

prototype(My.Package:Example) {
    // ...
    // Valid usage of @context within a nested level
    @context {
        someContext = 'someValue'
    }
    // ...
}

````

### Ternary Operator

*Description:* The `TernaryOperatorRule` class ensures that only one ternary operator is used in a single Eel expression in Neos Fusion.

*Rule Class:* `Vette\Neos\CodeStyle\Rules\Neos\TernaryOperatorRule`

*Error Message:* `Only one ternary operator should be used in an Eel expression`


*Examples:*
````
${condition ? 'true' : 'false'} // Correct usage

${condition1 ? 'true' : (condition2 ? 'nestedTrue' : 'nestedFalse')} // Error

````

## Custom rules

You can add additional rules in the configuration, by adding them to the `includes`:

```yaml
includes:
  - DistributionPackages/My.Package/Classes/CodeStyle/MyRule.php
```

The rule class should extend `Vette\Neos\CodeStyle\Rules\Rule` and implement the `process` method:

```php
<?php

declare(strict_types=1);

namespace My\Package\CodeStyle;

use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Rules\Rule;

class MyRule extends Rule
{
    protected array $tokenTypes = [
        Token::OBJECT_IDENTIFIER_TYPE
    ];

    public function process(int $tokenStreamIndex, File $file, int $level): void
    {
        $isPresentationComponent = str_contains($file->getPath(), 'Presentation');

        $namespace = $file->getTokenStream()->getTokenAt($tokenStreamIndex);
        $colon = $file->getTokenStream()->getTokenAt($tokenStreamIndex + 1);
        $identifier = $file->getTokenStream()->getTokenAt($tokenStreamIndex + 2);

        if ($isPresentationComponent
            && $colon instanceof Token
            && $colon->getType() === Token::COLON_TYPE
            && $identifier instanceof Token
            && $identifier->getType() === Token::OBJECT_IDENTIFIER_TYPE
            && $identifier->getValue() === 'ContentComponent'
            && $namespace instanceof Token
            && $namespace->getValue() === 'Neos.Neos') {
            $file->addError(
                'A presentation component must not be Neos.Neos:ContentComponent',
                $namespace->getLine(),
                $namespace->getColumn(),
                $this->severity
            );
        }
    }
}
```
