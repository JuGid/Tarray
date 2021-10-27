<?php

namespace Jugid\Tarray;

use InvalidArgumentException;
use Jugid\Tarray\Exception\IndexOutOfBoundsException;
use Jugid\Tarray\Exception\NotRightTypeException;
use PHPUnit\Framework\TestCase;

class TarrayTest extends TestCase {

    public function testShouldCreateATarray() {
        $tarray = new Tarray('string');

        $this->assertSame('string', $tarray->getAcceptedType());
    }

    public function testShouldGetTypeOfTarray() {
        $tarray_string = new Tarray('string');
        $tarray_int = new Tarray('integer');
        $tarray_float = new Tarray('float');
        $tarray_double = new Tarray('double');
        $tarray_boolean = new Tarray('boolean');
        $tarray_array = new Tarray('array');
        $tarray_object = new Tarray(Tarray::class);

        $this->assertSame('string', $tarray_string->getAcceptedType());
        $this->assertSame('integer', $tarray_int->getAcceptedType());
        //As float = double in PHP, Tarray convert float to double since gettype returns double
        $this->assertSame('double', $tarray_float->getAcceptedType());
        $this->assertSame('double', $tarray_double->getAcceptedType());
        $this->assertSame('boolean', $tarray_boolean->getAcceptedType());
        $this->assertSame('array', $tarray_array->getAcceptedType());
        $this->assertSame(Tarray::class, $tarray_object->getAcceptedType());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testShouldAddElement() {
        $tarray = new Tarray('string');
        $tarray->add('hello');

        //verifying if it does not send an exception
    }

    public function testShouldAddElementForWrongType() {
        $this->expectException(NotRightTypeException::class);

        $tarray = new Tarray('string');
        $tarray->add(1);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testShouldAddForEachTypeOfArray() {
        $tarray_string = new Tarray('string');
        $tarray_int = new Tarray('integer');
        $tarray_float = new Tarray('float');
        $tarray_double = new Tarray('double');
        $tarray_boolean = new Tarray('boolean');
        $tarray_array = new Tarray('array');
        $tarray_object = new Tarray(Tarray::class);

        $tarray_string->add('hello');
        $tarray_int->add(1);
        $tarray_float->add(1.5);
        $tarray_double->add(4.5);
        $tarray_boolean->add(true);
        $tarray_array->add(['hello', 1, 1.5]);
        $tarray_object->add(new Tarray('string'));
    }

    public function testShouldCountTarray() {
        $tarray = new Tarray('string');
        $tarray->add('hello');
        $tarray->add('world');

        $this->assertSame(2, $tarray->count());
    }

    public function testShouldAddMultipleElements() {
        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world', '!');

        $this->assertSame(3, $tarray->count());
    }

    public function testShouldGetElementAtIndex() {
        //indexes start at 0

        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world');

        $this->assertSame('world', $tarray->get(1));
    }

    public function testShouldThrowOutOfBoundException() {
        $this->expectException(IndexOutOfBoundsException::class);

        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world');

        $tarray->get(2);
    }

    public function testShouldNotMakeDifferenceBetweenDoubleAndFloat() {
        $tarray_float = new Tarray('float');
        $tarray_double = new Tarray('double');

        $tarray_float->add(7.56);
        $tarray_double->add(7.56);

        $this->assertSame(gettype(7.56), gettype($tarray_float->get(0)));
        $this->assertSame(gettype(7.56), gettype($tarray_float->get(0)));
    }

    public function testShouldRemoveAnElementAtIndex() {
        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world');

        $this->assertSame(2, $tarray->count());

        $this->assertTrue($tarray->removeAt(0));

        $this->assertSame(1, $tarray->count());
        $this->assertSame('world', $tarray->get(0));
    }

    public function testShouldTryToRemoveAnElementAtIndexOutOfBounds() {
        $this->expectException(IndexOutOfBoundsException::class);

        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world');
        $tarray->removeAt(3);
    }

    public function testShouldRemoveAnElementByValue() {
        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world');

        $this->assertSame(2, $tarray->count());

        $this->assertTrue($tarray->remove('hello'));
        $this->assertFalse($tarray->remove('foo'));
        $this->assertSame(1, $tarray->count());
        $this->assertSame('world', $tarray->get(0));
    }

    public function testShouldRemoveAllSpecified() {
        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world', '!', 'This is', 'a', 'test');

        $this->assertSame(6, $tarray->count());

        $this->assertTrue($tarray->removeAll('world', '!', 'This is'));
        $this->assertFalse($tarray->removeAll('foo', 'bar'));
        $this->assertTrue($tarray->removeAll('test', 'bar'));

        $this->assertSame(2, $tarray->count());
        $this->assertSame('hello', $tarray->get(0));
        $this->assertSame('a', $tarray->get(1));
    }

    public function testShouldClearTarray() {
        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world', '!', 'This is', 'a', 'test');

        $this->assertSame(6, $tarray->count());

        $tarray->clear();

        $this->assertSame(0, $tarray->count());
    }

    public function testShouldWatchIfTarrayIsEmpty() {
        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world', '!', 'This is', 'a', 'test');

        $this->assertSame(false, $tarray->isEmpty());

        $tarray->clear();

        $this->assertSame(true, $tarray->isEmpty());
    }


    public function testShouldTestIfTarrayContainsElementOfRightType() {
        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world');

        $this->assertSame(true, $tarray->contains('hello'));
        $this->assertSame(true, $tarray->contains('world'));
        $this->assertSame(false, $tarray->contains('!'));
    }

    public function testShouldTestIfTarrayContainsElementOfWrongType() {
        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world');

        $this->assertSame(false, $tarray->contains(2));
        $this->assertSame(false, $tarray->contains(2.56));
        $this->assertSame(false, $tarray->contains(new Tarray('string')));
    }

    public function testShouldSetElementAtIndex() {
        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world', '!');

        $this->assertTrue($tarray->set(1, 'people'));

        $this->assertSame('!', $tarray->get(2));
        $this->assertSame('people', $tarray->get(1));
        $this->assertSame('hello', $tarray->get(0));
    }

    public function testShouldSetElementWithWrongType() {
        $this->expectException(NotRightTypeException::class);

        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world', '!');

        $tarray->set(1, 2);
    }

    public function testShouldSetElementWithIndexOutOfBounds() {
        $this->expectException(IndexOutOfBoundsException::class);
        
        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world', '!');

        $tarray->set(5, 'people');
    }

    public function testShouldTransformToArray() {
        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world', '!');

        $this->assertSame(['hello', 'world', '!'], $tarray->toArray());
        $tarray->clear();
        $this->assertSame([], $tarray->toArray());
    }

    public function testShouldGetLastIndex() {
        $tarray = new Tarray('string');
        $tarray->addMultiple('hello', 'world', '!');

        $this->assertSame(2, $tarray->lastIndex());
        $tarray->clear();
        $this->assertSame(0, $tarray->lastIndex());
    }

    public function testShouldAddFromArray() {
        $tarray = new Tarray('string');
        $tarray->addArray(['hello', 'world', '!']);

        $this->assertSame(3, $tarray->count());
        $this->assertSame('hello', $tarray->get(0));
        $this->assertSame('world', $tarray->get(1));
        $this->assertSame('!', $tarray->get(2));
    }

    public function testShouldExportAsNewTarray() {
        $tarray = new Tarray('string');
        $tarray->addArray(['hello', 'world', '!']);
        
        $newTarray = $tarray->export(1,2);

        $this->assertInstanceOf(Tarray::class, $newTarray);
        $this->assertSame('world', $newTarray->get(0));
        $this->assertSame('!', $newTarray->get(1));
    }

    public function testShouldExportToFromIndexMoreThanLessIndex() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The $fromIndex should be less than $toIndex');

        $tarray = new Tarray('string');
        $tarray->addArray(['hello', 'world', '!']);
        
        $newTarray = $tarray->export(2,1);
    }

    public function testShouldExportToWrongFromIndex() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The $fromIndex should be less than the last index which is 2');

        $tarray = new Tarray('string');
        $tarray->addArray(['hello', 'world', '!']);
        
        $newTarray = $tarray->export(3,5);
    }

    public function testShouldExportToWrongLastIndex() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The $toIndex should be less than the last index which is 2');

        $tarray = new Tarray('string');
        $tarray->addArray(['hello', 'world', '!']);
        
        $newTarray = $tarray->export(1,5);
    }

    public function testShouldGetLastIndexOfElement() {
        $tarray = new Tarray('string');
        $tarray->addArray(['hello', 'world', '!']);

        $this->assertSame(1, $tarray->indexOf('world'));
        $this->assertSame(2, $tarray->indexOf('!'));
        $this->assertSame(-1, $tarray->indexOf('foo'));
    }

    public function testShouldIterateThroughtTarray() {
        $shouldBe = ['hello', 'world', '!'];
        $tarray = new Tarray('string');
        $tarray->addArray($shouldBe);
        
        foreach($tarray as $index => $element) {
            $this->assertSame($shouldBe[$index], $element);
        }
    }

    public function testShouldApplyCallable() {
        $tarray = new Tarray('integer');
        $tarray->addMultiple(1,2,3);

        $callable = function($item) {
            return $item * 2;
        };

        $newTarray = $tarray->forEach($callable, true);
        $this->assertSame([2,4,6], $newTarray->toArray());
        $this->assertSame([1,2,3], $tarray->toArray());

        $tarray->forEach($callable);
        $this->assertSame([2,4,6], $tarray->toArray());
    }

}