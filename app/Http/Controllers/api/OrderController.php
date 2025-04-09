<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;


class OrderController extends Controller
{
    //

    public  function index()
    {
        $orders = Order::with('user', 'doctor', 'clinic')->get();
        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public  function getOrderByClinic($id)
    {
        $orders = Order::with('user', 'doctor', 'clinic')->where('clinic_id', $id)->get();
        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function adminClinicSummary($id)
    {
        $orders = Order::with('user', 'doctor', 'clinic')->where('clinic_id', $id)->get();

        $totalIncome = $orders->where('status','paid')->sum('price');
        $paidCount = $orders->where('status','paid')->count();
        $cancelCount = $orders->where('status','cancel')->count();
        $waitingCount = $orders->where('status','waiting')->count();
        $doctorCount = $orders->groupBy('doctor_id')->count();
        $patientCount = $orders->groupBy('patient_id')->count();


        return response()->json([
            'status' => 'success',
            'data' => [
                'total_income' => $totalIncome,
                'doctor_count'=> $doctorCount,
                'patient_count'=> $patientCount,
                'paid_count' => $paidCount,
                'cancel_count' => $cancelCount,
                'waiting_count' => $waitingCount
            ]
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'service' => 'required',
            'price' => 'required',
            'duration' => 'required',
            'clinic_id' => 'required',
            'schedule' => 'required',
        ]);

        $data = $request->all();

        $order = Order::create($data);
        //XENDIT_SERVER_KEY
        Configuration::setXenditKey(env('XENDIT_SECRET_KEY',''));

        $apiInstance = new InvoiceApi();
        $create_invoice_request = new CreateInvoiceRequest([
            'external_id' => 'INV-' . $order->id,
            'description' => 'Payment for ' . $order->service,
            'amount' => $order->price,
            'invoice_duration' => 172800,
            'currency' => 'IDR',
            'reminder_time' => 1,
            'success_redirect_url' => 'flutter/success',
            'failure_redirect_url' => 'flutter/failure',
        ]);


        try {
            $result = $apiInstance->createInvoice($create_invoice_request);
            $payment_url = $result->getInvoiceUrl();
            $order->payment_url = $payment_url;
            $order->save();

            return response()->json([
                'status' => 'success',
                'data' => $order
            ], 201);
        } catch (\Xendit\XenditSdkException $e) {
            return response()->json([
                'status' => 'success',
                'data' => 'Exception when calling InvoiceApi->createInvoice: '. $e->getMessage()
            ], 500);

        }
    }


    public function handleCallback(Request $request)
    {
        //check header 'x-callback-token
        $xenditCallbackToken = env('XENDIT_CALLBACK_TOKEN', '');
        $callbackToken = $request->header('x-callback-token');
        if ($callbackToken != 'LiiQmaqlrSDE3K8SbujkxQ0Hx3n77gCWKcP1B0ACO88zVF9f') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $data = $request->all();
        $externalId = $data['external_id'];
        $order = Order::where('id', explode('-', $externalId)[1])->first();
        $order->status = $data['status'];
        $order->status_service = 'Active';
        $order->save();


        if ($data['status'] == 'Success') {
            //kirim notifikasi ke doctor
        }

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }
}
