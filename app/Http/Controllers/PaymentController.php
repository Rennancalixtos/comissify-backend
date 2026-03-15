<?php

namespace App\Http\Controllers;

use App\Models\PaymentOrder;
use App\Models\TokenPackage;
use App\Services\MercadoPagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $request->validate(['package_id' => 'required|integer|exists:token_packages,id']);

        // Preço sempre vem do banco — nunca do request
        $package = TokenPackage::where('id', $request->package_id)
            ->where('is_active', true)
            ->firstOrFail();

        $order = PaymentOrder::create([
            'user_id'       => Auth::id(),
            'package_id'    => $package->id,
            'amount'        => $package->price,
            'tokens_amount' => $package->tokens_amount,
            'status'        => 'pending',
        ]);

        ['preference_id' => $preferenceId] = app(MercadoPagoService::class)->createPreference($order);

        $order->update(['mp_preference_id' => $preferenceId]);

        return response()->json(['preference_id' => $preferenceId]);
    }

    public function success(Request $request): RedirectResponse
    {
        // MP redireciona com ?payment_id=...&status=approved&external_reference=...
        // O crédito é feito pelo webhook — aqui só mostramos feedback visual
        $status = $request->query('status');

        if ($status === 'approved') {
            return redirect()->route('credits')
                ->with('success', 'Pagamento aprovado! Seus créditos serão creditados em instantes.');
        }

        return redirect()->route('credits')
            ->with('info', 'Pagamento em processamento. Você será notificado quando confirmado.');
    }

    public function failure(): RedirectResponse
    {
        return redirect()->route('credits')
            ->with('error', 'Pagamento não concluído. Tente novamente ou escolha outro método de pagamento.');
    }

    public function pending(): RedirectResponse
    {
        return redirect()->route('credits')
            ->with('info', 'Pagamento pendente. Assim que confirmado, seus créditos serão adicionados automaticamente.');
    }
}
