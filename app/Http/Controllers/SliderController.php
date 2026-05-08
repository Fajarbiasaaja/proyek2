<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;

/**
 * SliderController
 * 
 * Controller untuk CRUD management slider/carousel images di dashboard.
 * Admin dapat upload, edit, delete, dan manage sliders dari sini.
 * 
 * Routes:
 * - GET /sliders -> index() - List semua sliders dengan pagination
 * - GET /sliders/create -> create() - Show form create
 * - POST /sliders -> store() - Store slider baru
 * - GET /sliders/{id}/edit -> edit() - Show form edit
 * - PUT /sliders/{id} -> update() - Update slider
 * - DELETE /sliders/{id} -> destroy() - Delete slider
 * - POST /sliders/{id}/toggle-active -> toggleActive() - Toggle status
 */
class SliderController extends Controller
{
    /**
     * Display listing of sliders dengan pagination
     * 
     * Menampilkan semua slider dalam table format dengan:
     * - Image preview
     * - Title dan description
     * - Sort order badge
     * - Status (active/inactive)
     * - Action buttons (edit, delete, toggle)
     * 
     * Pagination: 10 items per page
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get all sliders sorted by sort_order dengan pagination
        $sliders = Slider::orderBy('sort_order', 'asc')->paginate(10);
        
        return view('admin.sliders.index', compact('sliders'));
    }

    /**
     * Show form untuk create slider baru
     * 
     * Menampilkan form dengan fields:
     * - Image upload (required)
     * - Title (optional)
     * - Description (optional)
     * - Button text & link (optional CTA button)
     * - Sort order (untuk atur urutan display)
     * - Active checkbox (untuk activate/deactivate)
     * 
     * Termasuk tips dan preview untuk image sebelum upload.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.sliders.create');
    }

    /**
     * Store slider baru di database
     * 
     * Flow:
     * 1. Validasi input (image required, mimes, max 5MB)
     * 2. Store image ke storage/public/sliders/
     * 3. Simpan slider data ke database
     * 4. Redirect ke index dengan success message
     * 
     * Image Storage:
     * - Path: storage/app/public/sliders/{filename}
     * - Accessible via: /storage/sliders/{filename}
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',           // Judul (optional)
            'description' => 'nullable|string',              // Deskripsi (optional)
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Image REQUIRED
            'button_text' => 'nullable|string|max:50',      // CTA button text (optional)
            'button_link' => 'nullable|url',                // CTA button link (optional)
            'sort_order' => 'nullable|integer|min:0',       // Urutan (optional, default 0)
            'is_active' => 'nullable|boolean',              // Status (optional, default true)
        ]);

        // Handle image upload ke storage
        if ($request->hasFile('image')) {
            // Store image di storage/app/public/sliders/
            // Returns relative path: sliders/xxxxx.jpg
            $imagePath = $request->file('image')->store('sliders', 'public');
            $validated['image'] = $imagePath;
        }

        // Handle is_active checkbox (unchecked = false, checked = true)
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        // Set default sort_order jika tidak diisi
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Create slider di database
        Slider::create($validated);

        return redirect()->route('sliders.index')
            ->with('success', 'Slider berhasil ditambahkan!');
    }

    /**
     * Show form untuk edit slider existing
     * 
     * Menampilkan form dengan data slider yang sudah ada,
     * plus preview image saat ini.
     * 
     * User dapat:
     * - Update semua fields
     * - Upload image baru (atau keep existing)
     * - Ubah sort order
     * - Toggle active status
     * 
     * @param Slider $slider Slider yang mau diedit (route model binding)
     * @return \Illuminate\View\View
     */
    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    /**
     * Update slider di database
     * 
     * Flow:
     * 1. Validasi input
     * 2. Jika ada image baru:
     *    a. Hapus old image dari storage
     *    b. Upload new image
     * 3. Update slider record
     * 4. Redirect dengan success message
     * 
     * @param Request $request
     * @param Slider $slider Slider yang mau diupdate (route model binding)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Slider $slider)
    {
        // Validasi input (image now optional for update)
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Image optional
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload jika ada file baru
        if ($request->hasFile('image')) {
            // Delete old image file from storage
            if ($slider->image && file_exists(public_path('storage/' . $slider->image))) {
                unlink(public_path('storage/' . $slider->image));
            }

            // Upload dan store new image
            $imagePath = $request->file('image')->store('sliders', 'public');
            $validated['image'] = $imagePath;
        }

        // Handle is_active checkbox
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        // Preserve old sort_order jika tidak diubah
        $validated['sort_order'] = $validated['sort_order'] ?? $slider->sort_order;

        // Update slider
        $slider->update($validated);

        return redirect()->route('sliders.index')
            ->with('success', 'Slider berhasil diperbarui!');
    }

    /**
     * Delete slider dan image-nya
     * 
     * Flow:
     * 1. Delete image file dari storage
     * 2. Delete slider record dari database
     * 3. Redirect dengan success message
     * 
     * @param Slider $slider Slider yang mau dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Slider $slider)
    {
        // Delete image file from storage jika ada
        if ($slider->image && file_exists(public_path('storage/' . $slider->image))) {
            unlink(public_path('storage/' . $slider->image));
        }

        // Delete slider record dari database
        $slider->delete();

        return redirect()->route('sliders.index')
            ->with('success', 'Slider berhasil dihapus!');
    }

    /**
     * Toggle slider active status on/off
     * 
     * Mengubah status is_active dari true ke false atau sebaliknya.
     * Gunakan untuk quick enable/disable slider tanpa harus edit form.
     * 
     * Slider yang not active tidak akan ditampilkan di dashboard.
     * 
     * @param Slider $slider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(Slider $slider)
    {
        // Toggle is_active (true -> false, false -> true)
        $slider->update(['is_active' => !$slider->is_active]);

        return back()->with('success', 'Status slider berhasil diubah!');
    }
}
