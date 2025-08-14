<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Publisher;
use App\Imports\BookImport;
use App\Imports\PartiturImport;
use App\Imports\MapImport;
use App\Imports\AudioImport;
use App\Imports\FilmImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Collection;
use App\Models\Author;
use App\Models\JobStatus;
use Zip;
use App\Jobs\BulkUpload;
use App\Exports\MetadataIsbnBulkExport;
use App\Jobs\MetadataIsbnJob;

class CollectionUploadBulkController extends Controller
{

    public function selectType()
    {

        $publisher = User::find(session('id'))->publisher;

        $data = [
            'title'         => 'Pilih Tipe Koleksi Buku',
            'publisher'     => $publisher,
            'content'       => 'publisher.collection.select_type_import',
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');



        $totalData = JobStatus::where('user_id', session('id'))->where('queue', 'bulk_upload')->count();
        if (empty($search)) {
            $queryData = JobStatus::where('user_id', session('id'))
                ->where('queue', 'bulk_upload')
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = JobStatus::where('user_id', session('id'))->count();
        } else {
            $queryData = JobStatus::where('user_id', session('id'))
                ->where('queue', 'bulk_upload')
                ->where(function ($query) use ($search) {
                    $query->where('status', 'like', "%{$search}%")
                        ->orWhere('job_id', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = JobStatus::where('user_id', session('id'))
                ->where('queue', 'bulk_upload')
                ->where(function ($query) use ($search) {
                    $query->where('status', 'like', "%{$search}%")
                        ->orWhere('job_id', 'like', "%{$search}%");
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {

                $output = json_encode($val->output);
                $error = "";
                if (isset($output['error'])) {
                    $error = $output['error'];
                }

                $response['data'][] = [
                    $val->type(),
                    $val->job_id,
                    "<div class='progress'><div class='progress-bar progress-bar-striped progress-bar-animated bg-success' role='progressbar' aria-valuenow='$val->progress_now' aria-valuemin='$val->progress_now' aria-valuemax='$val->progress_max' style='width:$val->progress_now%'></div></div>",
                    $val->status(),
                    $output,
                    $error,
                    date('d-m-Y H:i:s', strtotime($val->created_at)),
                    $val->started_at == null ? '' : date('d-m-Y H:i:s', strtotime($val->started_at)),
                    $val->finished_at == null ? '' : date('d-m-Y H:i:s', strtotime($val->finished_at))
                ];
            }
        }

        $response['recordsTotal'] = 0;
        if ($totalData <> FALSE) {
            $response['recordsTotal'] = $totalData;
        }

        $response['recordsFiltered'] = 0;
        if ($totalFiltered <> FALSE) {
            $response['recordsFiltered'] = $totalFiltered;
        }

        return response()->json($response);
    }

    public function index($typeId)
    {

        $user = User::find(session('id'));
        $publisher = Publisher::find($user->userable_id);

        if ($typeId == 1) {
            $data = [
                'title'         => 'Upload Koleksi Buku',
                'content'       => 'publisher.book.bulk_upload',
                'typeId'        => $typeId,
                'publisher'     => $publisher
            ];

            return view('publisher.layout.index', ['data' => $data]);
        } else if ($typeId == 2) {
            $data = [
                'title'         => 'Upload Koleksi Partitur',
                'content'       => 'publisher.partitur.bulk_upload',
                'typeId'        => $typeId,
                'publisher'     => $publisher
            ];

            return view('publisher.layout.index', ['data' => $data]);
        } else if ($typeId == 3) {
            $data = [
                'title'         => 'Upload Koleksi Peta',
                'content'       => 'publisher.map.bulk_upload',
                'typeId'        => $typeId,
                'publisher'     => $publisher
            ];

            return view('publisher.layout.index', ['data' => $data]);
        } else if ($typeId == 5) {
            $data = [
                'title'         => 'Upload Koleksi Audio',
                'content'       => 'publisher.audio.bulk_upload',
                'typeId'        => $typeId,
                'publisher'     => $publisher
            ];

            return view('publisher.layout.index', ['data' => $data]);
        } else if ($typeId == 6) {
            $data = [
                'title'         => 'Upload Koleksi Video',
                'content'       => 'publisher.film.bulk_upload',
                'typeId'        => $typeId,
                'publisher'     => $publisher
            ];

            return view('publisher.layout.index', ['data' => $data]);
        } else if ($typeId == 4) {
            $data = [
                'title'         => 'Upload Koleksi Serial',
                'content'       => 'publisher.serial.bulk_upload',
                'typeId'        => $typeId,
                'publisher'     => $publisher
            ];

            return view('publisher.layout.index', ['data' => $data]);
        }
    }

    public function upload(Request $request, $typeId)
    {

        $params = ['type_id' => $typeId, 'file_zip' => $request->file_zip, 'user_id' => session('id'), 'type_of_collection' => $request->input('type_of_collection'), 'collectionId' => $request->input('collection_id')];

        $job = new BulkUpload($params);
        dispatch(($job)->onQueue('bulk_upload'));

        activity()
            ->causedBy(User::find(session('id')))
            ->log('Mengupload unggah banyak');

        session()->flash('success', 'Sedang proses upload!');
        $response = ['status' => 200];
        return response()->json($response);
    }

    public function downloadBillISBN(Request $request)
    {

        $user = User::find(session('id'));
        $publisher_id = $user->publisher->code_system;

        $data = [
            'publisher_id'  => $publisher_id,
            'user_id'       => session('id')
        ];

        MetadataIsbnJob::dispatch($data)->onQueue('report');

        return response()->json(200);
    }
}
