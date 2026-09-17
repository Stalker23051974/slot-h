<?php

/**
 * @param mixed $array
 * @return int
 */
function dfCount($array) {
    return is_array($array) ? count($array) : 0;
}

/**
 * @param mixed $array
 * @return array
 */
function dfArrayKeys($array, $search = null) {
    return is_array($array) ? (null === $search ? array_keys($array) : array_keys($array, $search)) : [];
}

/**
 * @param mixed $array
 * @return array
 */
function dfArrayValues($array) {
    return is_array($array) ? array_values($array) : [];
}

/**
 * @param mixed $array
 * @return int
 */
function dfArraySum($array) {
    return is_array($array) ? array_sum($array) : 0;
}

/**
 * @param mixed $array0
 * @param mixed $array1
 * @return array
 */
function dfArrayDiff($array0, $array1) {
    return array_diff(is_array($array0) ? $array0 : [], is_array($array1) ? $array1 : []);
}

/**
 * @param mixed $array0
 * @param mixed $array1
 * @return array
 */
function dfArrayIntersect($array0, $array1) {
    return array_intersect(is_array($array0) ? $array0 : [], is_array($array1) ? $array1 : []);
}

/**
 * @param mixed $array
 * @return array
 */
function dfArrayUnique($array) {
    return is_array($array) ? array_unique($array) : [];
}

/**
 * @param mixed $Value
 * @param mixed $array
 * @return bool
 */
function dfInArray($Value, $array) {
    return is_array($array) ? in_array($Value, $array) : false;
}

/**
 * @param mixed $Value
 * @return string
 */
function dfAddslashes($Value) {
    return addslashes((string)$Value);
}

/**
 * @param [] $array
 * @param bool $flag
 * @return bool|object|array
 */
function dfJsonEncode($array, $flag = false) {
    return !is_array($array) ? '[]' : json_encode($array, $flag);
}

/**
 * @param string $string
 * @param bool $flag
 * @return bool|object|array
 */
function dfJsonDecode($string, $flag = false) {
    return !is_string($string) ? false : json_decode($string, $flag);
}

/**
 * @param string $string
 * @param bool $flag
 * @return bool|object|array
 */
function dfArrayMerge($array0, $array1) {
    return array_merge(is_array($array0) ? $array0 : [], is_array($array1) ? $array1 : []);
}

/**
 * @param closure $func
 * @param array $array0
 * @param array|null $array1
 * @return array
 */
function dfArrayMap($func, $array0, $array1 = null) {
  return is_array($array0) && dfCount($array0) > 0 ? (null !== $array1 ? array_map($func, $array0, (is_array($array1)) ? $array1 : []) : array_map($func, $array0)) : [];
}

/**
 * @param string $delimiter
 * @param string $string
 * @return string[]
 */
function dfExplode($delimiter, $string) {
  return is_string($string) ? explode($delimiter, $string) : [$string];
}

/**
 * @param mixed
 * @return string
 */
function dfTrim($string, $all = true) {
    return is_string($string) ? trim($string) : ($all === true ? (string)$string: '');
}

/**
 * @param $array
 * @param $offset
 * @param null $length
 * @param bool $preserveKeys
 * @return array
 */
function dfArraySlice($array, $offset, $length = null, $preserveKeys = false) {
    return !is_array($array) ? [] : array_slice($array, $offset, $length, $preserveKeys);
}