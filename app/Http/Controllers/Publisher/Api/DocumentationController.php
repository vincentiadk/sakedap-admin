<?php

namespace App\Http\Controllers\Publisher\Api;

use App\Models\Publisher;
use App\Models\AuthClient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PublisherAccess;
use App\Http\Controllers\Controller;

class DocumentationController extends Controller
{

    public function index()
    {
        $publisher_id = session('id');
        $publisher    = Publisher::find($publisher_id);

        if ($publisher->publisherAccess->count() > 0) {
            $access = $publisher->publisherAccess->first();
            if ($access->publisherGroup) {
                $check = AuthClient::where('authable_type', 'publisher_groups')->where('authable_id', $publisher_id)->first();
                if ($check) {
                    $credentials = $check;
                } else {
                    $group_id        = $access->publisherGroup->id;
                    $check_same_auth = AuthClient::whereHas('publisherAccess', function ($query) use ($group_id) {
                        $query->where('publisher_group_id', $group_id);
                    })
                        ->first();

                    if ($check_same_auth) {
                        $client_id     = $check_same_auth->client_id;
                        $client_secret = $check_same_auth->client_secret;
                    } else {
                        $client_id     = Str::uuid();
                        $client_secret = md5(Str::uuid());
                    }

                    $credentials = AuthClient::create([
                        'client_id'     => $client_id,
                        'client_secret' => $client_secret,
                        'authable_type' => 'publisher_access',
                        'authable_id'   => $publisher_id
                    ]);
                }
            } else {
                $check = AuthClient::where('authable_type', 'publishers')->where('authable_id', $publisher_id)->first();
                if ($check) {
                    $credentials = $check;
                } else {
                    $credentials = AuthClient::create([
                        'client_id'     => Str::uuid(),
                        'client_secret' => md5(Str::uuid()),
                        'authable_type' => 'publishers',
                        'authable_id'   => $publisher_id
                    ]);
                }
            }
        } else {
            $check = AuthClient::where('authable_type', 'publishers')->where('authable_id', $publisher_id)->first();
            if ($check) {
                $credentials = $check;
            } else {
                $credentials = AuthClient::create([
                    'client_id'     => Str::uuid(),
                    'client_secret' => md5(Str::uuid()),
                    'authable_type' => 'publishers',
                    'authable_id'   => $publisher_id
                ]);
            }
        }

        $data = [
            'title'       => 'Dokumentasi API',
            'credentials' => $credentials,
            'content'     => 'publisher.api.documentation',
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }
}
