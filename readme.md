# Some observations in using mago

We are looking into using [mago](https://mago.carthage.software/) to replace our phplint/phpstan/ecs combination.

So far we are really impressed, with the lint/analyze/format options (guard is a step to far for us at the moment).
But we did find some odd things, and this repo tries to collect them and isolate the issues from our actual codebase.

First of all: the pieces of code that I've isolated from one of our legacy codebases is pretty weird code.
But that is where analyzers are brought in to help :)

## impossible-condition fixes to `if (falsefalse)`

Mago's analyze determines a part is always false and offers an unsafe fix. Which is indeed unsafe, as it just breaks the code.
But is also wrong in determining that part is always false.

```bash
$ vendor/bin/mago analyze 
warning[mixed-assignment]: Assigning `mixed` type to a variable may lead to unexpected behavior.
   ┌─ src/ImpossibleCondition.php:14:36
   │
14 │         foreach ($array as $key => $value) {
   │                                    ^^^^^^ Assigning `mixed` type here.
   │
   = Using `mixed` can lead to runtime errors if the variable is used in a way that assumes a specific type.
   = Help: Consider using a more specific type to avoid potential issues.

help[redundant-logical-operation]: Redundant `&&` operation: left operand is always falsy and right operand is not evaluated.
   ┌─ src/ImpossibleCondition.php:15:17
   │
15 │             if (is_numeric($key) && is_array($value) && count($value) === 1) {
   │                 ^^^^^^^^^^^^^^^^    ---------------- Right operand is not evaluated
   │                 │                    
   │                 Left operand is always falsy
   │
   = The `&&` operator will always return `false` in this case.
   = Help: Consider simplifying or removing this logical expression as it always evaluates to `false`.

help[redundant-logical-operation]: Redundant `&&` operation: left operand is always falsy and right operand is not evaluated.
   ┌─ src/ImpossibleCondition.php:15:17
   │
15 │             if (is_numeric($key) && is_array($value) && count($value) === 1) {
   │                 ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^    ------------------- Right operand is not evaluated
   │                 │                                        
   │                 Left operand is always falsy
   │
   = The `&&` operator will always return `false` in this case.
   = Help: Consider simplifying or removing this logical expression as it always evaluates to `false`.

warning[impossible-condition]: This condition (type `false`) will always evaluate to false.
   ┌─ src/ImpossibleCondition.php:15:17
   │
15 │             if (is_numeric($key) && is_array($value) && count($value) === 1) {
   │                 ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Expression of type `false` is always falsy
   │
   = Because this condition is always false, the code block it controls will never be executed.
   = Help: Check the logic of this expression. If the code block is intended to be unreachable, consider removing it. Otherwise, revise the condition.
```

There are of course more issues which are valid points. The above however are not. If you would feed this function with a plain array, `$key` will be numeric so the conclusion that `is_numeric($key)` is always false is not correct.
See test file.

Because it determines this is false, it starts to rewrite stuff and comes up with this fix:

```bash
$ vendor/bin/mago analyze --fix --unsafe --dry-run
--- original
+++ modified
@@ -12,7 +12,7 @@
             return $array;
         }
         foreach ($array as $key => $value) {
-            if (is_numeric($key) && is_array($value) && count($value) == 1) {
+            if (falsefalse) {                                                                                                                                                                                                                                                                                        
                 $newKey = array_keys($value)[0];                                                                                                                                                                                                                                                                     
                 $newValue = $value[$newKey];
                 unset($array[$key]);
```

That obviously won't work.


## redundant-type-comparison fixes to the description instead of code

```bash
$ vendor/bin/mago analyze 
help[redundant-type-comparison]: Redundant condition: variable `$model` (type `array{'name'?: string}`) is already known to be `array<array-key, mixed>`.
   ┌─ src/RedundantLogicalOperation.php:14:17
   │
14 │             if (!is_array($model) || !array_key_exists('name', $model)) {
   │                 ^^^^^^^^^^^^^^^^^ This condition always evaluates to true
   │
   = The type of variable `$model` (type `array{'name'?: string}`) already satisfies the condition that it is `array<array-key, mixed>`. This check is redundant.
   = Help: This condition is always true and the associated code block will always execute if reached. Consider simplifying.

help[redundant-logical-operation]: Redundant `||` operation: left operand is always false and right operand is evaluated.
   ┌─ src/RedundantLogicalOperation.php:14:17
   │
14 │             if (!is_array($model) || !array_key_exists('name', $model)) {
   │                 ^^^^^^^^^^^^^^^^^    --------------------------------- Right operand is evaluated
   │                 │                     
   │                 Left operand is always false
   │
   = The `||` operator will always return the boolean value of the right-hand side in this case.
   = Help: Consider simplifying or removing this logical expression as it always evaluates to the boolean value of the right-hand side.

help[redundant-logical-operation]: Redundant `||` operation: left operand is evaluated and right operand is always falsy.
   ┌─ src/RedundantLogicalOperation.php:31:17
   │
31 │             if (!array_key_exists('name', $model) || !is_array($model)) {
   │                 ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^    ----------------- Right operand is always falsy
   │                 │                                     
   │                 Left operand is evaluated
   │
   = The `||` operator will always return the boolean value of the left-hand side in this case.
   = Help: Consider simplifying or removing this logical expression as it always evaluates to the boolean value of the left-hand side.

help[redundant-type-comparison]: Redundant condition: variable `$model` (type `array{'name': string}`) is already known to be `array<array-key, mixed>`.
   ┌─ src/RedundantLogicalOperation.php:31:54
   │
31 │             if (!array_key_exists('name', $model) || !is_array($model)) {
   │                                                      ^^^^^^^^^^^^^^^^^ This condition always evaluates to true
   │
   = The type of variable `$model` (type `array{'name': string}`) already satisfies the condition that it is `array<array-key, mixed>`. This check is redundant.
   = Help: This condition is always true and the associated code block will always execute if reached. Consider simplifying.
```

This is of course correct, because the phpdoc says it is so. The check was still in from before, and it might be called this way.
```bash
$ vendor/bin/mago analyze --fix --unsafe --dry-run
--- original
+++ modified
@@ -11,7 +11,7 @@
     {
         $collection = [];
         foreach ($knownModels as $model) {
-            if (!is_array($model) || !array_key_exists('name', $model)) {
+            if (the boolean value of the right-hand side) {                                                                                                                                                                                                                                                          
                 continue;
             }

@@ -28,7 +28,7 @@
     {
         $collection = [];
         foreach ($knownModels as $model) {
-            if (!array_key_exists('name', $model) || !is_array($model)) {
+            if (the boolean value of the left-hand side) {                                                                                                                                                                                                                                                           
                 continue;
             }
```

## `format` removes needed parentheses in using a const as classname

The essence:
```php
    protected const string COMPONENT_CLASS = NeedsParenthesesObject::class;

    if ($item instanceof (static::COMPONENT_CLASS)) {
        $this->collection[] = $item;
    } elseif (is_array($item)) {
        $this->collection[] = new (static::COMPONENT_CLASS)($item);
    }
```

the mago formatter removes the parentheses in both these cases:
```bash
$ vendor/bin/mago format --dry-run
diff of 'src/NeedsParentheses.php':
--- original
+++ modified
@@ -28,10 +28,10 @@
         }

         foreach ($items as $item) {
-            if ($item instanceof (static::COMPONENT_CLASS)) {
+            if ($item instanceof static::COMPONENT_CLASS) {                                                                                                                                                                                                                                                          
                 $this->collection[] = $item;                                                                                                                                                                                                                                                                         
             } elseif (is_array($item)) {
-                $this->collection[] = new (static::COMPONENT_CLASS)($item);
+                $this->collection[] = new static::COMPONENT_CLASS($item);                                                                                                                                                                                                                                            
             }                                                                                                                                                                                                                                                                                                        
         }
     }
```

Except that that is not valid PHP...