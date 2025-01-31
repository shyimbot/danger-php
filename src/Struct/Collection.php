<?php
declare(strict_types=1);

namespace Danger\Struct;

abstract class Collection implements \IteratorAggregate, \Countable
{
    protected array $elements = [];

    public function __construct(iterable $elements = [])
    {
        foreach ($elements as $key => $element) {
            $this->set($key, $element);
        }
    }

    public function add($element): void
    {
        $this->validateType($element);

        $this->elements[] = $element;
    }

    public function set($key, $element): void
    {
        $this->validateType($element);

        $this->elements[$key] = $element;
    }

    /**
     * @param mixed|null $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->elements[$key];
        }

        return null;
    }

    public function clear(): void
    {
        $this->elements = [];
    }

    public function count(): int
    {
        return \count($this->elements);
    }

    public function getKeys(): array
    {
        return array_keys($this->elements);
    }

    public function has($key): bool
    {
        return \array_key_exists($key, $this->elements);
    }

    public function map(\Closure $closure): array
    {
        return array_map($closure, $this->elements);
    }

    public function reduce(\Closure $closure, $initial = null)
    {
        return array_reduce($this->elements, $closure, $initial);
    }

    public function fmap(\Closure $closure): array
    {
        return array_filter($this->map($closure));
    }

    public function sort(\Closure $closure): void
    {
        uasort($this->elements, $closure);
    }

    public function filter(\Closure $closure): static
    {
        return $this->createNew(array_filter($this->elements, $closure));
    }

    public function slice(int $offset, ?int $length = null): static
    {
        return $this->createNew(\array_slice($this->elements, $offset, $length, true));
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function first()
    {
        return array_values($this->elements)[0] ?? null;
    }

    public function last()
    {
        return array_values($this->elements)[\count($this->elements) - 1] ?? null;
    }

    /**
     * @param int|string $key
     */
    public function remove($key): void
    {
        unset($this->elements[$key]);
    }

    public function getIterator(): \Generator
    {
        yield from $this->elements;
    }

    abstract protected function getExpectedClass(): string;

    /**
     * @return static
     */
    protected function createNew(iterable $elements = [])
    {
        return new static($elements);
    }

    private function validateType($element): void
    {
        $expectedClass = $this->getExpectedClass();

        if (!$element instanceof $expectedClass) {
            $elementClass = \get_class($element);

            throw new \InvalidArgumentException(
                sprintf('Expected collection element of type %s got %s', $expectedClass, $elementClass)
            );
        }
    }
}
