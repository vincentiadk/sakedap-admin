<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\CollectionMedia;
use Illuminate\Support\Facades\Storage;
use App\Helper\GeneralHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Imagick;
use ImagickDraw;
use Response;
use File;

class CollectionMediaController extends Controller
{
    public function getContentBook($collectionId, $page)
    {

        $collection = Collection::find($collectionId);
        $data       = CollectionMedia::where('collection_id', $collectionId)
            ->where('type', 3)->first();
        $file       = $data ? $data->getImageBook() : null;
        $image      = '';

        if ($collection->acccess != 1) {
            $preview = explode('-', $collection->preview);
            $total_file = $preview[1];
        } else {
            $total_file = count($file);
        }

        if ($page > $total_file) {
            return response()->json([
                'page'            => $page,
                'file'          => null,
                'mime'            => 'image/jpeg',
                'num_of_page'     => $total_file
            ]);
        }


        if ($file) {
            $image = $this->addWatermark($file[(int)$page - 1], $data);
        }



        return response()->json([
            'page'            => $page,
            'file'          => base64_encode($image),
            'mime'            => 'image/jpeg',
            'num_of_page'     => $total_file
        ]);
    }

    public function getContentBook2($collectionId)
    {

        $collection = Collection::find($collectionId);
        $data       = CollectionMedia::where('collection_id', $collectionId)
            ->where('type', 2)->first();

        if (Storage::disk('storage1')->exists($data->link)) {
            $file = Storage::disk('storage1')->get($data->link);
        } else {
            $file = Storage::disk('storage2')->get($data->link);
        }
        return \Response::make($file, 200, array('content-type' => $data->mimes));
    }

    public function cover($id)
    {
        $data = CollectionMedia::where('type', 1)
            ->where('collection_id', $id)
            ->first();

        if (!$data) {
            return response()->json(['message' => 'File not found.'], 404);
        }
        if (Storage::disk('storage1')->exists($data->link)) {
            $file = Storage::disk('storage1')->get($data->link);
        } else {
            $file = Storage::disk('storage2')->get($data->link);
        }
        //$img     = new Imagick($file);
        return \Response::make($file, 200, array('content-type' => $data->mimes));
        // base64_encode($img);
    }

    public function getContentAudio($collectionId)
    {
        $collection = Collection::find($collectionId);
        $data       = CollectionMedia::where('collection_id', $collectionId)
            ->where('type', 6)->first();

        if (!$data) {
            return response()->json([
                'message'   => 'File not found.'
            ], 404);
        }
        if (Storage::disk('storage1')->exists($data->link)) {
            $file = Storage::disk('storage1')->get($data->link);
        } else {
            $file = Storage::disk('storage2')->get($data->link);
        }

        return \Response::make($file, 200, array('content-type' => $data->mimes));
        //$response = new BinaryFileResponse($file);

        //BinaryFileResponse::trustXSendfileTypeHeader();


        //return $response;
    }

    public function getContentAudioVisual($collectionId)
    {
        $collection = Collection::find($collectionId);
        $data       = CollectionMedia::where('collection_id', $collectionId)
            ->where('type', 9)->first();

        if (!$data) {
            return response()->json([
                'message'   => 'File not found.'
            ], 404);
        }

        if (Storage::disk('storage1')->exists($data->link)) {
            $file = Storage::disk('storage1')->get($data->link);
        } else {
            $file = Storage::disk('storage2')->get($data->link);
        }
        $response = new BinaryFileResponse($file);

        BinaryFileResponse::trustXSendfileTypeHeader();


        return $response;
    }

    private function addWatermark($path, $data)
    {
        $img     = new Imagick(Storage::disk($data->location->location)->path($path));

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
        return $img->getImageBlob();
    }

    public function getFileAudio($encrypt)
    {
        $path =  GeneralHelper::decryptString($encrypt);
        $file =  File::get(Storage::disk('local')->path($path)); // mesti dapat storage nya dmn

        $response = response()->make($file, 200);
        $response->header('Content-Type', 'audio/wav');
        return $response;
    }

    public function getFileAudioVisual($encrypt)
    {
        $path =  GeneralHelper::decryptString($encrypt);
        $file =  File::get(Storage::disk('local')->path($path)); // mesti dapat storage nya dmn

        $response = response()->make($file, 200);
        $response->header('Content-Type', 'video/mp4');
        return $response;
    }
}
