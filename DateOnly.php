<?php

class DateOnly {

    private const DAYS_IN_MONTH = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    private array $data;

    public function __construct(int $year, int $month, int $day) {
        $this->data = self::adjustDate($year, $month, $day);
    }

    public function __get($key) {
        return $this->data[$key];
    }

    private static function validateDate(string $date) { // valid format: yyyy-MM-dd
        $pattern = '/^[0-9]{4}(-[0-9]{2}){2}$/';
        return preg_match($pattern, $date);
    }

    private static function explodeDate(string $date) {
        if (self::validateDate($date)) {
            $parts = explode('-', $date);
            return [
                'year'=>(int)$parts[0],
                'month'=>(int)$parts[1],
                'day'=>(int)$parts[2]
            ];
        }
        return null;
    }

    private static function adjustYear(int $year) {
        return $year < 1 ? 1 : ($year > 9999 ? 9999 : $year);
    }

    private static function adjustMonth(int $month) {
        return $month < 1 ? 1 : ($month > 12 ? 12 : $month);
    }

    private static function isLeapYear(int $year) {
        return $year % 100 == 0 ? ($year % 400 == 0) : ($year % 4 == 0);
    }

    public static function getDaysInMonth(int $month, int $year) {
        $index = $month - 1;
        return self::DAYS_IN_MONTH[$index] + ($month == 2 && self::isLeapYear($year) ? 1 : 0);
    }

    private static function adjustDay(int $day, int $month, int $year) {
        $totalDays = self::getDaysInMonth($month, $year);
        return $day < 1 ? 1 : ($day > $totalDays ? $totalDays : $day);
    }

    private static function adjustDate(int $year, int $month, int $day) {
        $month = self::adjustMonth($month);
        $year = self::adjustYear($year);
        return [
            'year'=>$year,
            'month'=>$month,
            'day'=>self::adjustDay($day, $month, $year)
        ];
    }

    public static function buildByString(string $date): ?DateOnly {
        $parts = self::explodeDate($date);
        return isset($parts) ? new DateOnly($parts['year'], $parts['month'], $parts['day']) : null;
    }

    public function __toString() {
        return $this->toString();
    }

    public function toString(string $format = "dd/MM/yyyy"): string {

        switch($format){
            default:
            case "dd/MM/yyyy":
                return str_pad($this->day, 2, '0', STR_PAD_LEFT) . '/' . str_pad($this->month, 2, '0', STR_PAD_LEFT) . '/' . str_pad($this->year, 4, '0', STR_PAD_LEFT);
            case "MM/dd/yyyy":
                return str_pad($this->month, 2, '0', STR_PAD_LEFT) . '/' . str_pad( $this->day, 2,'0', STR_PAD_LEFT) . '/' . str_pad($this->year, 4, '0', STR_PAD_LEFT);
            case "yyyy/MM/dd":
                return str_pad($this->year, 4, '0', STR_PAD_LEFT) . '/' . str_pad($this->month, 2, '0', STR_PAD_LEFT) . '/' . str_pad($this->day, 2, '0', STR_PAD_LEFT);
            case "d/M/y":
                return $this->day . '/' . $this->month . '/' . $this->year;
            case "M/d/y":
                return $this->month . '/' . $this->day . '/' . $this->year;
            case "y/M/d":
                return $this->year . '/' . $this->month . '/' . $this->day;
        }
    }
}

echo new DateOnly(1997,12,8) . "\n";
echo DateOnly::buildByString('2002-05-30');