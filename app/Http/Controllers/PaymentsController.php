<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatuses;
use App\Events\PaymentCompleted;
use App\Models\Payment;
use App\Utils\Telegram;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentsController extends Controller
{
    public function store(Request $request)
    {
        $user = \Auth::user();
        $plan = config("credits.plans.{$request->get("plan")}");
        // TODO: No plan/not exists

        $payment = new Payment();
        $payment->user()->associate($user);
        $payment->status = PaymentStatuses::Initial;
        $payment->price = $plan['cost'];
        $payment->credits = $plan['amount'];
        $data = [
            "seller_payme_id"   => config("services.payme.api_key"),
            'product_name'      => 'טעינת קרדיטים',
            'sale_price'        => $payment->price * 100,
            'currency'          => 'ILS',
//            'transaction_id'    => $payment->uuid,
            'sale_callback_url' => url("/api/payments/callbacks"),
        ];
        if ($user->payment_token) {
            $response = Http::asJson()->post(config("services.payme.url")."/api/generate-sale", $data + [
                'buyer_key' => $user->payment_token,
            ])->throw();

            $payment->status = PaymentStatuses::Succeed;
            $payment->provider_id = $response->json("payme_sale_id");
            $payment->save();

            event(new PaymentCompleted($payment));

            return \redirect("/credits");
        }

        $response = Http::asJson()->post(config("services.payme.url")."/api/generate-sale", $data + [
            'sale_return_url' => url("/payments/redirect"),
            'capture_buyer' => 1,
        ])->throw();

        Log::debug("[PaymentsController][store] Got response ", $response->json());

        $payment->provider_id = $response->json("payme_sale_id");
        $payment->save();

        $name = explode(" ", $user->name, 2);
        $name[1] ??= '';

        return \redirect($response->json("sale_url")."?first_name={$name[0]}&last_name={$name[1]}&email={$user->email}");
    }

    public function redirect(Request $request)
    {
        Log::info("[PaymentsController][redirect] Got redirect ", $request->query());
        $payment = Payment::query()->where('provider_id', $request->get("payme_sale_id"))->firstOrFail();

        if ($request->get("payme_status") != "success") {
            // Payme redirects only in success
            return response()->json();
        }

        return view('thank-you', [
            "payment" => $payment,
            "data" => $request->query()
        ]);
    }

    public function callbacks(Request $request)
    {
        Log::info("[PaymentsController][callbacks] Got callback", $request->post());

        $payment = Payment::query()->where('provider_id', $request->post("payme_sale_id"))->firstOrFail();

        // TODO: get-sale make sure this is not hack
        switch ($request->post("sale_status")) {
            case "refunded":
            case "partial-refund":
            case "chargeback":
            case "partial-chargeback":
                $message = "[PaymentsController][callbacks] Got '{$request->post("sale_status")}' From payment provider for {$payment->provider_id}";
                Log::critical($message);
                (new Telegram())->send($message);
            case "initial":
                return response()->json();
        }

        if ($payment->status == PaymentStatuses::Succeed) {
            Log::debug("[PaymentsController][callbacks] return already Succeed");

            return response()->json();
        }

        if ($request->post("sale_status") != "completed") {
            $payment->status = PaymentStatuses::Failed;
            $payment->message = $request->post("status_error_details");
            $payment->save();

            return response()->json();
        }
        $payment->status = PaymentStatuses::Succeed;
        $payment->save();

        if ($buyerKey = $request->post("buyer_key")) {
            $payment->user->payment_token = $buyerKey;
            $payment->user->save();
        }

        event(new PaymentCompleted($payment));

        return response()->json([]);
    }
}
