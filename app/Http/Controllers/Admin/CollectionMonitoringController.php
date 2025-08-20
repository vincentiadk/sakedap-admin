<?php

namespace App\Http\Controllers\Admin;

use App\Models\Problem;
use App\Models\Setting;
use App\Models\Director;
use App\Models\Location;
use App\Models\Collection;
use App\Jobs\WatermarkAudio;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Jobs\SetIsbnReceived;
use App\Models\CollectionMedia;
use App\Models\CollectionProblem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\DashboardController;


class CollectionMonitoringController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function index(Request $request)
    {
        if ($request->has('type')) {
            $collection_id = Collection::select('id')
                ->where('type', $request->type)
                ->where('status', 1)
                ->where('parent_id', 0)
                ->orderBy('id', 'asc')
                ->first();

            if ($collection_id) {
                return redirect('admin/collection/monitoring/review/' . $collection_id->id);
            } else {
                echo '<script>alert("Tidak ada koleksi yang direview.")</script>';
                echo '<script>window.location.href="' . url('admin/collection/monitoring') . '"</script>';
            }
        } else {
            $data = [
                'title'          => 'Pemantauan Koleksi',
                'total_book'     => DashboardController::statistic('collection_type_status', [1, 1]),
                'total_partitur' => DashboardController::statistic('collection_type_status', [2, 1]),
                'total_map'      => DashboardController::statistic('collection_type_status', [3, 1]),
                'total_serial'   => DashboardController::statistic('collection_type_status', [4, 1]),
                'total_audio'    => DashboardController::statistic('collection_type_status', [5, 1]),
                'total_film'     => DashboardController::statistic('collection_type_status', [6, 1]),
                'content'        => 'admin.collection.monitoring'
            ];

            return view('admin.layout.index', ['data' => $data]);
        }
    }

    public function review(Request $request, $id)
    {
        $collection = Collection::find($id);

        $collection_id = Collection::select('id')
            ->where('type', $collection->type)
            ->where('status', $collection->status)
            ->where('parent_id', 0)
            ->orderBy('id', 'asc')
            ->first();

        if ($collection->status != 1) {
            return redirect('admin/collection/monitoring/review/' . $collection_id->id);
        }

        if ($request->has('_token') && session()->token() == $request->_token) {
            if ($collection->code) {
                if ($request->status == 3 || $request->status == 5) {
                    $check = 0;
                } else {
                    $check = Collection::where('code', $collection->code)
                        ->whereNotNull('deposit')
                        ->where('parent_id', 0)
                        ->where('status', 2)
                        ->whereNotNull('received_at')
                        ->whereNotNull('received_by')
                        ->count();
                }
            } else {
                $check = 0;
            }

            if ($check < 1) {
                if ($request->status == 3) {
                    Collection::where('id', $id)->update([
                        'status'       => 3,
                        'problem'      => $request->problem,
                        'updated_by'   => session('id'),
                        'rejected_by'  => session('id'),
                        'rejected_at'  => date('Y-m-d H:i:s')
                    ]);

                    $problem = '';
                    if ($request->has('collection_problem')) {
                        foreach ($request->collection_problem as $key => $cp) {
                            $data       = Problem::find($cp);
                            $delimeter  = $key + 1 == count($request->collection_problem) ? '' : ', ';
                            $problem   .= $data->name . $delimeter;

                            CollectionProblem::create([
                                'collection_id' => $id,
                                'problem_id'    => $cp,
                                'solved'        => 0
                            ]);
                        }
                    }

                    $template = Setting::where('slug', 'template-email-collection-problem')->first();
                    if ($collection->publisher->email) {
                        Mail::send([], [], function ($message) use ($collection, $template) {
                            $header      = Setting::where('slug', 'template-email-header')->first();
                            $footer      = Setting::where('slug', 'template-email-footer')->first();
                            $link_header = public_path('storage/' . str_replace('public/', '', $header->content));
                            $link_footer = public_path('storage/' . str_replace('public/', '', $footer->content));

                            $data = [
                                'header'    => '<img src="' . $message->embed($link_header) . '" style="width:100%;">',
                                'footer'    => '<img src="' . $message->embed($link_footer) . '" style="width:100%;">',
                                'publisher' => $collection->publisher->name,
                                'title'     => $collection->title
                            ];

                            $message->to($collection->publisher->email, 'edeposit@perpusnas.go.id')
                                ->subject('Koleksi Bermasalah')
                                ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                                ->setBody($template->parse($data), 'text/html');
                        });
                    }

                    activity('collections')
                        ->performedOn($collection)
                        ->causedBy(session('id'))
                        ->withProperties(['judul' => $collection->title, 'masalah' => $problem])
                        ->log('Koleksi bermasalah (' . $collection->title . ')');

                    Notification::create([
                        'user_id' => $collection->publisher->user->id,
                        'title'   => 'Koleksi Bermasalah',
                        'body'    => $template->content
                    ]);

                    return redirect('admin/collection/monitoring')->with(['success' => 'Koleksi berhasil di update!']);
                } else if ($request->status == 5) {
                    Collection::where('id', $id)->update([
                        'status'       => 5,
                        'problem'      => 'Ditolak',
                        'updated_by'   => session('id'),
                        'rejected_by'  => session('id'),
                        'rejected_at'  => date('Y-m-d H:i:s')
                    ]);

                    $template = Setting::where('slug', 'template-email-collection-problem')->first();
                    if ($collection->publisher->email) {
                        Mail::send([], [], function ($message) use ($collection, $template) {
                            $header      = Setting::where('slug', 'template-email-header')->first();
                            $footer      = Setting::where('slug', 'template-email-footer')->first();
                            $link_header = public_path('storage/' . str_replace('public/', '', $header->content));
                            $link_footer = public_path('storage/' . str_replace('public/', '', $footer->content));

                            $data = [
                                'header'    => '<img src="' . $message->embed($link_header) . '" style="width:100%;">',
                                'footer'    => '<img src="' . $message->embed($link_footer) . '" style="width:100%;">',
                                'publisher' => $collection->publisher->name,
                                'title'     => $collection->title
                            ];

                            $message->to($collection->publisher->email, 'edeposit@perpusnas.go.id')
                                ->subject('Koleksi Ditolak')
                                ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                                ->setBody($template->parse($data), 'text/html');
                        });
                    }

                    activity('collections')
                        ->performedOn($collection)
                        ->causedBy(session('id'))
                        ->withProperties(['judul' => $collection->title])
                        ->log('Menolak koleksi (' . $collection->title . ')');

                    Notification::create([
                        'user_id' => $collection->publisher->user->id,
                        'title'   => 'Koleksi Ditolak',
                        'body'    => $template->content
                    ]);

                    return redirect('admin/collection/monitoring')->with(['success' => 'Koleksi berhasil di update!']);
                } else if ($request->status == 2) {
                    Collection::where('id', $id)->update([
                        'deposit'      => GeneralHelper::depositCollection($request->received_at),
                        'copyright'    => 'Copyrights (c) ' . date('Y') . ' ' . $collection->publisher->name,
                        'status'       => 2,
                        'received_at'  => date('Y-m-d H:i:s', strtotime($request->received_at)),
                        'received_by'  => session('id'),
                        'updated_by'   => session('id'),
                    ]);

                    activity('collections')
                        ->performedOn($collection)
                        ->causedBy(session('id'))
                        ->withProperties(['judul' => $collection->title])
                        ->log('Menyetujui koleksi (' . $collection->title . ')');

                    CollectionProblem::where('collection_id', $id)->update(['solved' => 1]);
                    $template = Setting::where('slug', 'template-email-collection-success')->first();

                    if ($collection->type == 1 && $collection->code != "") {
                        if (config('app.env') == 'production') {
                            if ($collection->type_book == '1') {
                                SetIsbnReceived::dispatch($collection->code, date('Y-m-d H:i:s', strtotime($request->received_at)))->onQueue('check_isbn');
                                $collection->update(['sync' => 1]);
                            }
                        }
                    }

                    if ($collection->publisher->email) {
                        Mail::send([], [], function ($message) use ($collection, $template) {
                            $collection_media = $collection->collectionMedia->first();
                            $header           = Setting::where('slug', 'template-email-header')->first();
                            $footer           = Setting::where('slug', 'template-email-footer')->first();
                            $link_header      = public_path('storage/' . str_replace('public/', '', $header->content));
                            $link_footer      = public_path('storage/' . str_replace('public/', '', $footer->content));
                            $director         = Director::where('province_id', session('province_id'))->orderByRaw('DATE(position_start) DESC')->first();

                            if ($director) {
                                $signature = $director->position . ', <br>' . $director->name . '<br><br><img src="' . public_path('storage/' . str_replace('public/', '', $director->signature)) . '" width="180"><br><br>NIP. ' . $director->nip;
                            } else {
                                $signature = '';
                            }

                            $data = [
                                'header'      => '<img src="' . $message->embed($link_header) . '" style="width:100%;">',
                                'footer'      => '<img src="' . $message->embed($link_footer) . '" style="width:100%;">',
                                'received_at' => $collection->received_at,
                                'code'        => $collection->code,
                                'publisher'   => $collection->publisher->name,
                                'title'       => $collection->title,
                                'depositid'   => $collection->deposit,
                                'mimes'       => $collection_media->mimes,
                                'hash'        => $collection_media->hash,
                                'size'        => $collection_media->size,
                                'director'    => $signature
                            ];

                            $message->to($collection->publisher->email, 'edeposit@perpusnas.go.id')
                                ->subject('Koleksi Diverifikasi')
                                ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                                ->setBody($template->parse($data), 'text/html');
                        });
                    }

                    if ($collection->type == 5) {

                        $original = $collection->collectionMedia->where('type', 2)->first();
                        $dir_original = Storage::disk($this->location->location)->path($original->link);

                        $create_media = CollectionMedia::create([
                            'collection_id' => $collection->id,
                            'link'          => $dir_original,
                            'size'          => File::size($dir_original),
                            'extension'     => $original->extension,
                            'mimes'         => File::mimeType($dir_original),
                            'hash'          => md5_file($dir_original),
                            'type'          => 2,
                            'method'        => 4,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s'),
                            'location_id'   => $this->location->id
                        ]);

                        dispatch(new WatermarkAudio(Storage::disk($this->location->location)->path($dir_original), $create_media))->onQueue('audio');
                    }

                    Notification::create([
                        'user_id' => $collection->publisher->user->id,
                        'title'   => 'Koleksi Divalidasi',
                        'body'    => $template->content
                    ]);

                    return redirect('admin/collection/monitoring')->with(['success' => 'Koleksi berhasil di update!']);
                } else {
                    return redirect()->back()->with(['failed' => 'Koleksi gagal di update!']);
                }
            } else {
                return redirect()->back()->with(['failed' => 'Koleksi telah ada dipengelolaan!']);
            }
        } else {
            if ($collection->type == 1) {
                if (count($collection->edition()->get()) > 0) {
                    $data = [
                        'title'   => 'Review Pemantauan Buku',
                        'content' => 'admin.book.review_monitoring_jilid'
                    ];
                } else {
                    $data = [
                        'title'   => 'Review Pemantauan Buku',
                        'content' => 'admin.book.review_monitoring'
                    ];
                }
            } else if ($collection->type == 2) {
                $data = [
                    'title'   => 'Review Pemantauan Partitur',
                    'content' => 'admin.partitur.review_monitoring'
                ];
            } else if ($collection->type == 3) {
                $data = [
                    'title'   => 'Review Pemantauan Peta',
                    'content' => 'admin.map.review_monitoring'
                ];
            } else if ($collection->type == 4) {
                $data = [
                    'title'   => 'Review Pemantauan Serial',
                    'content' => 'admin.serial.review_monitoring'
                ];
            } else if ($collection->type == 5) {
                $data = [
                    'title'   => 'Review Pemantauan Audio',
                    'content' => 'admin.audio.review_monitoring'
                ];
            } else if ($collection->type == 6) {
                $data = [
                    'title'   => 'Review Pemantauan Film',
                    'content' => 'admin.film.review_monitoring'
                ];
            } else {
                return redirect()->back();
            }

            $edition = Collection::where('parent_id', $id)
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->get();

            $data = array_merge($data, [
                'collection'    => $collection,
                'problem'       => Problem::all(),
                'collection_id' => $collection_id->id,
                'edition'       => $edition
            ]);

            return view('admin.layout.index', ['data' => $data]);
        }
    }
}
