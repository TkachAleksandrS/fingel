<?php

/**
 * @param array $document
 * @param string $searchValue
 * @return void
 */
function search(string $searchValue, array $document): void
{
    foreach ($document as $data) {
        if (isArrayHasSearchValue($data, $searchValue)) {
            render($data);
            exit();
        }

        if (isset($data['children'])) {
            search($searchValue, $data['children']);
        }
    }
}

/**
 * @param array $data
 * @return void
 */
function render(array $data): void
{
    echo getStylesForPage($data) . ' ' . setClassesInText($data);
}

/**
 * @param array $array
 * @param string $searchValue
 * @param string $searchFieldName
 * @return bool
 */
function isArrayHasSearchValue(
    array $array,
    string $searchValue,
    string $searchFieldName = 'id'
): bool
{
    if (!isset($array[$searchFieldName])) {
        return false;
    }

    return $array[$searchFieldName] === $searchValue;
}


/**
 * @param array $data
 * @return array
 */
function getStylePositions(array $data): array
{
    $characterStyleIds = [];
    $char = null;
    $key = 0;
    foreach ($data['characterStyleOverrides'] as $character) {
        if ($char !== $character) {
            $char = $character;
            $key++;
            $characterStyleIds[$key][$character] = 0;
            continue;
        }

        $characterStyleIds[$key][$character] += 1;
    }

    return $characterStyleIds;
}

/**
 * @param array $data
 * @return string
 */
function getStylesForPage(array $data): string
{
    $styleOverrideTable = setDefaultStyle($data);

    $res = '<style> ';

    $res .= getWrapper($data);

    foreach ($styleOverrideTable as $searchValue => $styles) {
        $res .= ".class_${searchValue} { ";

        $res .= prepareStyles($styles);

        $res .= '} ';
    }

    $res .= ' </style>';

    return $res;
}

/**
 * @param array $styles
 * @return string
 */
function prepareStyles(array $styles): string
{
    $res = '';
    foreach ($styles as $nameStyle => $valueStyle) {
        if (is_array($valueStyle)) {
            continue;
        }

        $customStyle = formatCustomStyleNames($nameStyle, $valueStyle);
        $hasCustomStyle = strlen($customStyle);

        if ($hasCustomStyle) {
            $res .= $customStyle;
            continue;
        }

        $res .= camelToSnake($nameStyle).':'.$valueStyle.'; ';
    }

    return $res;
}

/**
 * @param $nameStyle
 * @param $valueStyle
 * @return string
 */
function formatCustomStyleNames($nameStyle, $valueStyle): string
{
    return match ($nameStyle) {
        'lineHeightPx' => " line-height:${valueStyle}px;",
        'lineHeightPercent' => " line-height:${valueStyle}%;",
        default => '',
    };
}

/**
 * @param array $data
 * @return array
 */
function setDefaultStyle(array $data): array
{
    return $data['styleOverrideTable'] + [0 => $data['style']];
}

/**
 * @param array $data
 * @return string
 */
function setClassesInText(array $data): string
{
    $res = '<div class="wrapper"> ';

    $string = $data['characters'];

    $stylePositions = getStylePositions($data);

    $offset = 0;
    foreach ($stylePositions as $stylePosition) {
        foreach ($stylePosition as $classId => $length) {
            $res .= '<span class="class_'.$classId.'">'.mb_substr($string, $offset, $length).'</span>';
            $res .= '['.$classId.'|'.$length.']';
            $offset += $length;
        }
    }

    $res .= '<div>';

    return $res ;
}

/**
 * @param array $data
 * @return string
 */
function getWrapper(array $data): string
{
    $box = $data['absoluteBoundingBox'];

    $res = '.wrapper { width: '.$box['width'].'; height: '.$box['height'].';';

    $res .= prepareStyles($data['style']);
    
    $res .= ' } ';

    return $res;
}

/**
 * @param string $str
 * @return string
 */
function camelToSnake(string $str): string
{
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $str));
}

