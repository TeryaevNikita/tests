<?php


namespace App\Services\Set;

use Exception;

class SetStorage implements Set
{
    /**
     * @var array
     */
    private $storage;

    /**
     * @param $element
     * @throws Exception
     */
    public function add($element): void
    {
        $hash = $this->getHash($element);

        $elementsByHash = $this->storage[$hash] ?? null;

        if (null === $elementsByHash) {
            $elementsByHash = [$element];
        } else {
            if (!$this->containsInSameHash($elementsByHash, $element)) {
                if (!is_array($elementsByHash)) {
                    $elementsByHash = [$elementsByHash];
                }
                $elementsByHash[] = $element;
            }
        }

        $this->storage[$hash] = $elementsByHash;
    }

    /**
     * @param $element
     * @throws Exception
     */
    public function remove($element): void
    {
        $hash = $this->getHash($element);

        $elementsByHash = $this->storage[$hash] ?? null;

        if (null === $elementsByHash) {
            return;
        }

        $findEqual = false;

        if (is_array($elementsByHash)) {
            foreach ($elementsByHash as $index => $item) {
                $findEqual = $this->isEqual($item, $element);
                if ($findEqual) {
                    unset($elementsByHash[$index]);
                    if (count($elementsByHash) == 1) {
                        $elementsByHash = reset($elementsByHash);
                    }
                    break;
                }
            }
        } else {
            if ($this->isEqual($elementsByHash, $element)) {
                $findEqual = true;
                $elementsByHash = null;
            } else {
                return;
            }
        }

        if (!$findEqual) {
            return;
        }

        $this->storage[$hash] = $elementsByHash;
    }

    /**
     * @param $element
     * @return bool
     * @throws Exception
     */
    public function contains($element): bool
    {
        $hash = $this->getHash($element);

        $elementsByHash = $this->storage[$hash] ?? null;

        if (null === $elementsByHash) {
            return false;
        }

        return $this->containsInSameHash($elementsByHash, $element);
    }

    /**
     * @param array $elementList
     * @param $element
     * @return bool
     */
    private function containsInSameHash(array $elementList, $element): bool
    {
        $findEqual = false;

        foreach ($elementList as $item) {
            $findEqual = $this->isEqual($item, $element);
            if ($findEqual) {
                break;
            }
        }


        return $findEqual;
    }

    /**
     * @param $element1
     * @param $element2
     * @return bool
     */
    private function isEqual($element1, $element2): bool
    {
        if (is_object($element1) && is_object($element2)) {
            if ($element1 instanceof Equals) {
                return $element1->equals($element2);
            }
        }
        return $element1 === $element2;
    }

    /**
     * @param $element
     * @return string
     * @throws Exception
     */
    private function getHash($element): string
    {
        if (is_string($element) || is_numeric($element)) {
            return hash('md5', $element);
        }

        if (is_array($element)) {
            return md5(json_encode($element));
        }

        if (is_object($element)) {
            if ($element instanceof Hashable) {
                $hash = $element->Hash();
            } else {
                $hash = spl_object_hash($element);
            }
        }

        $hash = $hash ?? null;
        if ($hash === null) {
            throw new Exception("unsupported type");
        }

        return $hash;
    }

}
