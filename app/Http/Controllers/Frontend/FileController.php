<?php

namespace App\Http\Controllers\Frontend;

use Imagick;
use ImagickDraw;
use App\Models\Banner;
use App\Models\Location;
use App\Helper\GeneralHelper;
use App\Models\CollectionMedia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function getFileFromEncrypt($string)
    {

        $path =  GeneralHelper::decryptString($string);
        $storage = request('storage');
        $img     = new Imagick(Storage::disk($storage)->path('/public/' . $path));

        $text            = 'Pelaksanaan UU No. 13/2018';
        $wm = new ImagickDraw();
        $wm->setFont(public_path('fonts/Roboto-Regular.ttf'));
        $wm->setFontSize(50);
        $wm->setFillColor('grey');
        $wm->setFillOpacity(.3);
        $wm->setGravity(Imagick::GRAVITY_CENTER);
        $img->annotateImage($wm, 0, 0, 0, $text);
        $wm->setGravity(Imagick::GRAVITY_NORTHWEST);
        $img->annotateImage($wm, 10, 10, 0, $text);
        $wm->setGravity(Imagick::GRAVITY_SOUTHEAST);
        $img->annotateImage($wm, 5, 15, 0, $text);

        header('Content-Type: image/' . $img->getImageFormat());
        $thumbnail = $img->getImageBlob();
        echo $thumbnail;
    }

    public function getCover($id)
    {
        $media = CollectionMedia::find($id);
        //$path = Storage::disk($media->location->location)->path($media->link);
        $location = Location::all();
        $path = '';

        foreach ($location as $loc) {
            if (Storage::disk($loc->location)->exists($media->link)) {
                $path = Storage::disk($loc->location)->path($media->link);
                break;
            }
        }

        return response()->download($path);
    }

    public function getEpub()
    {
        $id = request('id');
        $media = CollectionMedia::find($id);
        //$path = Storage::disk($media->location->location)->path($media->link);
        $location = Location::all();
        foreach ($location as $loc) {
            if (Storage::disk($loc->location)->exists($media->link)) {
                $path = Storage::disk($loc->location)->path($media->link);
                break;
            }
        }
        return response()->download($path);
    }

    public function getBanner($id)
    {

        $path = Banner::find($id);

        $location = Location::all();
        foreach ($location as $loc) {
            if (Storage::disk($loc->location)->exists($path->image)) {
                $path = Storage::disk($loc->location)->path($path->image);
                break;
            }
        }
        return response()->download($path);
    }
}
