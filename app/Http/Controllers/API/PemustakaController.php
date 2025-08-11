<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;

class PemustakaController extends Controller
{
    public function getDetail(Request $request)
    {
        $dateofbirth = $request->get("dateofbirth");
        $data = Member::where('memberno', $request->get('memberno'))
            ->where('dateofbirth', \DB::raw("TO_DATE('$dateofbirth','YYYY-mm-dd')"))
            ->first();
        if ($data) {
            return response()->json([
                "memberno" => $data->memberno,
                "fullname" => $data->fullname,
                "educationlevel" => $data->educationlevel,
                "jenisanggota" => $data->jenisanggota,
                "statusanggota" => $data->statusanggota,
                "jobname" => $data->jobname,
                "dateofbirth" => $data->dateofbirth
            ], 200);
        } else {
            return response()->json([
                "message" => "member not found!"
            ], 500);
        }
    }
}
