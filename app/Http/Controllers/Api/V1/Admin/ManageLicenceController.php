<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LicenceKeyManagement;
use App\Models\LicenceHistory;

class ManageLicenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:licences',['except' => ['show']]);
        $this->middleware('permission:licence-add', ['only' => ['store']]);
        $this->middleware('permission:licence-edit', ['only' => ['update']]);
        $this->middleware('permission:licence-read', ['only' => ['show']]);
        $this->middleware('permission:licence-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
