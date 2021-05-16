<?php

namespace App\Http\Livewire;

use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\DataTable\WithShorting;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    use WithShorting, WithBulkActions, WithPerPagePagination, WithCachedRows;

    public Transaction $editing;
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $showFilters = false;
 
    public $filters = [
        'search' => '',
        'status' => '',
        'amount-min' => null,
        'amount-max' => null,
        'date-min' => null,
        'date-max' => null
    ];

    protected $queryString = [];

    protected $listeners = ['refreshTransactions' => '$refresh'];

    public function rules()
    {
        return [
            'editing.title' => 'required|min:3',
            'editing.amount' => 'required|min:3',
            'editing.status' => 'required|in:'.collect(Transaction::STATUSES)->keys()->implode(','),
            'editing.date_for_editing' => 'required|min:3',
        ];
    }

    public function mount()
    {
        $this->editing = $this->makeBlankTransaction();
    }

    public function exportSelected()
    {
        return response()->streamDownload(function () {
            echo $this->selectedRowsQuery->toCsv();
        }, 'transactions.cvs');
    }

    public function deleteSelected()
    {
        $this->selectedRowsQuery->delete();

        $this->showDeleteModal = false;
    }

    public function makeBlankTransaction()
    {
        return Transaction::make(['date' => now(), 'status' => 'success']);
    }

    public function toggleShowFilters()
    {
        $this->useCachedRows();

        $this->showFilters = ! $this->showFilters;
    }
    
    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function create()
    {
        $this->useCachedRows();
    
        if ($this->editing->getkey()) {
            $this->editing = $this->makeBlankTransaction();
        }

        $this->showEditModal = true;
    }

    public function edit(Transaction $transaction)
    {
        $this->useCachedRows();

        if ($this->editing->isNot($transaction)) {
            $this->editing = $transaction;
        }

        $this->showEditModal = true;
    }

    public function save()
    {
        $this->validate();

        $this->editing->save();

        $this->showEditModal = false;
    }

    public function getRowsQueryProperty()
    {
        $query = Transaction::query()
                ->when($this->filters['status'], fn ($query, $status) => $query->where('status', $status))
                ->when($this->filters['amount-min'], fn ($query, $amount) => $query->where('amount', '>=', $amount))
                ->when($this->filters['amount-max'], fn ($query, $amount) => $query->where('amount', '<=', $amount))
                ->when($this->filters['date-min'], fn ($query, $date) => $query->where('date', '>=', Carbon::parse($date)))
                ->when($this->filters['date-max'], fn ($query, $date) => $query->where('date', '<=', Carbon::parse($date)))
                ->when($this->filters['search'], fn ($query, $search) => $query->where('title', 'like', '%'.$search.'%'));

        return $this->applySorting($query);
    }

    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->applyPagination($this->rowsQuery);
        });
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'transactions' => $this->rows
        ]);
    }
}
