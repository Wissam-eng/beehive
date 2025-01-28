<?php

namespace App\Http\Controllers;

use App\Models\clients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientsController extends Controller
{



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
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [


                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:clients|max:255',
                'mobile' => 'required|string|max:255|unique:clients',
                'gender' => 'required|in:male,female',
                'birth_date' => 'required|date',



                'age' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'work' => 'nullable|string|max:255',
                'center' => 'nullable|string|max:255',
                'landline' => 'nullable|string|max:255',
                'na_number' => 'nullable|string|max:255',
                'governorate' => 'nullable|string|max:255',
                'Village_Street' => 'nullable|string|max:255',
                'another_mobile' => 'nullable|string|max:255|unique:clients',
                'num_of_children' => 'nullable|string|max:255',
                'marital_status' => 'nullable|in:single,married',
                'Academic_qualification' => 'nullable|string|max:255',
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422); // Unprocessable Entity
            }


            $input = $request->except(['img']);
            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $path = $file->store('images/clients', 'public');
                $input['img'] = 'storage/app/public/' . $path;
            }

            $input['password'] = Hash::make($request->password);

            // Save the client
            $new_client = clients::create($input);

            return response()->json([
                'success' => true,
                'data' => $new_client,
            ], 201); // Created
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error
        }
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


    public function show_my_account($id)
    {
        $clients = clients::find($id);


        if (!$clients) {
            return response()->json(['error' => 'Client not found'], 404);
        }


        return response()->json([
            'success' => true,
            'my_account' => $clients
        ]);
    }


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



    public function inactive_my_account($id)
    {
        $client = Clients::find($id);
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        $client->status = 'inactive';
        $client->save();
        return response()->json(['message' => 'your account Inactive'], 200);
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



    public function update(Request $request, clients $clients)
    {
        //
    }


    public function destroy(clients $clients)
    {
        //
    }
}
