<?php

namespace App\Livewire;

use App\Models\Role;
use App\Models\Waste;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MapPanel extends Component
{
    use WithPagination;

    // Properti untuk state
    public $q = '';
    public $sortBy = '';
    public $activeTab = 'search';
    public $selectedWasteId;

    // Listener untuk event dari JavaScript
    protected $listeners = ['showDetailsFromMap'];

    public function updated($propertyName)
    {
        // Jika filter diubah, reset halaman pagination
        if (in_array($propertyName, ['q', 'sortBy'])) {
            $this->resetPage();
        }
    }

    public function switchTab($tabName)
    {
        $this->activeTab = $tabName;
        // Reset detail saat beralih tab
        if ($tabName !== 'detail') {
            $this->selectedWasteId = null;
        }
    }

    public function showDetails($wasteId)
    {
        $this->selectedWasteId = $wasteId;
        $this->activeTab = 'detail';
    }

    // Listener yang dipanggil dari marker di peta
    public function showDetailsFromMap($wasteId)
    {
        $this->showDetails($wasteId);
    }

    public function render()
    {
        $wastes = Waste::query()
            ->join('stores', 'wastes.store_id', '=', 'stores.id')
            ->whereNotNull('stores.location')
            ->select(
                'wastes.id as waste_id',
                'wastes.name as waste_name',
                'wastes.price',
                'stores.name as store_name',
                DB::raw('ST_Y(ST_AsText(stores.location)) as latitude'),
                DB::raw('ST_X(ST_AsText(stores.location)) as longitude')
            )
            ->when($this->q, function ($query) {
                $query->where('wastes.name', 'ILIKE', '%' . $this->q . '%');
            })
            ->when($this->sortBy === 'price_asc', function ($query) {
                $query->orderBy('wastes.price', 'asc');
            })
            // Tambahkan sorting lain jika perlu
            ->paginate(15);
        
        // Kirim event ke JavaScript setiap kali data diperbarui
        $this->dispatch('wastesUpdated', wastes: $wastes->items());

        return view('livewire.map-panel', [
            'wastes' => $wastes,
            'roles' => Role::all(), // Untuk form registrasi di profil
            'selectedWaste' => $this->selectedWasteId ? Waste::find($this->selectedWasteId) : null,
        ]);
    }
}