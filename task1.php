<?php

/**
 * The task was to create a simple search for a date in a string, the USA format was taken as the basis
 * To solve the task, 3 sites were selected with date standards that can be in a string. I wrote them out in $variants
https://docs.oracle.com/cd/E41183_01/DR/Date_Format_Types.html
https://www.ibm.com/docs/en/cmofz/10.1.0?topic=SSQHWE_10.1.0/com.ibm.ondemand.mp.doc/arsa0257.htm
https://www.wikihow.com/Write-Dates#:~:text=Place%20the%20year%20before%20the,out%20as%202022%20October%209.
 * It took ~30 min to solve the problem, after 3 hours working I stopped, the code can be also expanded:
- Merged date format: Feb172009
- Values that are similar to both time and date, example: "22:22:22 2018-02-21", details:
To fix it I could ignore format: H:i:s
I can also return an array of found dates, for this you need to replace preg_match with preg_match_all
And, when finding several values - use checkdate to all of them
 * I had to use RegEx, because PHP functions don't work correctly with some formats.
 */

// https://docs.oracle.com/cd/E41183_01/DR/Date_Format_Types.html
// https://www.ibm.com/docs/en/cmofz/10.1.0?topic=SSQHWE_10.1.0/com.ibm.ondemand.mp.doc/arsa0257.htm
// https://www.wikihow.com/Write-Dates#:~:text=Place%20the%20year%20before%20the,out%20as%202022%20October%209.

$variants = [
    'bbb' => null,
    '02/17/2009' => '2009-02-17', // Month-Day-Year with leading zeros (02/17/2009)
    '2009/02/17' => '2009-02-17', // Year-Month-Day with leading zeros (2009/02/17)
    '2/17/2009' => '2009-02-17', // Month-Day-Year with no leading zeros (2/17/2009)
    '2009/2/17' => '2009-02-17', // Year-Month-Day with no leading zeros (2009/2/17)
    '2009/ 2/17' => '2009-02-17', // Year-Month-Day with spaces instead of leading zeros (2009/ 2/17)
    '02172009' => '2009-02-17', //Month-Day-Year with no separators (02172009)
    '20090217' => '2009-02-17', //Year-Month-Day with no separators (20090217)
    '02/21/18' => '2018-02-21', //
    '21-02-2018' => '2018-02-21', // Day-Month-Year (incorrect date for Month-Day-Year)
    '21-02-18' => '2018-02-21',
    '2018-02-21' => '2018-02-21',
    '21/2/18' => '2018-02-21',
    '2018-02-21 12:00:00' => '2018-02-21',
    '2018-02-21 10:02:48 AM' => '2018-02-21',
    '17 February, 2009' => '2009-02-17', // Day-Month name-Year
    '2009, February 17' => '2009-02-17', // Year-Month name-Day (2009, February 17)

    'Feb 21, 2018' => '2018-02-21',
    'February 21, 2018' => '2018-02-21',
    'February 17, 2009' => '2009-02-17',
    'Feb 17, 2009' => '2009-02-17',
    '17 Feb, 2009' => '2009-02-17',
    '2009, Feb 17' => '2009-02-17',
    'Feb 17, 2014' => '2014-02-17',
    '17 Feb, 2014' => '2014-02-17',
    '2014, Feb 17' => '2014-02-17',

    '10/09/2009' => '2009-10-09',
    'Oct 9 2009' => '2009-10-09',
    'October 9 2009' => '2009-10-09',
    'October 9, 2009' => '2009-10-09',
    'Sunday, October 9, 2009' => '2009-10-09',

    '17Feb2009' => '2009-02-17',
    '2009Feb17' => '2009-02-17',

    'October ninth, 2009' => '2009-10-09',
    '9th October 2009' => '2009-10-09',
    'Sunday the 9th of October 2009' => '2009-10-09',
    'The 9th of October 2009' => '2009-10-09',
    'The 21st of June 2009' => '2009-06-21',
    'The 22nd of July, 2009' => '2009-07-22',
    'The 23rd of August - 2009' => '2009-08-23',
    'The 24th of September. 2009.' => '2009-09-24',

    '20.november-2022' => '2022-11-20',
    '1-11-1987' => '1987-01-11',
    '1999march12' => '1999-03-12',
    '01.02.03' => '2003-01-02',
    '02/nov/01' => '2001-11-02',
    '01-02-03' => '2003-01-02',
    'november-20-2022' => '2022-11-20',
    '20-november-2022' => '2022-11-20',
    'nov/02/2022' => '2022-11-02',
    'July 25 2010' => '2010-07-25',

    //  'Feb172009' => '2009-02-17',
    //  '12:01:01 2018-02-21' => '2018-02-21',
];

interface IDateParser
{
    public function __construct(string $dateFormat);

    public function getDateFromString(string $inputDate): string|null;
}

class DateParser implements IDateParser
{
    public readonly string $dateFormat;

    private array $availableMonthList = [
        'january', 'february', 'march', 'april', 'may', 'june',
        'july', 'august', 'september', 'october', 'november', 'december',
        'jan', 'feb', 'mar', 'apr', 'may', 'jun',
        'jul', 'aug', 'sep', 'sept', 'oct', 'nov', 'dec',
    ];
    private array $availableEndOfDay = ['st', 'nd', 'rd', 'th'];

    private array $availableDayList = [
        'ninth' => 9,
    ];

    public function __construct(string $dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    public function getDateFromString(string $inputDate): string|null
    {
        $date = $this->tryToGetDateWithNumbersOnly($inputDate);

        if (!$date) {
            $date = $this->tryToGetDateWithNumbersAndMonthTogether($inputDate);
        }

        if (!$date) {
            $date = $this->tryToGetDateInAnyLocationInString($inputDate);
        }

        return ($date instanceof DateTime ? $date->format($this->dateFormat) : null);
    }

    private function tryToGetDateWithNumbersOnly(string $inputDate): DateTime|null
    {
        try {
            if (preg_match('#(\d{2})(\d{2})(\d{4})#i', $inputDate, $matches)) {
                $date = $this->tryToCreateDateFromMonthDayYearOrder($matches);
                if (!$date && preg_match('#(\d{4})(\d{2})(\d{2})#i', $inputDate, $matches)) {
                    $date = $this->createDateTime(
                        year: $matches[1],
                        month: $matches[2],
                        day: $matches[3],
                    );
                }
            } elseif (preg_match('#(\d{1,4})[^\da-z]+(\d{1,2})[^\da-z]+(\d{1,4})#i', $inputDate, $matches)) {
                if (strlen($matches[1]) > 2) {
                    $date = $this->createDateTime(
                        year: $matches[1],
                        month: $matches[2],
                        day: $matches[3],
                    );
                } else {
                    $date = $this->tryToCreateDateFromMonthDayYearOrder($matches);
                    if (!$date) {
                        $date = $this->createDateTime(
                            year: $matches[3],
                            month: $matches[2],
                            day: $matches[1],
                        );
                    }
                }
            }
        } catch (Exception $e) {
            return null;
        }

        return (isset($date) && $date instanceof DateTime ? $date : null);
    }

    /**
     * @param int|string $year
     * @param int|string $month
     * @param int|string $day
     * @return DateTime
     * @throws Exception
     */
    private function createDateTime(int|string $year, int|string $month, int|string $day): DateTime
    {
        return new DateTime($year . '-' . $month . '-' . $day);
    }

    private function tryToCreateDateFromMonthDayYearOrder(array $matches): DateTime|null
    {
        try {
            $date = $this->createDateTime(
                year: $matches[3],
                month: $matches[1],
                day: $matches[2],
            );
        } catch (Exception $e) {
            return null;
        }

        return $date;
    }

    private function tryToGetDateWithNumbersAndMonthTogether(string $inputDate): DateTime|null
    {
        if (
            preg_match(
                '#(\d+[^\da-z]*)?(\d+[^\da-z]*)?('
                . implode('|', $this->availableMonthList) .
                ')([^\da-z]*\d+)?([^\da-z]*\d+)?#iu',
                $inputDate,
                $matches
            )
        ) {
            $matches = $this->cleanMatchesWithNumbersAndMonthTogether($matches);
            $date = $this->convertArrayOfDatePartsToDateTime($matches);
        }

        return (isset($date) && $date instanceof DateTime ? $date : null);
    }

    private function cleanMatchesWithNumbersAndMonthTogether(array $matches): array
    {
        unset($matches[0]);
        foreach ($matches as $k2 => $v2) {
            if (empty($v2)) {
                unset($matches[$k2]);
            } else {
                $matches[$k2] = preg_replace('#[^\da-z]#i', '', $v2);
            }
        }

        return $matches;
    }

    private function convertArrayOfDatePartsToDateTime(array $parts): DateTime|null
    {
        if (count($parts) !== 3) {
            return null;
        }

        $date = [];

        foreach ($parts as $v2) {
            if (!is_numeric($v2)) {
                $tmp = date_parse($v2);
                $date['month'] = $tmp['month'];
            } else {
                if (strlen($v2) > 2) {
                    $date['year'] = $v2;
                } else {
                    $date[(isset($date['day']) ? 'year' : 'day')] = $v2;
                }
            }
        }

        try {
            return $this->createDateTime(
                year:  $date['year'],
                month: $date['month'],
                day:   $date['day'],
            );
        } catch (Exception $e) {
            return null;
        }
    }

    private function tryToGetDateInAnyLocationInString(string $inputDate): DateTime|null
    {
        $date = [];

        if (preg_match('#(' . implode('|', $this->availableMonthList) . ')#i', $inputDate, $matches)) {
            $tmp = date_parse($matches[1]);
            $date['month'] = $tmp['month'];
        }

        if (preg_match('#(\d{4})#i', $inputDate, $matches)) {
            $date['year'] = $matches[1];
        }

        if (
            preg_match(
                '#([^\d]|^)([0-3]?\d)(' . implode('|', $this->availableEndOfDay) . ')#i',
                $inputDate,
                $matches
            )
        ) {
            $date['day'] = $matches[2];
        } elseif (preg_match('#(' . implode('|', array_keys($this->availableDayList)) . ')#i', $inputDate, $matches)) {
            $date['day'] = $this->availableDayList[$matches[1]];
        } elseif (preg_match('#([^\d]|^)([0-3]?\d)([^\d]|$)#i', $inputDate, $matches)) {
            $date['day'] = $matches[2];
        }

        if (count($date) === 3) {
            try {
                return $this->createDateTime(
                    year:  $date['year'],
                    month: $date['month'],
                    day:   $date['day'],
                );
            } catch (Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }
}

$dateParser = new DateParser('Y/M/D');

$result = [];
foreach ($variants as $k => $v) {
    $inputDate = 'Hello. Today: ' . $k . ', I`m glad to see you';
    $result = $dateParser->getDateFromString($inputDate);
    $expectedRusult = ($v ? date_format(date_create($v), $dateParser->dateFormat) : null);

    if ($expectedRusult !== $result) {
        echo $result . ' !== ' . $expectedRusult ,
            '(Details: ' . $inputDate . '|' . $k . '|' . $v . ')' . "Parser error\r\n";
    }
}
