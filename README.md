# Tarray

This is a copy of the ArrayList implementation in Java. 
It can be used as an Iterable (foreach...).

# Available methods

```
public function add(mixed $element) : void;
public function addArray(array $elements) : void;
public function addMultiple(mixed ...$elements) : void;
public function clear() : bool;
public function contains(mixed $element) : bool;
public function count() : int;
public function export(int $fromIndex, int $toIndex) : Tarray;
public function forEach(callable $callable, bool $returnCopy = false) : self|Tarray;
public function get(int $index) : mixed;
public function getAcceptedType() : string;
public function indexOf(mixed $element) : int;
public function isEmpty() : bool;
public function lastIndex() : int;
public function remove(mixed $element) : bool;
public function removeAll(mixed ...$elements) : bool;
public function removeAt(int $index) : bool;
public function set(int $index, mixed $element) : bool;
public function toArray() : array;
```