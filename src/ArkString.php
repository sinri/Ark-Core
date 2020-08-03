<?php


namespace sinri\ark\core;

/**
 * Class ArkString
 * @package sinri\ark\core
 * @since 2.7.5
 */
class ArkString
{
    protected $string;

    public function __construct($string)
    {
        $this->string =& $string;
    }

    /**
     * Generate a single-byte string from a number
     * Returns a one-character string containing the character specified by interpreting bytevalue as an unsigned integer.
     * This can be used to create a one-character string in a single-byte encoding such as ASCII, ISO-8859, or Windows 1252, by passing the position of a desired character in the encoding's mapping table. However, note that this function is not aware of any string encoding, and in particular cannot be passed a Unicode code point value to generate a string in a multibyte encoding like UTF-8 or UTF-16.
     * This function complements ord().
     * @param int $ascii An integer between 0 and 255, or (ASCII + 256)%256
     * @return string A single-character string containing the specified byte.
     */
    public static function createCharWithAsciiCode($ascii)
    {
        return chr($ascii);
    }

    /**
     * Convert the first byte of a string to a value between 0 and 255
     * Interprets the binary value of the first byte of string as an unsigned integer between 0 and 255.
     * If the string is in a single-byte encoding, such as ASCII, ISO-8859, or Windows 1252, this is equivalent to returning the position of a character in the character set's mapping table. However, note that this function is not aware of any string encoding, and in particular will never identify a Unicode code point in a multi-byte encoding such as UTF-8 or UTF-16.
     * This function complements chr().
     * @see https://www.php.net/manual/en/function.ord.php
     * @see http://www.asciitable.com/
     * @param string $string
     * @return int An integer between 0 and 255.
     */
    public static function createIntWithAsciiChar($string)
    {
        return ord($string);
    }

    public function __toString()
    {
        return $this->string;
    }

    /**
     * Quote string with slashes in a C style
     * Returns a string with backslashes before characters that are listed in char list parameter.
     * @see https://www.php.net/manual/en/function.addcslashes.php
     * @param string $charList
     * @return string
     */
    public function addCSlashes(string $charList)
    {
        return addcslashes($this->string, $charList);
    }

    /**
     * Returns a string with backslashes stripped off.
     * Recognizes C-like \n, \r ..., octal and hexadecimal representation.
     * @see https://www.php.net/manual/en/function.stripcslashes.php
     * @return string Returns the unescaped string.
     */
    public function removeCSlashes()
    {
        return stripcslashes($this->string);
    }

    /**
     * Quote string with slashes
     * Returns a string with backslashes added before characters that need to be escaped. These characters are:
     * single quote (')
     * double quote (")
     * backslash (\)
     * NUL (the NUL byte)
     *
     * @see https://www.php.net/manual/en/function.addslashes.php
     *
     * @return string
     */
    public function addSlashes()
    {
        return addslashes($this->string);
    }

    /**
     * Un-quotes a quoted string
     * @see https://www.php.net/manual/en/function.stripslashes.php
     * @return string Returns a string with backslashes stripped off. (\' becomes ' and so on.) Double backslashes (\\) are made into a single backslash (\).
     */
    public function stripSlashes()
    {
        return stripslashes($this->string);
    }

    /**
     * Convert binary data into hexadecimal representation
     * Returns an ASCII string containing the hexadecimal representation of str.
     * The conversion is done byte-wise with the high-nibble first.
     * @return string
     */
    public function bin2hex()
    {
        return bin2hex($this->string);
    }

    /**
     * Split a string into smaller chunks
     * Can be used to split a string into smaller chunks
     * which is useful for e.g. converting base64_encode() output to match RFC 2045 semantics.
     * It inserts end every chunk size characters.
     * @see https://www.php.net/manual/en/function.chunk-split.php
     * @param int|null $chunkSize
     * @param string|null $end
     * @return string
     */
    public function splitToChunks($chunkSize = null, $end = null)
    {
        return chunk_split($this->string, $chunkSize, $end);
    }

    /**
     * @see https://www.php.net/manual/en/function.chunk-split.php
     * @param int $chunkSize
     * @param string $end
     * @return string
     */
    public function splitToChunksUnicode($chunkSize = 76, $end = PHP_EOL)
    {
        $pattern = '~.{1,' . $chunkSize . '}~u'; // like "~.{1,76}~u"
        $str = preg_replace($pattern, '$0' . $end, $this->string);
        return rtrim($str, $end);
    }

    /**
     * Converts from one Cyrillic character set to another.
     * Supported characters are:
     * k - koi8-r
     * w - windows-1251
     * i - iso8859-5
     * a - x-cp866
     * d - x-cp866
     * m - x-mac-cyrillic
     * @see https://www.php.net/manual/en/function.convert-cyr-string.php
     * @param string $from The source Cyrillic character set, as a single character.
     * @param string $to The target Cyrillic character set, as a single character.
     * @return string
     */
    public function convertBetweenCyrillicCharSets(string $from, string $to)
    {
        return convert_cyr_string($this->string, $from, $to);
    }

    /**
     * Decode a uuencoded string
     * Note: convert_uudecode() neither accepts the begin nor the end line, which are part of uuencoded files.
     * @see https://www.php.net/manual/en/function.convert-uudecode.php
     * @return string
     */
    public function uuDecode()
    {
        return convert_uudecode($this->string);
    }

    /**
     * Uuencode a string
     * convert_uuencode() encodes a string using the uuencode algorithm.
     * Uuencode translates all strings (including binary data) into printable characters, making them safe for network transmissions.
     * Uuencoded data is about 35% larger than the original.
     * Note: convert_uuencode() neither produces the begin nor the end line, which are part of uuencoded files.
     * @see https://www.php.net/manual/en/function.convert-uuencode.php
     * @return string
     */
    public function uuEncode()
    {
        return convert_uuencode($this->string);
    }

    /**
     * Return information about characters used in a string
     * Counts the number of occurrences of every byte-value (0..255) in string and returns it in various ways.
     * Depending on mode count_chars() returns one of the following:
     * 0 - an array with the byte-value as key and the frequency of every byte as value.
     * 1 - same as 0 but only byte-values with a frequency greater than zero are listed.
     * 2 - same as 0 but only byte-values with a frequency equal to zero are listed.
     * 3 - a string containing all unique characters is returned.
     * 4 - a string containing all not used characters is returned.
     * @see https://www.php.net/manual/en/function.count-chars.php
     * @param int $mode
     * @return int[]|string
     */
    public function countChars($mode = 0)
    {
        return count_chars($this->string, $mode);
    }

    /**
     * Calculates the crc32 polynomial of a string
     * Generates the cyclic redundancy checksum polynomial of 32-bit lengths of the str.
     * This is usually used to validate the integrity of data being transmitted.
     * @return int
     */
    public function getCRC32()
    {
        return crc32($this->string);
    }

    /**
     * One-way string hashing
     * @see https://www.php.net/manual/en/function.crypt.php
     * @param string|null $salt
     * @return string|null
     * @deprecated This function is not (yet) binary safe!
     */
    public function crypt($salt = null)
    {
        return crypt($this->string, $salt = null);
    }

    /**
     * Split a string by a string
     * @param string $delimiter
     * @param int $limit
     * @return ArkArray
     */
    public function explodeInArkArray($delimiter, $limit = null)
    {
        $array = $this->explode($delimiter, $limit);
        return new ArkArray($array);
    }

    /**
     * Split a string by a string
     * @see https://www.php.net/manual/en/function.explode.php
     * @param string $delimiter
     * @param int $limit
     *  If limit is set and positive, the returned array will contain a maximum of limit elements with the last element containing the rest of string.
     *  If the limit parameter is negative, all components except the last -limit are returned.
     *  If the limit parameter is zero, then this is treated as 1.
     * @return false|string[]
     */
    public function explode($delimiter, $limit = null)
    {
        return explode($delimiter, $this->string, $limit);
    }

    /**
     * Decodes a hexadecimally encoded binary string
     * Caution: This function does NOT convert a hexadecimal number to a binary number. This can be done using the base_convert() function.
     * If the hexadecimal input string is of odd length or invalid hexadecimal string an E_WARNING level error is thrown.
     * @see https://www.php.net/manual/en/function.hex2bin.php
     * @return false|string
     */
    public function hex2bin()
    {
        return hex2bin($this->string);
    }

    /*
     * FLAG LIST
     * ENT_COMPAT	Will convert double-quotes and leave single-quotes alone.
     * ENT_QUOTES	Will convert both double and single quotes.
     * ENT_NOQUOTES	Will leave both double and single quotes unconverted.
     * ENT_HTML401	Handle code as HTML 4.01.
     * ENT_XML1	Handle code as XML 1.
     * ENT_XHTML	Handle code as XHTML.
     * ENT_HTML5	Handle code as HTML 5.
     */

    /**
     * Convert HTML entities to their corresponding characters
     * html_entity_decode() is the opposite of htmlentities() in that it converts HTML entities in the string to their corresponding characters.
     * More precisely, this function decodes all the entities (including all numeric entities) that a) are necessarily valid for the chosen document type — i.e., for XML, this function does not decode named entities that might be defined in some DTD — and b) whose character or characters are in the coded character set associated with the chosen encoding and are permitted in the chosen document type. All other entities are left as is.
     * @see https://www.php.net/manual/en/function.html-entity-decode.php
     * @param int|null $flags A bitmask of one or more of the following flags, which specify how to handle quotes and which document type to use. The default is ENT_COMPAT | ENT_HTML401.
     * @param string|null $encoding
     * @return string
     *
     */
    public function decodeHtmlEntity(int $flags = null, string $encoding = null)
    {
        return html_entity_decode($this->string, $flags, $encoding);
    }

    /**
     * Convert all applicable characters to HTML entities
     * This function is identical to htmlspecialchars() in all ways, except with htmlentities(), all characters which have HTML character entity equivalents are translated into these entities.
     * If you want to decode instead (the reverse) you can use html_entity_decode().
     * @see https://www.php.net/manual/en/function.htmlentities.php
     * @param int $flags The default is ENT_COMPAT | ENT_HTML401.
     * @param string $charset
     * @param bool $doubleEncode When double_encode is turned off PHP will not encode existing html entities. The default is to convert everything.
     * @return string
     */
    public function encodeHtmlEntities($flags = null, $charset = null, $doubleEncode = true)
    {
        return htmlentities($this->string, $flags, $charset, $doubleEncode);
    }

    /**
     * Convert special HTML entities back to characters
     * This function is the opposite of htmlspecialchars(). It converts special HTML entities back to characters.
     * The converted entities are: &amp;, &quot; (when ENT_NOQUOTES is not set), &#039; (when ENT_QUOTES is set), &lt; and &gt;.
     * @see https://www.php.net/manual/en/function.htmlspecialchars-decode.php
     * @param int $flag A bitmask of one or more of the following flags, which specify how to handle quotes and which document type to use. The default is ENT_COMPAT | ENT_HTML401.
     * @return string
     */
    public function decodeHtmlSpecialChars($flag = null)
    {
        return htmlspecialchars_decode($this->string, $flag);
    }

    /**
     * Convert special characters to HTML entities
     * @see https://www.php.net/manual/en/function.htmlspecialchars.php
     * @param int $flags A bitmask of one or more of the following flags, which specify how to handle quotes, invalid code unit sequences and the used document type. The default is ENT_COMPAT | ENT_HTML401.
     * @param string $encoding
     * @param bool $doubleEncode When double_encode is turned off PHP will not encode existing html entities, the default is to convert everything.
     * @return string
     * If the input string contains an invalid code unit sequence within the given encoding an empty string will be returned, unless either the ENT_IGNORE or ENT_SUBSTITUTE flags are set.
     */
    public function encodeHtmlSpecialChars($flags = null, $encoding = null, $doubleEncode = true)
    {
        return htmlspecialchars($this->string, $flags, $encoding, $doubleEncode);
    }

    /**
     * Convert a quoted-printable string to an 8 bit string
     * This function returns an 8-bit binary string corresponding to the decoded quoted printable string
     * (according to » RFC2045, section 6.7, not » RFC2821, section 4.5.2, so additional periods are not stripped from the beginning of line).
     * This function is similar to imap_qprint(), except this one does not require the IMAP module to work.
     * @see https://www.php.net/manual/en/function.quoted-printable-decode.php
     * @return string
     */
    public function decodeQuotedPrintable()
    {
        return quoted_printable_decode($this->string);
    }

    /**
     * Convert a 8 bit string to a quoted-printable string
     * Returns a quoted printable string created according to » RFC2045, section 6.7.
     * This function is similar to imap_8bit(), except this one does not require the IMAP module to work.
     * @see https://www.php.net/manual/en/function.quoted-printable-encode.php
     * @return string
     */
    public function quoted_printable_encode()
    {
        return quoted_printable_encode($this->string);
    }

    /**
     * Quote meta characters
     * Returns a version of str with a backslash character (\) before every character that is among these:
     * `. \ + * ? [ ^ ] ( $ )`
     * @return string Returns the string with meta characters quoted, or FALSE if an empty string is given as str.
     * Note: This function is binary-safe.
     */
    public function quoteMetaChars()
    {
        return quotemeta($this->string);
    }

    /**
     * @see https://www.php.net/manual/en/function.rtrim.php
     * This function returns a string with whitespace stripped from the beginning and end of str. Without the second parameter, trim() will strip these characters:
     * Optionally, the stripped characters can also be specified using the character_mask parameter. Simply list all characters that you want to be stripped. With .. you can specify a range of characters.
     * " " (ASCII 32 (0x20)), an ordinary space.
     * "\t" (ASCII 9 (0x09)), a tab.
     * "\n" (ASCII 10 (0x0A)), a new line (line feed).
     * "\r" (ASCII 13 (0x0D)), a carriage return.
     * "\0" (ASCII 0 (0x00)), the NUL-byte.
     * "\x0B" (ASCII 11 (0x0B)), a vertical tab.
     * @param string $charList You can also specify the characters you want to strip, by means of the character_mask parameter. Simply list all characters that you want to be stripped. With .. you can specify a range of characters.
     * @return string
     */
    public function trim($charList = " \t\n\r\0\x0B")
    {
        return trim($this->string, $charList);
    }

    public function ltrim($charList = " \t\n\r\0\x0B")
    {
        return ltrim($this->string, $charList);
    }

    public function rtrim($charList = " \t\n\r\0\x0B")
    {
        return rtrim($this->string, $charList);
    }

    /**
     * Calculate the sha1 hash of a string
     * Calculates the sha1 hash of str using the » US Secure Hash Algorithm 1.
     * Warning
     *  It is not recommended to use this function to secure passwords,
     *  due to the fast nature of this hashing algorithm.
     *  See the Password Hashing FAQ for details and best practices.
     * @see https://www.php.net/manual/en/function.sha1.php
     * @param bool $rawOutput If the optional raw_output is set to TRUE, then the sha1 digest is instead returned in raw binary format with a length of 20, otherwise the returned value is a 40-character hexadecimal number.
     * @return string
     */
    public function sha1($rawOutput = false)
    {
        return sha1($this->string, $rawOutput);
    }

    /**
     * Calculate the md5 hash of a string
     * @see https://www.php.net/manual/en/function.md5.php
     * Warning
     *  It is not recommended to use this function to secure passwords,
     *  due to the fast nature of this hashing algorithm.
     *  See the Password Hashing FAQ for details and best practices.
     * Calculates the MD5 hash of str using the » RSA Data Security, Inc. MD5 Message-Digest Algorithm, and returns that hash.
     * @param bool $rawOutput If the optional raw_output is set to TRUE, then the md5 digest is instead returned in raw binary format with a length of 16.
     * @return string
     */
    public function md5($rawOutput = false)
    {
        return md5($this->string, $rawOutput);
    }

    /**
     * Calculate the soundex key of a string
     * Soundex keys have the property that words pronounced similarly produce the same soundex key, and can thus be used to simplify searches in databases where you know the pronunciation but not the spelling. This soundex function returns a string 4 characters long, starting with a letter.
     * This particular soundex function is one described by Donald Knuth in "The Art Of Computer Programming, vol. 3: Sorting And Searching", Addison-Wesley (1973), pp. 391-392.
     * @see https://www.php.net/manual/en/function.soundex.php
     * @return string|false Returns the soundex key as a string, or FALSE on failure.
     */
    public function soundex()
    {
        return soundex($this->string);
    }

    /**
     * Calculate the metaphone key of a string
     * @see https://www.php.net/manual/en/function.metaphone.php
     * Similar to soundex() metaphone creates the same key for similar sounding words.
     * It's more accurate than soundex() as it knows the basic rules of English pronunciation.
     * The metaphone generated keys are of variable length.
     * Metaphone was developed by Lawrence Philips <lphilips at verity dot com>.
     * It is described in ["Practical Algorithms for Programmers", Binstock & Rex, Addison Wesley, 1995].
     * @see https://www.php.net/manual/en/function.metaphone.php
     * @param int $phonemes This parameter restricts the returned metaphone key to phonemes characters in length. The default value of 0 means no restriction.
     * @return false|string
     */
    public function metaphone($phonemes = 0)
    {
        return metaphone($this->string, $phonemes);
    }

    /**
     * @param string $search
     * @param string $replace
     * @param int $count
     * @return string
     * @see https://www.php.net/manual/en/function.str-replace.php
     */
    public function replace($search, $replace, &$count = null)
    {
        return str_replace($search, $replace, $this->string, $count);
    }

    /**
     * @param string $search
     * @param string $replace
     * @param int $count
     * @return string
     * @see https://www.php.net/manual/en/function.str-ireplace.php
     */
    public function replaceCaseInsensitively($search, $replace, &$count = null)
    {
        return str_ireplace($search, $replace, $this->string, $count);
    }

    /**
     * Inserts HTML line breaks before all newlines in a string
     * Returns string with <br /> or <br> inserted before all newlines (\r\n, \n\r, \n and \r).
     * @see https://www.php.net/manual/en/function.nl2br.php
     * @param bool $isXHtml Whether to use XHTML compatible line breaks or not.
     * @return string
     */
    public function nl2br($isXHtml = true)
    {
        return nl2br($this->string, $isXHtml);
    }

    /**
     * Parses the string into variables
     * Parses encoded_string as if it were the query string passed via a URL and sets variables in the current scope (or in the array if result is provided).
     * @return array If the second parameter result is present, variables are stored in this variable as array elements instead.
     * Warning
     *  Using this function without the result parameter is highly DISCOURAGED and DEPRECATED as of PHP 7.2.
     *  Dynamically setting variables in function's scope suffers from exactly same problems as register_globals.
     *  Read section on security of Using Register Globals explaining why it is dangerous.
     */
    public function parseQueryString()
    {
        parse_str($this->string, $result);
        return $result;
    }

    /**
     * Output a string
     * Outputs arg.
     * print is not actually a real function (it is a language construct) so you are not required to use parentheses with its argument list.
     * The major differences to echo are that print only accepts a single argument and always returns 1.
     * Note: Because this is a language construct and not a function, it cannot be called using variable functions.
     * @see https://www.php.net/manual/en/function.print.php
     * @return int Returns 1, always.
     */
    public function print()
    {
        return print($this->string);
    }

    /**
     * Pad a string to a certain length with another string
     * This function returns the input string padded on the left, the right, or both sides to the specified padding length. If the optional argument pad_string is not supplied, the input is padded with spaces, otherwise it is padded with characters from pad_string up to the limit.
     * @see https://www.php.net/manual/en/function.str-pad.php
     * @param int $padLength If the value of pad_length is negative, less than, or equal to the length of the input string, no padding takes place, and input will be returned.
     * @param string $padString Note: The pad_string may be truncated if the required number of padding characters can't be evenly divided by the pad_string's length.
     * @param int $padType Optional argument pad_type can be STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH. If pad_type is not specified it is assumed to be STR_PAD_RIGHT.
     * @return string Returns the padded string.
     */
    public function pad($padLength, $padString = ' ', $padType = STR_PAD_RIGHT)
    {
        return str_pad($this->string, $padLength, $padString, $padType);
    }

    /**
     * Repeat a string
     * Returns input repeated multiplier times.
     * @see https://www.php.net/manual/en/function.str-repeat.php
     * @param int $multiplier Number of time the input string should be repeated. multiplier has to be greater than or equal to 0. If the multiplier is set to 0, the function will return an empty string.
     * @return string Returns the repeated string.
     */
    public function repeat(int $multiplier)
    {
        return str_repeat($this->string, $multiplier);
    }

    /**
     * Perform the rot13 transform on a string
     * Performs the ROT13 encoding on the str argument and returns the resulting string.
     * The ROT13 encoding simply shifts every letter by 13 places in the alphabet while leaving non-alpha characters untouched. Encoding and decoding are done by the same function, passing an encoded string as argument will return the original version.
     * @see https://www.php.net/manual/en/function.str-rot13.php
     * @return string Returns the ROT13 version of the given string.
     */
    public function rot13()
    {
        return str_rot13($this->string);
    }

    /**
     * Randomly shuffles a string
     * str_shuffle() shuffles a string. One permutation of all possible is created.
     * Caution
     *  This function does not generate cryptographically secure values, and should not be used for cryptographic purposes.
     *  If you need a cryptographically secure value, consider using random_int(), random_bytes(), or openssl_random_pseudo_bytes() instead.
     * @return string Returns the shuffled string.
     */
    public function shuffle()
    {
        return str_shuffle($this->string);
    }

    /**
     * Convert a string to an array
     * @see https://www.php.net/manual/en/function.str-split.php
     * @param int $splitLength Maximum length of the chunk.
     * @return array
     *  If the optional split_length parameter is specified, the returned array will be broken down into chunks with each being split_length in length, otherwise each chunk will be one character in length.
     *  FALSE is returned if split_length is less than 1. If the split_length length exceeds the length of string, the entire string is returned as the first (and only) array element.
     */
    public function split($splitLength = 1)
    {
        return str_split($this->string, $splitLength);
    }

    /**
     * Return information about words used in a string
     * Counts the number of words inside string. If the optional format is not specified, then the return value will be an integer representing the number of words found. In the event the format is specified, the return value will be an array, content of which is dependent on the format. The possible value for the format and the resultant outputs are listed below.
     * For the purpose of this function, 'word' is defined as a locale dependent string containing alphabetic characters, which also may contain, but not start with "'" and "-" characters.
     * @see https://www.php.net/manual/en/function.str-word-count.php
     * @param int $format Specify the return value of this function.
     * @param null $charList A list of additional characters which will be considered as 'word'
     * @return int|string[] Returns an array or an integer, depending on the format chosen.
     *
     * FORMAT: The current supported values are:
     * 0 - returns the number of words found
     * 1 - returns an array containing all the words found inside the string
     * 2 - returns an associative array, where the key is the numeric position of the word inside the string and the value is the actual word itself
     */
    public function countWord($format = 0, $charList = null)
    {
        return str_word_count($this->string, $format, $charList);
    }

    /**
     * Find length of initial segment not matching mask
     * Returns the length of the initial segment of subject which does not contain any of the characters in mask.
     * If start and length are omitted, then all of subject will be examined. If they are included, then the effect will be the same as calling strcspn(substr($subject, $start, $length), $mask) (see substr for more information).
     * @see https://www.php.net/manual/en/function.strcspn.php
     * @param string $mask The string containing every disallowed character.
     * @param null $start The position in subject to start searching.
     * @param null $length The length of the segment from subject to examine. Positive or Negative.
     * @return int Returns the length of the initial segment of subject which consists entirely of characters not in mask.
     * Note:
     *  When a start parameter is set, the returned length is counted starting from this position, not from the beginning of subject.
     */
    public function findUnmaskedPrefixLength($mask, $start = null, $length = null)
    {
        return strcspn($this->string, $mask, $start, $length);
    }

    /**
     * Strip HTML and PHP tags from a string
     * This function tries to return a string with all NULL bytes, HTML and PHP tags stripped from a given str.
     * It uses the same tag stripping state machine as the fgetss() function.
     * @param mixed $allowableTags You can use the optional second parameter to specify tags which should not be stripped. These are either given as string, or as of PHP 7.4.0, as array. Refer to the example below regarding the format of this parameter.
     * @return string Returns the stripped string.
     */
    public function strip_tags($allowableTags)
    {
        return strip_tags($this->string, $allowableTags);
    }

    /**
     * @param string $needle
     * @param int $offset
     * @return false|int
     */
    public function findSubStringIndex($needle, $offset = 0)
    {
        return strpos($this->string, $needle, $offset);
    }

    /**
     * Find the position of the first occurrence of a case-insensitive substring in a string
     * Find the numeric position of the first occurrence of needle in the haystack string.
     * Unlike the strpos(), stripos() is case-insensitive.
     * @param string $needle Note that the needle may be a string of one or more characters.
     * @param int $offset If specified, search will start this number of characters counted from the beginning of the string. If the offset is negative, the search will start this number of characters counted from the end of the string.
     * @return false|int
     *  Returns the position of where the needle exists relative to the beginnning of the haystack string (independent of offset). Also note that string positions start at 0, and not 1.
     *  Returns FALSE if the needle was not found.
     */
    public function findSubStringIndexCaseInsensitively($needle, $offset = 0)
    {
        return stripos($this->string, $needle, $offset);
    }

    /**
     * Returns all of haystack starting from and including the first occurrence of needle to the end.
     * @see https://www.php.net/manual/en/function.strstr.php
     * @param string $needle If needle is not a string, it is converted to an integer and applied as the ordinal value of a character. This behavior is deprecated as of PHP 7.3.0, and relying on it is highly discouraged. Depending on the intended behavior, the needle should either be explicitly cast to string, or an explicit call to chr() should be performed.
     * @param bool $returnBeforeNeedle If TRUE, stristr() returns the part of the haystack before the first occurrence of the needle (excluding needle).
     * @return false|string Returns the matched substring. If needle is not found, returns FALSE.
     */
    public function findSubString($needle, $returnBeforeNeedle = false)
    {
        return strstr($this->string, $needle, $returnBeforeNeedle);
    }

    /**
     * Returns all of haystack starting from and including the first occurrence of needle to the end.
     * @see https://www.php.net/manual/en/function.stristr.php
     * @param string $needle If needle is not a string, it is converted to an integer and applied as the ordinal value of a character. This behavior is deprecated as of PHP 7.3.0, and relying on it is highly discouraged. Depending on the intended behavior, the needle should either be explicitly cast to string, or an explicit call to chr() should be performed.
     * @param bool $returnBeforeNeedle If TRUE, stristr() returns the part of the haystack before the first occurrence of the needle (excluding needle).
     * @return false|string Returns the matched substring. If needle is not found, returns FALSE.
     */
    public function findSubStringCaseInsensitively($needle, $returnBeforeNeedle = false)
    {
        return strstr($this->string, $needle, $returnBeforeNeedle);
    }

    /**
     * Get string length
     * @return int The length of the string on success, and 0 if the string is empty.
     */
    public function length()
    {
        return strlen($this->string);
    }

    /**
     * Search a string for any of a set of characters
     * @param string $charList This parameter is case sensitive.
     * @return false|string Returns a string starting from the character found, or FALSE if it is not found.
     */
    public function seekFirstSubStringStartsWithAnyGivenChars($charList)
    {
        return strpbrk($this->string, $charList);
    }

    /**
     * Find the last occurrence of a character in a string
     * This function returns the portion of haystack which starts at the last occurrence of needle and goes until the end of haystack.
     * @see https://www.php.net/manual/en/function.strrchr.php
     * @param string $char If needle contains more than one character, only the first is used. This behavior is different from that of strstr().
     * @return false|string This function returns the portion of string, or FALSE if needle is not found.
     */
    public function seekLastSubStringStartsWithGivenChar($char)
    {
        return strrchr($this->string, $char);
    }

    /**
     * Reverse a string
     * @see https://www.php.net/manual/en/function.strrev.php
     * @return string
     */
    public function reverse()
    {
        return strrev($this->string);
    }

    /**
     * Find the position of the last occurrence of a case-insensitive substring in a string
     * Find the numeric position of the last occurrence of needle in the haystack string.
     * Unlike the strrpos(), strripos() is case-insensitive.
     * @see https://www.php.net/manual/en/function.strripos.php
     * @param string $needle If needle is not a string, it is converted to an integer and applied as the ordinal value of a character. This behavior is deprecated as of PHP 7.3.0, and relying on it is highly discouraged. Depending on the intended behavior, the needle should either be explicitly cast to string, or an explicit call to chr() should be performed.
     * @param int $offset
     *  If zero or positive, the search is performed left to right skipping the first offset bytes of the haystack.
     *  If negative, the search is performed right to left skipping the last offset bytes of the haystack and searching for the first occurrence of needle.
     * Note: This is effectively looking for the last occurrence of needle before the last offset bytes.
     * @return false|int
     *  Returns the position where the needle exists relative to the beginnning of the haystack string (independent of search direction or offset).
     *  Note: String positions start at 0, and not 1.
     *  Returns FALSE if the needle was not found.
     */
    public function findIndexOfTheLastSubstringCaseInsensitively($needle, $offset = 0)
    {
        return strripos($this->string, $needle, $offset);
    }

    /**
     * @see https://www.php.net/manual/en/function.strrpos.php
     * @param string $needle
     * @param int $offset
     * @return false|int
     */
    public function findIndexOfTheLastSubstring($needle, $offset = 0)
    {
        return strrpos($this->string, $needle, $offset);
    }

    /**
     * Finds the length of the initial segment of a string consisting entirely of characters contained within a given mask
     * @see https://www.php.net/manual/en/function.strspn.php
     * @param string $mask The list of allowable characters.
     * @param int|null $start
     * @param int|null $length
     * @return int Returns the length of the initial segment of subject which consists entirely of characters in mask.
     * Note:
     *  When a start parameter is set, the returned length is counted starting from this position, not from the beginning of subject.
     */
    public function getLengthOfFirstSubStringMaskable($mask, $start = null, $length = null)
    {
        return strspn($this->string, $mask, $start, $length);
    }

    public function toLowerCase()
    {
        return strtolower($this->string);
    }

    public function toUpperCase()
    {
        return strtoupper($this->string);
    }

    /**
     * Tokenize string
     * strtok() splits a string (str) into smaller strings (tokens), with each token being delimited by any character from token. That is, if you have a string like "This is an example string" you could tokenize this string into its individual words by using the space character as the token.
     * Note that only the first call to strtok uses the string argument. Every subsequent call to strtok only needs the token to use, as it keeps track of where it is in the current string. To start over, or to tokenize a new string you simply call strtok with the string argument again to initialize it. Note that you may put multiple tokens in the token parameter. The string will be tokenized when any one of the characters in the argument is found.
     * @see https://www.php.net/manual/en/function.strtok.php
     * @param null|string $token The delimiter used when splitting up str.
     * @return string
     */
    public function tokenize($token = null)
    {
        if ($token !== null) return strtok($this->string, $token);
        return strtok($this->string);
    }

    /**
     * Replace substrings
     * If given two arguments, the second should be an array in the form array('from' => 'to', ...). The return value is a string where all the occurrences of the array keys have been replaced by the corresponding values. The longest keys will be tried first. Once a substring has been replaced, its new value will not be searched again.
     * In this case, the keys and the values may have any length, provided that there is no empty key; additionally, the length of the return value may differ from that of str. However, this function will be the most efficient when all the keys have the same size.
     * @see https://www.php.net/manual/en/function.strtr.php
     * @param array $dict
     * @return string
     */
    public function transformSubStringByDictionary(array $dict)
    {
        return strtr($this->string, $dict);
    }

    /**
     * Translate characters
     * If given three arguments, this function returns a copy of str where all occurrences of each (single-byte) character in from have been translated to the corresponding character in to, i.e., every occurrence of $from[$n] has been replaced with $to[$n], where $n is a valid offset in both arguments.
     * If from and to have different lengths, the extra characters in the longer of the two are ignored. The length of str will be the same as the return value's.
     * @see https://www.php.net/manual/en/function.strtr.php
     * @param string $keyString
     * @param string $targetString
     * @return string Returns the translated string.
     *  If replace_pairs contains a key which is an empty string (""), FALSE will be returned. If the str is not a scalar then it is not typecasted into a string, instead a warning is raised and NULL is returned.
     */
    public function transformSubStringByByteDictionary(string $keyString, string $targetString)
    {
        return strtr($this->string, $keyString, $targetString);
    }

    /**
     * Binary safe comparison of two strings from an offset, up to length characters
     * substr_compare() compares main_str from position offset with str up to length characters.
     * @see https://www.php.net/manual/en/function.substr-compare.php
     * @param string $piece
     * @param int $offset
     * @param int|null $length
     * @param bool $caseInsensitivity
     * @return int
     */
    public function compareSubString($piece, $offset, $length = null, $caseInsensitivity = false)
    {
        return substr_compare($this->string, $piece, $offset, $length, $caseInsensitivity);
    }

    /**
     * Count the number of substring occurrences
     * substr_count() returns the number of times the needle substring occurs in the haystack string. Please note that needle is case sensitive.
     * This function doesn't count overlapped substrings.
     * @see https://www.php.net/manual/en/function.substr-count.php
     * @param string $needle
     * @param int|null $offset
     * @param int|null $length
     * @return int
     */
    public function countSubString($needle, $offset = null, $length = null)
    {
        return substr_count($this->string, $needle, $offset, $length);
    }

    /**
     * Replace text within a portion of a string
     * substr_replace() replaces a copy of string delimited by the start and (optionally) length parameters with the string given in replacement.
     * @see https://www.php.net/manual/en/function.substr-replace.php
     * @param string $replace
     * @param int|null $start
     * @param int|null $length
     * @return string The result string is returned.
     */
    public function replaceSubString(string $replace, $start, $length = null)
    {
        return substr_replace($this->string, $replace, $start, $length);
    }

    /**
     * Return part of a string
     * Returns the portion of string specified by the start and length parameters.
     * @see https://www.php.net/manual/en/function.substr.php
     * @param int $start
     * @param null $length
     * @return false|string Returns the extracted part of string; or FALSE on failure, or an empty string.
     *
     * If start is non-negative, the returned string will start at the start'th position in string, counting from zero. For instance, in the string 'abcdef', the character at position 0 is 'a', the character at position 2 is 'c', and so forth.
     * If start is negative, the returned string will start at the start'th character from the end of string.
     * If string is less than start characters long, FALSE will be returned.
     *
     * If length is given and is positive, the string returned will contain at most length characters beginning from start (depending on the length of string).
     * If length is given and is negative, then that many characters will be omitted from the end of string (after the start position has been calculated when a start is negative). If start denotes the position of this truncation or beyond, FALSE will be returned.
     * If length is given and is 0, FALSE or NULL, an empty string will be returned.
     * If length is omitted, the substring starting from start until the end of the string will be returned.
     */
    public function subString($start, $length = null)
    {
        return substr($this->string, $start, $length);
    }

    /**
     * Make a string's first character uppercase
     * Returns a string with the first character of str capitalized, if that character is alphabetic.
     * Note that 'alphabetic' is determined by the current locale.
     * For instance, in the default "C" locale characters such as umlaut-a (ä) will not be converted.
     * @see https://www.php.net/manual/en/function.ucfirst.php
     * @return string
     */
    public function withFirstCharUpperCase()
    {
        return ucfirst($this->string);
    }

    /**
     * Uppercase the first character of each word in a string
     * Returns a string with the first character of each word in str capitalized, if that character is alphabetic.
     * The definition of a word is any string of characters that is immediately after any character listed in the delimiters parameter (By default these are: space, form-feed, newline, carriage return, horizontal tab, and vertical tab).
     * @see https://www.php.net/manual/en/function.ucwords.php
     * @param string $delimiters
     * @return string
     */
    public function withFirstWordCharUpperCase($delimiters = " \t\r\n\f\v")
    {
        return ucwords($this->string, $delimiters);
    }

    /**
     * Wraps a string to a given number of characters
     * Wraps a string to a given number of characters using a string break character.
     * @param int $width The number of characters at which the string will be wrapped.
     * @param string $break The line is broken using the optional break parameter.
     * @param bool $cut If the cut is set to TRUE, the string is always wrapped at or before the specified width. So if you have a word that is larger than the given width, it is broken apart. (See second example). When FALSE the function does not split the word even if the width is smaller than the word width.
     * @return string
     * @see https://www.php.net/manual/en/function.wordwrap.php
     */
    public function wrappedWord($width = 75, $break = "\n", $cut = false)
    {
        return wordwrap($this->string, $width, $break, $cut);
    }
}