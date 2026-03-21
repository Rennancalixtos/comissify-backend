<?php

namespace App\Livewire\Admin;

use App\Models\SystemPrice;
use App\Models\TokenPackage;
use App\Models\TokenTransaction;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class Tokens extends Component
{
    use WithPagination;

    public bool   $showPackageModal = false;
    public ?int   $editingPackageId = null;
    public string $pkgName          = '';
    public float  $pkgTokens        = 0;
    public float  $pkgPrice         = 0;
    public bool   $pkgActive        = true;
    public bool   $pkgFeatured      = false;

    public float  $priceSearch    = 0;
    public float  $priceLink      = 0;
    public float  $pricePostText  = 0;
    public float  $pricePostImage = 0;
    public float  $pricePostVideo = 0;
    public float  $pricePostPin        = 0;
    public float  $pricePostTg         = 0;
    public float  $pricePostTiktok     = 0;
    public float  $pricePostTiktokShop = 0;
    public float  $pricePostFbAd       = 0;

    public bool   $showAddTokensModal = false;
    public ?int   $addTokensUserId    = null;
    public float  $addTokensAmount    = 0;
    public string $addTokensDesc      = '';

    public bool   $showTransactionsModal = false;
    public ?int   $transactionsUserId    = null;

    public function mount(): void
    {
        $this->priceSearch    = SystemPrice::get('search_cost');
        $this->priceLink      = SystemPrice::get('link_generation_cost');
        $this->pricePostText  = SystemPrice::get('post_text_cost');
        $this->pricePostImage = SystemPrice::get('post_image_cost');
        $this->pricePostVideo = SystemPrice::get('post_video_cost');
        $this->pricePostPin        = SystemPrice::get('post_pinterest_cost');
        $this->pricePostTg         = SystemPrice::get('post_telegram_cost');
        $this->pricePostTiktok     = SystemPrice::get('post_tiktok_cost');
        $this->pricePostTiktokShop = SystemPrice::get('post_tiktok_shop_cost');
        $this->pricePostFbAd       = SystemPrice::get('post_fb_adlibrary_cost');
    }


    public function openNewPackage(): void
    {
        $this->resetPackageForm();
        $this->showPackageModal = true;
    }

    public function openEditPackage(int $id): void
    {
        $pkg = TokenPackage::findOrFail($id);
        $this->editingPackageId = $id;
        $this->pkgName          = $pkg->name;
        $this->pkgTokens        = (float) $pkg->tokens_amount;
        $this->pkgPrice         = (float) $pkg->price;
        $this->pkgActive        = $pkg->is_active;
        $this->pkgFeatured      = $pkg->is_featured;
        $this->showPackageModal = true;
    }

    public function savePackage(): void
    {
        $this->validate([
            'pkgName'    => 'required|string|max:100',
            'pkgTokens'  => 'required|numeric|min:0.01',
            'pkgPrice'   => 'required|numeric|min:0',
        ]);

        $data = [
            'name'          => $this->pkgName,
            'tokens_amount' => $this->pkgTokens,
            'price'         => $this->pkgPrice,
            'is_active'     => $this->pkgActive,
            'is_featured'   => $this->pkgFeatured,
        ];

        if ($this->editingPackageId) {
            TokenPackage::findOrFail($this->editingPackageId)->update($data);
        } else {
            TokenPackage::create($data);
        }

        $this->showPackageModal = false;
        $this->resetPackageForm();
    }

    public function deletePackage(int $id): void
    {
        TokenPackage::findOrFail($id)->delete();
    }

    private function resetPackageForm(): void
    {
        $this->editingPackageId = null;
        $this->pkgName          = '';
        $this->pkgTokens        = 0;
        $this->pkgPrice         = 0;
        $this->pkgActive        = true;
        $this->pkgFeatured      = false;
    }


    public function savePrices(): void
    {
        $this->validate([
            'priceSearch'    => 'required|numeric|min:0',
            'priceLink'      => 'required|numeric|min:0',
            'pricePostText'  => 'required|numeric|min:0',
            'pricePostImage' => 'required|numeric|min:0',
            'pricePostVideo' => 'required|numeric|min:0',
            'pricePostPin'        => 'required|numeric|min:0',
            'pricePostTg'         => 'required|numeric|min:0',
            'pricePostTiktok'     => 'required|numeric|min:0',
            'pricePostTiktokShop' => 'required|numeric|min:0',
            'pricePostFbAd'       => 'required|numeric|min:0',
        ]);

        SystemPrice::set('search_cost',           $this->priceSearch);
        SystemPrice::set('link_generation_cost',  $this->priceLink);
        SystemPrice::set('post_text_cost',        $this->pricePostText);
        SystemPrice::set('post_image_cost',       $this->pricePostImage);
        SystemPrice::set('post_video_cost',       $this->pricePostVideo);
        SystemPrice::set('post_pinterest_cost',   $this->pricePostPin);
        SystemPrice::set('post_telegram_cost',    $this->pricePostTg);
        SystemPrice::set('post_tiktok_cost',      $this->pricePostTiktok);
        SystemPrice::set('post_tiktok_shop_cost', $this->pricePostTiktokShop);
        SystemPrice::set('post_fb_adlibrary_cost',$this->pricePostFbAd);

        session()->flash('price_saved', 'Preços atualizados com sucesso.');
    }


    public function openAddTokens(int $userId): void
    {
        $this->addTokensUserId = $userId;
        $this->addTokensAmount = 0;
        $this->addTokensDesc   = 'Crédito manual pelo admin';
        $this->showAddTokensModal = true;
    }

    public function confirmAddTokens(): void
    {
        $this->validate([
            'addTokensAmount' => 'required|numeric|min:0.0001',
            'addTokensDesc'   => 'required|string|max:255',
        ]);

        $user = User::findOrFail($this->addTokensUserId);
        $user->ensureTokenWallet()->credit($this->addTokensAmount, $this->addTokensDesc);

        $this->showAddTokensModal = false;
    }


    public function openTransactions(int $userId): void
    {
        $this->transactionsUserId  = $userId;
        $this->showTransactionsModal = true;
    }

    public function render()
    {
        $packages     = TokenPackage::latest()->get();
        $users        = User::where('is_admin', false)
            ->with('token')
            ->latest()
            ->paginate(15);

        $transactions = $this->transactionsUserId
            ? TokenTransaction::where('user_id', $this->transactionsUserId)->latest()->take(50)->get()
            : collect();

        $transactionUser = $this->transactionsUserId
            ? User::find($this->transactionsUserId)
            : null;

        return view('livewire.admin.tokens', compact('packages', 'users', 'transactions', 'transactionUser'))
            ->layout('layouts.admin', ['title' => 'Gestão de Tokens']);
    }
}
