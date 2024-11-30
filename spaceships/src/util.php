<?php

function truncateString(string $string, int $maxLength, string $ellipsis = '...')
{
  if (strlen($string) > $maxLength) {
    $trimLength = $maxLength - strlen($ellipsis);

    return substr($string, 0, $trimLength) . $ellipsis;
  }

  return $string;
}