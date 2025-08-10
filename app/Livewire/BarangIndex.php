<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Barang;
use App\Models\TransaksiDetail;

class BarangIndex extends Component
{
    use WithPagination;

    public $nama = '';
    public $harga = '';
    public $stok = '';
    public $successMessage = '';
    public $isEdit = false;
    public $editId = null;
    public $search = '';
    public $showModal = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModalTambah()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate([
            'nama'  => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok'  => 'required|integer|min:0',
        ]);

        if ($this->isEdit && $this->editId) {
            $barang = Barang::findOrFail($this->editId);
            $barang->update([
                'nama'  => $this->nama,
                'harga' => $this->harga,
                'stok'  => $this->stok,
            ]);
            $this->successMessage = 'Barang berhasil diperbarui.';
        } else {
            Barang::create([
                'nama'  => $this->nama,
                'harga' => $this->harga,
                'stok'  => $this->stok,
            ]);
            $this->successMessage = 'Barang berhasil ditambahkan.';
        }

        $this->resetForm();
        $this->closeModal();
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $this->editId = $barang->id;
        $this->nama   = $barang->nama;
        $this->harga  = $barang->harga;
        $this->stok   = $barang->stok;
        $this->isEdit = true;
        $this->showModal = true;
        $this->successMessage = '';
    }

    public function delete($id)
    {
        $barang = Barang::findOrFail($id);

        // Cek apakah barang digunakan di Transaksi Detail
        $dipakai = TransaksiDetail::where('barang_id', $barang->id)->exists();
        if ($dipakai) {
            $this->successMessage = "Barang tidak dapat dihapus karena sedang digunakan di transaksi.";
            return;
        }

        $barang->delete();
        $this->successMessage = "Barang berhasil dihapus.";
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->nama = '';
        $this->harga = '';
        $this->stok = '';
        $this->isEdit = false;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $listBarang = Barang::where('nama', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(5);

        return view('livewire.barang-index', [
            'listBarang' => $listBarang,
        ]);
    }
}
