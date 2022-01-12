<?php

interface Comparable {
    /**
     * @param Comparable $other
     * @return Int -1, 0 or 1 depending of comparison
     *
     */
    public function compareTo(Comparable $other): int;
}