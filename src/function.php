<?php

// *** parsing functions> ***

/**
 * parseDataRequest
 * Parse arbitrary multipart/form-data content or application/x-www-form-urlencoded content
 * @param  mixed $content
 * @return array
 */
function parseDataRequest(?string $content): ?array
{
    if (getallheaders()['Content-Type'] === "application/x-www-form-urlencoded") {
        $data = parseApplicationContent($content);
        return empty($data) ? null : $data;
    }
    if (str_contains(getallheaders()['Content-Type'], "multipart/form-data")) {
        $ContTypeArr= explode("=", getallheaders()['Content-Type']);
        $boundary = $ContTypeArr[1];

        $data = parseMultipartContent($content, $boundary);
        return empty($data) ? null : $data;
    }
}

/**
* Parse arbitrary multipart/form-data content
* Note: null result or null values for headers or value means error
* @return array|null [{"headers":array|null,"value":string|null}]
* @param string|null $boundary
* @param string|null $content
*/
function parseMultipartContent(?string $content, ?string $boundary): ?array
{
    if (empty($content) || empty($boundary)) {
        return null;
    }
    $sections = array_map("trim", explode("--$boundary", $content));
    $parts = [];
    $keyArray = [];
    $valueArray = [];
    foreach ($sections as $section) {
        if ($section === "" || $section === "--") {
            continue;
        }
        $fields = explode("\r\n\r\n", $section);

        preg_match('/(?<=["])\w+(?=["])/', $fields[0], $matches);
                    
        foreach ($matches as $match) {
            array_push($keyArray, $match);
            array_push($valueArray, $fields[1]);
        }
    }
    $parts = array_combine($keyArray, $valueArray);
    return empty($parts) ? null : $parts;
}

/**
 * parseApplicationContent
 * Parse arbitrary application/x-www-form-urlencoded content
 * @param  mixed $content
 * @return array
 */
function parseApplicationContent(?string $content): ?array
{
    $parts = [];
    $keyArray = [];
    $valueArray = [];
    $parseData = [];
    $strArr = explode("&", $content);
    foreach ($strArr as &$value) {
        $value = urldecode($value);
        $parseData[] = explode("=", $value);
    }
    for ($j=0; $j < count($parseData); $j++) {
        if (empty($parseData[$j])) {
            unset($parseData[$j]);
        }
        array_push($keyArray, $parseData[$j][0]);
        array_push($valueArray, $parseData[$j][1]);
    }
    
    $parts = array_combine($keyArray, $valueArray);
    
    return empty($parts) ? null : $parts;
}

// *** <parsing functions ***

// *** deleteDirFunc function> ***

function deleteDirFunc(string $searchRoot)
{
    $scandirArr = array_diff(scandir($searchRoot), array('.', '..'));
    foreach ($scandirArr as $value) {
        $value = $searchRoot ."/". $value;
        if (is_dir($value)) {
            $value = $value . "/";
            deleteDirFunc($value);
            if (count(scandir($value)) == 2) {
                rmdir($value);
            }
        } elseif (is_file($value)) {
            unlink($value);
        }
    }
    return $result = "success";
}


// *** <research function ***
