<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CampaignRepository;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class CampaignController extends Controller
{
    public function __construct(
        protected CampaignRepository $campaigns,
        protected CloudinaryService $cloudinary
    ) {
    }

    public function index()
    {
        return view('admin.campaign.index', [
            'campaign' => $this->campaigns->get(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'aktif' => 'nullable|boolean',
            'image' => 'nullable|file|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        $current = $this->campaigns->get();
        $aktif = $request->boolean('aktif');
        $imageUrl = $current['image_url'];
        $publicId = $current['cloudinary_public_id'];

        try {
            if ($request->boolean('remove_image')) {
                $this->cloudinary->deleteImage($publicId);
                $imageUrl = null;
                $publicId = null;
            } elseif ($request->hasFile('image')) {
                if (! $this->cloudinary->isConfigured()) {
                    return back()->with('error', 'Cloudinary belum dikonfigurasi. Isi CLOUDINARY_* di file .env');
                }

                $uploaded = $this->cloudinary->uploadImage($request->file('image'));
                if ($current['cloudinary_public_id']) {
                    $this->cloudinary->deleteImage($current['cloudinary_public_id']);
                }
                $imageUrl = $uploaded['url'];
                $publicId = $uploaded['public_id'];
            }
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Gagal simpan campaign: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            $msg = 'Gagal menyimpan campaign. Coba lagi.';
            if (config('app.debug')) {
                $msg .= ' ('.$e->getMessage().')';
            }

            return back()->with('error', $msg);
        }

        if ($aktif && ! filled($imageUrl)) {
            return back()->with('error', 'Upload gambar dulu sebelum mengaktifkan campaign.');
        }

        $this->campaigns->save([
            'aktif' => $aktif,
            'image_url' => $imageUrl,
            'cloudinary_public_id' => $publicId,
        ]);

        return redirect()
            ->route('admin.campaign.index')
            ->with('success', $aktif ? 'Campaign aktif dan akan tampil di landing page.' : 'Campaign disimpan (nonaktif).');
    }
}
