<?php

namespace App\Http\Middleware;

use App\Models\Qrcode;
use App\Models\Tache;
use Closure;
use Illuminate\Support\Facades\Auth;

class MustBeOwnerOfQrcode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Récupération de la tâche
        $qrcode = Qrcode::find($request->id);

        // Si PAS propriétaire de la tâche
        if($qrcode AND $qrcode->utilisateur_id != Auth::user()->id)
            return response()->json(['message' => "Not the owner"], 401);

        return $next($request);
    }
}
