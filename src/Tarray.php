<?php

namespace Jugid\Tarray;

use InvalidArgumentException;
use Jugid\Tarray\Exception\IndexOutOfBoundsException;
use Jugid\Tarray\Exception\NotRightTypeException;
use Iterator;

/**
 * A Typed array inspired by ArrayList from Java
 */
final class Tarray implements Iterator {

    private array $content;
    private string $type;
    private int $currentIndex;

    public function __construct(string $type) {
        if($type == 'float') {
            $type = 'double';
        }

        $this->type = $type;
        $this->content = [];
        $this->currentIndex = 0;
    }

    /**
     * Add an element to Tarray
     * 
     * @param mixed $element
     * @return void
     */
    public function add(mixed $element) : void {
        if(!$this->hasRightType($element)) {
            throw new NotRightTypeException('The element should be of type '.$this->type.' but ' . gettype($element) . ' found');
        }

        $this->content[] = $element;
    }

    /**
     * Add an array of elements to Tarray
     * 
     * @param array $elements
     * @return void
     */
    public function addArray(array $elements) : void {
        foreach($elements as $element) {
            $this->add($element);
        }
    }

    /**
     * Add multiple elements to Tarray
     * 
     * @param mixed ...$element
     * @return void
     */
    public function addMultiple(mixed ...$elements) : void {
        foreach($elements as $element) {
            $this->add($element);
        }
    }

    /**
     * Remove all of the elements from the Tarray
     * 
     * @param mixed $element
     * @return void
     */
    public function clear() : bool {
        $this->content = [];
        return true;
    }

    /**
     * Return true if the Tarray contains the element, false otherwise
     * 
     * @param mixed $element
     * @return void
     */
    public function contains(mixed $element) : bool {
        return array_search($element, $this->content) !== false;
    }

    /**
     * Return the Tarray size
     * 
     * @return int
     */
    public function count() : int {
        return count($this->content);
    }

    /**
     * Return a new Tarray which contains the elements from the current Tarray by the two index specified
     * 
     * @param int $fromIndex
     * @param int $toIndex
     * @return Tarray
     */
    public function export(int $fromIndex, int $toIndex) : Tarray {
        if($fromIndex >= $toIndex) {
            throw new InvalidArgumentException('The $fromIndex should be less than $toIndex');
        }

        if($fromIndex >= $this->lastIndex()) {
            throw new InvalidArgumentException('The $fromIndex should be less than the last index which is '.$this->lastIndex());
        }

        if($toIndex > $this->lastIndex()) {
            throw new InvalidArgumentException('The $toIndex should be less than the last index which is '.$this->lastIndex());
        }

        $length = $toIndex - $fromIndex + 1;

        $newTarray = new Tarray($this->getAcceptedType());
        $newTarray->addArray(array_slice($this->content, $fromIndex, $length, false));

        return $newTarray;
    }

    /**
     * Apply the callback to the content. Specify the $returnCopy parameter to get a new Tarray
     * 
     * @param callable $callable
     * @param bool $returnCopy
     * @return self
     */
    public function forEach(callable $callable, bool $returnCopy = false) : self|Tarray {
        if($returnCopy) {
            $workingArray = array_map($callable, $this->content);
            $tarrayCopy = new Tarray($this->type);
            $tarrayCopy->addArray($workingArray);
            return $tarrayCopy;
        }
        
        $this->content = array_map($callable, $this->content);

        return $this;
    }

    /**
     * Return the element at the specified index
     * 
     * @param int $index
     * @return mixed
     */
    public function get(int $index) : mixed {
        if(!isset($this->content[$index])) {
            throw new IndexOutOfBoundsException('You are trying to get an element which index is out of bounds');
        }

        return $this->content[$index];
    }

    /**
     * Return the type accepted by the Tarray as a string
     * 
     * @return string
     */
    public function getAcceptedType() : string {
        return $this->type;
    }

    /**
     * Return the index of the first occurence of the specified element, -1 if it does not contains the element
     * 
     * @param mixed $element
     * @return int
     */
    public function indexOf(mixed $element) : int {
        $index = array_search($element, $this->content);

        return $index !== false ? $index : -1;
    }

    /**
     * Return true if the Tarray does not contain elements
     * 
     * @return bool
     */
    public function isEmpty() : bool {
        return count($this->content) == 0;
    }

    /**
     * Return the last index for the Tarray
     * 
     * @return int
     */
    public function lastIndex() : int {
        $size = $this->count();
        return $size > 0 ? $size - 1 : 0;
    }

    /**
     * Remove the specified element if the Tarray contains it. Return true if the element is removed
     * 
     * @param mixed $element
     * @return bool
     */
    public function remove(mixed $element) : bool {
        $index = array_search($element, $this->content);

        if($index !== false) {
            return $this->removeAt($index);
        }

        return false;
    }

    /**
     * Remove all the specified elements using remove(). Return true if at least one element is removed
     * 
     * @param mixed ...$element
     * @return bool
     */
    public function removeAll(mixed ...$elements) : bool {
        $somethingRemoved = false;
        foreach($elements as $element) {
            if($this->remove($element)) {
                $somethingRemoved = true;
            }
        }

        return $somethingRemoved;
    }

    /**
     * Return true if the element at index has been removed. Return true if the element is removed
     * 
     * @param int $index
     * @return bool
     */
    public function removeAt(int $index) : bool {
        if(!isset($this->content[$index])) {
            throw new IndexOutOfBoundsException('You are trying to remove an element which index is out of bounds');
        }

        array_splice($this->content, $index, 1);

        return true;
    }

    /**
     * Change the element at the specified index. Return true if the element is changed
     * 
     * @param int $index
     * @param mixed $element
     * @return void
     */
    public function set(int $index, mixed $element) : bool {
        if(!isset($this->content[$index])) {
            throw new IndexOutOfBoundsException('You are trying to set at an index out of bounds');
        }

        if(!$this->hasRightType($element)) {
            throw new NotRightTypeException('The element should be of type '.$this->type.' but ' . gettype($element) . ' found');
        }

        $this->content[$index] = $element;

        return true;
    }

    /**
     * Return the Tarray as an array
     * 
     * @return array
     */
    public function toArray() : array {
        return $this->content;
    }

    /**
     * Return true if element has the type for the Tarray
     * 
     * @param mixed $element
     * @return bool
     */
    private function hasRightType(mixed $element) : bool {
        if(gettype($element) == 'object') {
            return $element instanceof $this->type;
        }
        
        return gettype($element) == $this->type;
    }

    public function rewind()
    {
        $this->currentIndex = 0;
    }

    public function current()
    {
        return $this->content[$this->currentIndex];
    }

    public function key()
    {
        return $this->currentIndex;
    }

    public function next()
    {
        ++$this->currentIndex;
    }

    public function valid()
    {
        return isset($this->content[$this->currentIndex]);
    }
}