<?php

namespace App\Http\Controllers;

use App\Services\InvitationStorage;
use App\Services\InvitationTemplateRenderer;
use Illuminate\Http\Request;

class InvitationPublicController extends Controller
{
    public function __construct(
        protected InvitationStorage $storage,
        protected InvitationTemplateRenderer $renderer
    ) {
    }

    public function show(string $slug)
    {
        $undangan = $this->storage->findBySlug($slug);
        abort_if(! $undangan, 404);
        abort_if(($undangan['status'] ?? '') !== 'aktif', 404);

        $this->storage->incrementViews($slug);
        $undangan = $this->storage->findBySlug($slug);

        $html = $this->renderer->render($undangan);

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function storeUcapan(Request $request, string $slug)
    {
        $undangan = $this->storage->findBySlug($slug);
        abort_if(! $undangan || ($undangan['status'] ?? '') !== 'aktif', 404);

        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'ucapan' => 'required|string|max:500',
            'kehadiran' => 'required|in:hadir,tidak_hadir',
        ]);

        $this->storage->addUcapan($slug, $data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => 'Ucapan tersimpan.']);
        }

        return back()->with('success', 'Terima kasih! Ucapanmu sudah tersimpan.');
    }
}
