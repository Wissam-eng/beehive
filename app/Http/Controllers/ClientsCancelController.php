<?php

namespace App\Http\Controllers;

use App\Models\clients_cancel;
use App\Models\clients;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class ClientsCancelController extends Controller
{

    public function index()
    {
        $clients_cancel = clients_cancel::with('client')->where('status', 'pending')->orderBy('created_at', 'desc')->get();
        return view('clients_cancel.index', compact('clients_cancel'));
    }


    public function index_api()
    {
        $id = auth()->user()->id;
        $clients_cancel = clients_cancel::with('client')->where('status', 'pending')->where('client_id', $id)->orderBy('created_at', 'desc')->get();
        return response()->json(['clients_cancel' => $clients_cancel], 200);
    }

    public function store(Request $request)
    {
        try {



            $client = auth()->user()->id;

            $request['client_id'] = $client;

            $validator = Validator::make($request->all(), [
                'client_id' => 'required|integer|exists:clients,id',
                'name' => 'required|string|max:255',
                'bank_number' => 'nullable|string|max:255',
                'mobile_wallet' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }


            $status = clients_cancel::where('client_id', $client)->first();

            if ($status) {
                return response()->json(['error' => 'client already has a cancellation request'], 401);
            }


            $clients_cancel = clients_cancel::create(request()->all());


            $client = clients::find($client);

            $client->update([
                'status' => 'canceled',
            ]);

            return response()->json(['success', 'تم التسجيل الطلب بنجاح'], 200);
        } catch (\Exception $e) {
            return response()->json(['error', 'حدث خطاء اثناء التسجيل: ' . $e->getMessage()], 401);
        }
    }


    public function update(Request $request, clients_cancel $clients_cancel)
    {
        //
    }


    public function destroy($id)
    {

        $clients_cancel = clients_cancel::find($id);

        if (!$clients_cancel) {
            return redirect()->route('clients_cancel.index')->with('error', 'الطلب غير موجود');
        }

        $clients_cancel->forceDelete();

        return redirect()->route('clients_cancel.index')->with('success', 'تم الحذف بنجاح');
    }
}
