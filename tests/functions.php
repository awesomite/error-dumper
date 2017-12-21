<?php

/**
 * @internal
 *
 * @param array $array
 *
 * @return array|bool
 */
function awesomite_each(array &$array)
{
    if (false !== $arg = \current($array)) {
        $i = \key($array);
        \next($array);

        return array(
            1 => $arg,
            'value' => $arg,
            0 => $i,
            'key' => $i,
        );
    }

    return false;
}
