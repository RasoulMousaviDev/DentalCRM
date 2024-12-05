<?php

namespace Database\Seeders;

use App\Models\Holiday;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;
use Morilog\Jalali\Jalalian;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = Jalalian::now()->getYear();
        $endYear = $currentYear + 10;

        for ($year = $currentYear; $year <= $endYear; $year++) {
            $this->fetchAndStoreHolidays($year);
        }
    }

    private function fetchAndStoreHolidays($year)
    {
        $client = new Client();
        $url = "https://www.bahesab.ir/time/$year/";

        try {

            $response = $client->request('GET', $url);
            $html = $response->getBody()->getContents();

            $dom = new \DOMDocument();
            @$dom->loadHTML($html);

            $xpath = new \DOMXPath($dom);
            $listItems = $xpath->query("//section[contains(@class, 'holiday-list')]//ul/li");

            foreach ($listItems as $li) {
                $text = trim($li->nodeValue);
                $holidayPart = explode('،', $text)[0];
                $holidayPart = $this->persianToLatin($holidayPart);
                $holidayDate = $this->convertToGregorian($holidayPart);

                if ($holidayDate && !Holiday::where('date', $holidayDate)->exists()) {
                    Holiday::create([
                        'date' => $holidayDate,
                    ]);
                }
            }

            $this->storeFridays($year);
        } catch (\Exception $e) {
            echo "Error fetching holidays for year $year: " . $e->getMessage() . PHP_EOL;
        }
    }

    private function convertToGregorian($holidayPart)
    {
        $parts = preg_split('/\s+/', $holidayPart);

        if (count($parts) < 4) return null;

        $day = (int) $parts[1];
        $monthName = $parts[2];
        $year = $parts[3];

        $months = [
            'فروردین' => 1,
            'اردیبهشت' => 2,
            'خرداد' => 3,
            'تیر' => 4,
            'مرداد' => 5,
            'شهریور' => 6,
            'مهر' => 7,
            'آبان' => 8,
            'آذر' => 9,
            'دی' => 10,
            'بهمن' => 11,
            'اسفند' => 12
        ];
        $month = $months[$monthName] ?? null;

        if (!$month) return null;

        if($day<10) $day = "0$day";
        if($month<10) $month = "0$month";

        return Jalalian::fromFormat('Y-m-d', "$year-$month-$day",)->toCarbon()->toDateString();
    }

    private function storeFridays($year)
    {
        $firstFriday = Jalalian::fromFormat('Y-m-d', "$year-01-01")->toCarbon();

        while (!$firstFriday->isFriday()) {
            $firstFriday->addDay();
        }

        for ($i = 0; $i < 52; $i++) {
            $fridayDate = $firstFriday->copy()->addDays($i * 7)->toDateString();

            if (!Holiday::where('date', $fridayDate)->exists()) {
                Holiday::create(['date' => $fridayDate]);
            }
        }
    }

    private function persianToLatin($string)
    {
        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $latinDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        // جایگزینی اعداد فارسی با اعداد لاتین
        return str_replace($persianDigits, $latinDigits, $string);
    }
}
