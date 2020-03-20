<?php

namespace Voidpointers\Etsy\Helpers;

/**
 * Class RequestValidator
 * @package Gentor\Etsy\Helpers
 */
class RequestValidator
{
    /**
     * @param $args
     * @param $methodInfo
     * @return array
     */
    public static function validateParams($args, $methodInfo)
    {
        $result = array('_valid' => array());
        if (!is_array($methodInfo)) {
            $result['_invalid'][] = 'Method not found';
            return $result;
        }

        if (preg_match_all('@\/\:(\w+)@', $methodInfo['uri'], $match)) {
            if (isset($args['params'])) {
                foreach ($match[0] as $i => $value) {
                    if (!isset($args['params'][$match[1][$i]])) {
                        $result['_invalid'][] = 'Required parameter "' . $match[1][$i] . '" not found';
                    }
                }
            } else {
                $result['_invalid'][] = 'Required parameters not found: ' . implode(', ', $match[1]);
            }

            if (isset($result['_invalid'])) {
                return $result;
            }
        }

        if (isset($args['data'])) {
            $dataResult = static::validateData($args['data'], $methodInfo);
            return array_merge($result, $dataResult);
        }

        return $result;
    }

    /**
     * @param $type
     * @return string
     */
    protected static function transformValueType($type)
    {
        switch ($type) {
            case 'integer':
                return 'int';
            case 'double':
                return 'float';
        }

        return $type;
    }

    /**
     * @param $args
     * @param $methodInfo
     * @return array
     */
    public static function validateData($args, $methodInfo)
    {
        $result = array('_valid' => array());

        if (!is_array($methodInfo)) {
            $result['_invalid'][] = 'Method not found';
            return $result;
        }

        $methodsParams = $methodInfo['params'];
        foreach ($args as $name => $arg) {
            if (isset($methodsParams[$name])) {
                $validType = $methodsParams[$name];
                $type = static::transformValueType(gettype($arg));
                switch ($type) {
                    case 'array':
                        if (@array_key_exists('json', $arg)) {
                            $type = 'stringJSON';
                            $arg = $arg['json'];
                            break;
                        }

                        if (count($arg) > 0) {
                            if (preg_match('@^map\(@', $validType)) {
                                $valueTypes = array();
                                foreach ($arg as $value) {
                                    $valueTypes[] = static::transformValueType(gettype($value));
                                }
                                $type = 'map(' . implode($valueTypes, ', ') . ')';
                                break;
                            }

                            $item_type = static::transformValueType(@gettype($arg[0]));
                            $type = 'array(' . $item_type . ')';
                        }

                        break;

                    case 'string':
                        if ('imagefile' == $validType) {
                            if (!is_file($arg)) {
                                break;
                            }
                            $type = 'imagefile';
                        }

                        break;
                }

                if ($validType !== $type) {
                    if (substr($validType, 0, 4) === 'enum') {
                        if ($arg === 'enum' || !preg_match("@" . preg_quote($arg) . "@", $validType)) {
                            $result['_invalid'][] = 'Invalid enum data param "' . $name . '" value (' . $arg . '): valid values "' . $validType . '"';
                        } else {
                            $result['_valid'][$name] = $arg;
                        }
                    } elseif ($type === 'array' && substr($validType, 0, 5) === 'array' ||
                        $type === 'string' && $validType === 'text'
                    ) {
                        $result['_valid'][$name] = $arg;
                    } elseif ($type === 'json' && substr($validType, 0, 5) === 'array') {
                        $result['_valid'][$name] = $arg;
                    } else {
                        $result['_invalid'][] = static::invalidParamType($name, $arg, $type, $validType);
                    }
                } else {
                    $result['_valid'][$name] = $arg;
                }
            } else {
                $result['_invalid'][] = static::invalidParam($name, gettype($arg));
            }
        }

        return $result;
    }

    /**
     * @param $name
     * @param $type
     * @return string
     */
    public static function invalidParam($name, $type)
    {
        return 'Unrecognized data param "' . $name . '" (' . $type . ')';
    }

    /**
     * @param $name
     * @param $value
     * @param $type
     * @param $validType
     * @return string
     */
    public static function invalidParamType($name, $value, $type, $validType)
    {
        return 'Invalid data param type "' . $name . '" (' . (is_array($value) ? implode(', ', $value) : $value) . ': ' . $type . '): required type "' . $validType . '"';
    }
}
