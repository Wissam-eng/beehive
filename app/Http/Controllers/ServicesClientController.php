<?php

namespace App\Http\Controllers;

use App\Models\clients;
use App\Models\services_client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ServicesClientController extends Controller
{

    public function index()
    {
        $services = services_client::all();
        return view('services_client.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'حدث خطاء اثناء التسجيل: ' . $validator->errors());
        }

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $client = clients::find($id);

         $services = services_client::where('client_id', $id)->get();
      
         return view('clients.show_details', compact('services' , 'client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(services_client $services_client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, services_client $services_client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(services_client $services_client)
    {
        //
    }
}
