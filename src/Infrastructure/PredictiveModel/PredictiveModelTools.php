<?php

namespace Weather\Infrastructure\PredictiveModel;

class PredictiveModelTools
{
    public static function convertJsonHotPoints2String(string $jsonHotpoints): string
    {
        /** @var array<string,mixed> */
        $hotpoints = json_decode($jsonHotpoints, true);

        $coordinates = array();
        /** @var array<string,string> $item */
        foreach ($hotpoints as $item) {
            $latitude = $item['on_Latitude'];
            $longitude = $item['on_Longitude'];
            $coordinates[] = $latitude . ',' . $longitude;
        }

        return implode(';', $coordinates);
    }
}
