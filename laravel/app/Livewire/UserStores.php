<?php
namespace App\Livewire;

use App\Models\Store;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class UserStores extends Component
{
    public $stores;
    public $showStoreModal = false;
    public $editableStore; // Berisi data toko yang akan diedit
    public $confirmingStoreDeletion = false;
    public $storeIdToDelete;

    protected function rules()
    {
        return [
            'editableStore.name' => 'required|string|max:255',
            'editableStore.address' => 'required|string',
            // Tambahkan validasi lain jika ada, misal location, image_path, dll.
        ];
    }

    public function mount()
    {
        $this->loadStores();
    }

    public function loadStores()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && $user->hasRole('seller')) {
            $this->stores = Store::where('user_id', $user->id)->get();
        } else {
            $this->stores = collect(); // Kosongkan jika bukan seller
        }
    }

    public function create()
    {
        $this->resetErrorBag();
        $this->editableStore = new Store();
        $this->showStoreModal = true;
    }

    public function edit($storeId)
    {
        $this->resetErrorBag();
        $this->editableStore = Store::find($storeId);
        if ($this->editableStore && $this->editableStore->user_id === Auth::id()) {
            $this->showStoreModal = true;
        }
    }

    public function saveStore()
    {
        $this->validate();

        if (!Auth::check()) return;

        $this->editableStore->user_id = Auth::id();

        try {
            $this->editableStore->save();
            $this->showStoreModal = false;
            $this->loadStores();
            session()->flash('message', 'Toko berhasil disimpan!');
        } catch (\Exception $e) {
            Log::error("Gagal simpan toko: " . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan toko.');
        }
    }

    public function confirmStoreDeletion($storeId)
    {
        $this->storeIdToDelete = $storeId;
        $this->confirmingStoreDeletion = true;
    }

    public function deleteStore()
    {
        $store = Store::find($this->storeIdToDelete);
        if ($store && $store->user_id === Auth::id()) {
            try {
                $store->delete();
                $this->loadStores();
                session()->flash('message', 'Toko berhasil dihapus!');
            } catch (\Exception $e) {
                Log::error("Gagal hapus toko: " . $e->getMessage());
                session()->flash('error', 'Gagal menghapus toko.');
            }
        }
        $this->confirmingStoreDeletion = false;
    }

    public function render()
    {
        return view('livewire.user-stores');
    }
}