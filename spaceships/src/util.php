<?php

// This function truncates a string to the specified length
function truncateString(string $string, int $maxLength, string $ellipsis = '...')
{
  // If the string is longer than the threshold...
  if (strlen($string) > $maxLength) {
    $trimLength = $maxLength - strlen($ellipsis); // Calculate the trim length

    return substr($string, 0, $trimLength) . $ellipsis; // Trim the string and append the ellipsis.
  }

  // String is shorter than maxLength, don't touch it.
  return $string;
}