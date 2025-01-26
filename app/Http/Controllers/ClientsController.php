<?php

namespace App\Http\Controllers;

use App\Models\clients;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = clients::where('status', 'active')->get();
        return view('clients.index', compact('clients'));
    }


    public function ClientsInActive()
    {

        $clients = clients::where('status', 'inactive')->get();
        return view('clients.ClientsInActive', compact('clients'));
    }


    public function store(Request $request)
    {
        //
    }


    public function show_details($id)
    {
        $clients = clients::with('orders')->find($id);

        // dd($clients->orders);
        if (!$clients) {
            return redirect()->back()->with('error', 'Client not found');
        }


        return view('clients.show_details', compact('clients'));
    }


    public function show($id)
    {
        $clients = clients::find($id);


        if (!$clients) {
            return redirect()->back()->with('error', 'Client not found');
        }


        return view('clients.show', compact('clients'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(clients $clients)
    {
        //
    }




    public function inactive_client($id)
    {
        $client = Clients::find($id);
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found');
        }
        $client->status = 'inactive';
        $client->save();
        return redirect()->back()->with('success', 'Client Inactive');
    }


    public function active_client($id)
    {
        $client = Clients::find($id);
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found');
        }
        $client->status = 'active';
        $client->save();
        return redirect()->back()->with('success', 'Client active');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, clients $clients)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(clients $clients)
    {
        //
    }
}
