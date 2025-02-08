<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DpoService;

class PaymentsController extends Controller
{
    protected $dpoService;

    public function __construct(DpoService $dpoService)
    {
        // Pass the order ID to the DpoService constructor
        $order_id = "12345678"; // You can also fetch this from a config or database
        $this->dpoService = $dpoService;
    }

    public function submitPayment(Request $request)
    {
        // Validate incoming request data
        $data = $request->validate([
            'payment_option' => 'required|string',
            'amount' => 'required|numeric',
            'phone_num' => 'required_if:payment_option,mobile_money|string',
            'network' => 'required_if:payment_option,mobile_money|string',
        ]);

        // Determine mobile network based on the selected option
        $mno = $data['network'] === 'mtn' ? "MTNZM" : "AirtelZM";
        $phone_number = substr($data['phone_num'], 1); // Remove leading '+' from phone number

        // Call the chargeTokenMobileMoney method to process payment
        $payment = $this->dpoService->chargeTokenMobileMoney($mno, $phone_number, $data['amount'], "ZM", "ZMW");

        // Check if payment was successful
        if ($payment['isSuccess']) {
            return response()->json(['token' => $payment['token'], 'message' => 'Enter PIN to complete transaction.']);
        }

        // If payment failed, return error message
        return response()->json(['error' => 'Transaction failed. Please try again.'], 400);
    }

    public function verifyTransaction(Request $request)
    {
        // Retrieve the token from the query parameters
        $token = $request->query('token');

        if ($token) {
            // Call the verifyTrans method on the dpoPay service
            $verify = $this->dpoService->verifyTrans($token);

            // Check the transaction status and set the status message
            if ($verify['tran_status'] == "pending") {
                $status = "pending";
            } elseif ($verify['tran_status'] == "success") {
                $status = "success";
            } elseif ($verify['tran_status'] == "rejected") {
                $status = "rejected";
            } else {
                $status = "unknown"; // Add an unknown case if needed
            }

            // Return the status as a JSON response
            return response()->json(['status' => $status]);
        }

        // If token is not provided, return an error response
        return response()->json(['error' => 'Token is required'], 400);
    }
}
