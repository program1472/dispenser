<?php
if (!defined('DISPENSER')) exit; // 개별 페이지 접근 불가

/**
 * ───────────────────────────────────────────────────────────
 * 진수 변환 모듈 (DecToJinsu / JinsuToDec)
 * ───────────────────────────────────────────────────────────
 * VB 모듈을 PHP 함수로 옮긴 버전입니다.
 * - DecToJinsu: 10진수 → N진수 문자열
 * - JinsuToDec: N진수 문자열 → 10진수
 * - ConvertToSymbol: 나머지 → 1,2,…,9,A…Z,각,…,학
 * - ConvertToDecimal: 문자(A…Z) → 10…35
 */

/**
 * 나머지 값을 기호(문자)로 변환
 */
function convertToSymbol(int $remainder): string
{
    switch ($remainder) {
        case 1:  return '1';
        case 2:  return '2';
        case 3:  return '3';
        case 4:  return '4';
        case 5:  return '5';
        case 6:  return '6';
        case 7:  return '7';
        case 8:  return '8';
        case 9:  return '9';
        case 10: return 'A';
        case 11: return 'B';
        case 12: return 'C';
        case 13: return 'D';
        case 14: return 'E';
        case 15: return 'F';
        case 16: return 'G';
        case 17: return 'H';
        case 18: return 'I';
        case 19: return 'J';
        case 20: return 'K';
        case 21: return 'L';
        case 22: return 'M';
        case 23: return 'N';
        case 24: return 'O';
        case 25: return 'P';
        case 26: return 'Q';
        case 27: return 'R';
        case 28: return 'S';
        case 29: return 'T';
        case 30: return 'U';
        case 31: return 'V';
        case 32: return 'W';
        case 33: return 'X';
        case 34: return 'Y';
        case 35: return 'Z';
        case 36: return 'a';
        case 37: return 'b';
        case 38: return 'c';
        case 39: return 'd';
        case 40: return 'e';
        case 41: return 'f';
        case 42: return 'g';
        case 43: return 'h';
        case 44: return 'i';
        case 45: return 'j';
        case 46: return 'k';
        case 47: return 'l';
        case 48: return 'm';
        case 49: return 'n';
		case 50: return 'o';
		case 51: return 'p';
		case 52: return 'q';
		case 53: return 'r';
		case 54: return 's';
		case 55: return 't';
		case 56: return 'u';
		case 57: return 'v';
		case 58: return 'w';
		case 59: return 'x';
		case 60: return 'y';
		case 61: return 'z';
        default: return 0;
    }
}

/**
 * 문자(A~Z)를 10진수 값으로 변환
 */
function convertToDecimal(string $symbol): int
{
    switch (mb_strtoupper($symbol, 'UTF-8')) {
        case 'A': return 10;
        case 'B': return 11;
        case 'C': return 12;
        case 'D': return 13;
        case 'E': return 14;
        case 'F': return 15;
        case 'G': return 16;
        case 'H': return 17;
        case 'I': return 18;
        case 'J': return 19;
        case 'K': return 20;
        case 'L': return 21;
        case 'M': return 22;
        case 'N': return 23;
        case 'O': return 24;
        case 'P': return 25;
        case 'Q': return 26;
        case 'R': return 27;
        case 'S': return 28;
        case 'T': return 29;
        case 'U': return 30;
        case 'V': return 31;
        case 'W': return 32;
        case 'X': return 33;
        case 'Y': return 34;
		case 'Z': return 35;
        case 'a': return 36;
		case 'b': return 37;
		case 'c': return 38;
		case 'd': return 39;
		case 'e': return 40;
		case 'f': return 41;
		case 'g': return 42;
		case 'h': return 43;
		case 'i': return 44;
		case 'j': return 45;
		case 'k': return 46;
		case 'l': return 47;
		case 'm': return 48;
		case 'n': return 49;
		case 'o': return 50;
		case 'p': return 51;
		case 'q': return 52;
		case 'r': return 53;
		case 's': return 54;
		case 't': return 55;
		case 'u': return 56;
		case 'v': return 57;
		case 'w': return 58;
		case 'x': return 59;
		case 'y': return 60;
		case 'z': return 61;
        default:  return 0;
    }
}

/**
 * 10진수 → N진수 문자열
 *
 * @param int    $number   변환할 10진수
 * @param int    $base     목표 진수 (예: 2~49)
 * @param string $solution 내부 재귀용(일반적으로 생략)
 * @return string 대문자 문자열
 */
function decToJinsu(int $number, int $base, string $solution = ''): string
{
    // 나머지 구해서 기호로 변환
    $remainder = $number % $base;
    $solution  = convertToSymbol($remainder) . $solution;

    // 몫
    $quotient = intdiv($number, $base);

    if ($quotient > $base) {
        // 몫이 여전히 base보다 크면 재귀
        $solution = decToJinsu($quotient, $base, $solution);
    } else {
        // 최종 몫도 기호로 변환
        $solution = convertToSymbol($quotient) . $solution;
    }

    return mb_strtoupper($solution, 'UTF-8');
}

/**
 * N진수 문자열 → 10진수
 *
 * @param string $value N진수 문자열
 * @param int    $base  진수 (예: 2~62)
 * @return int 10진수 결과
 */
function jinsuToDec(string $value, int $base): int
{
    // 1) 한 글자씩 배열로 (유니코드 안전)
    $chars = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY);
    // 2) 뒤집어서, 인덱스가 0이면 가장 낮은 자리
    $chars = array_reverse($chars);

    $result = 0;
    foreach ($chars as $i => $char) {
        // 3) 숫자면 그대로, 아니면 매핑 테이블로
        if (ctype_digit($char)) {
            $digit = (int) $char;
        } else {
            $digit = convertToDecimal($char);
        }

        // 4) 자리값 = base^i
        $mult = (int) pow($base, $i);

        $result += $digit * $mult;
    }

    return $result;
}

?>