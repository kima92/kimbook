<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 25/02/2024
 * Time: 14:06
 */

namespace App\Actions;

use Illuminate\Support\Facades\Http;

class Niqqud
{

    public function handle(string $text)
    {
        if ($this->is($text)) {
            return $text;
        }

        $response = Http::asJson()->post('https://nakdan-5-3.loadbalancer.dicta.org.il/api', [
            'addmorph' => true,
            'keepmetagim' => true,
            'keepqq' => false,
            'nodageshdefmem' => false,
            'patachma' => false,
            'task' => 'nakdan',
            'data' => $text,
            'useTokenization' => true,
            'genre' => 'modern',
        ]);

        return $response->collect("data")->map(fn($part) => str_replace("|", "", $part["nakdan"]["options"][0]["w"] ?? $part["nakdan"]["word"] ?? ""))->join('');
    }

    public function is(string$string): bool
    {
        // Regular expression to match Hebrew letters and Niqqud
        return preg_match('/[\x{0591}-\x{05C7}]/u', $string);
    }
}
