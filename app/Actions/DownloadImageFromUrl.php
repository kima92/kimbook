<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 17/04/2024
 * Time: 18:49
 */

namespace App\Actions;

use App\Jobs\DownloadImage;
use App\Models\Image;

class DownloadImageFromUrl
{


    public function execute(Image $image, string $url): void
    {
        $image->image_url = $url;
        $image->save();
        dispatch(new DownloadImage($image));
    }
}
