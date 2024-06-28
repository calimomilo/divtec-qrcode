<?php

namespace App\Http\Controllers;

use App\Models\Qrcode;
use App\Models\Stat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class QrcodeController extends Controller
{
    /* public function __construct()
    {
        $this->middleware('auth');
    }*/

    public function showAllQrcodes()
    {
        return Qrcode::where('utilisateur_id', Auth::id())->get();
    }

    public function showOneQrcode($id)
    {
        return Qrcode::findOrFail($id);
    }

    public function createQrcode(Request $request) // Il faudrait générer le code_id et l'image automatiquement
    {
        $this->validate($request, Qrcode::validateRules());
        $data = $request->all();
        $data['utilisateur_id'] = Auth::id();
        return Qrcode::create($data);
    }

    public function updateQrcode(Request $request, $id)
    {
        $this->validate($request, Qrcode::validateRules());
        Qrcode::findOrFail($id)->update($request->all());
        return Qrcode::findOrFail($id);
    }

    public function deleteQrcode($id)
    {
        Qrcode::findOrFail($id)->delete();
        return response('', 204);
    }

    public function countStats($id)
    {
        Qrcode::findOrFail($id);
        return Stat::where('qrcode_id', $id)->count();
    }

    // scan retourne l'url a appeler
    public function scanQrcode($code_id)
    {
        $qrcode = Qrcode::where('code_id', $code_id);

        if ($qrcode->doesntexist())
            return response()->json(['message' => "qrcode invalide"], 404);

        Stat::create(['qrcode_id' => $qrcode->value('id')]);
        
        header('Location: ' . $qrcode->value('lien_redir'));
        exit;

    }

}
