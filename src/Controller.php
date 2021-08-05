<?php
namespace Amuz\XePlugin\IntegratedKeychain;

use XeFrontend;
use XePresenter;
use Xpressengine\Http\Request;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    private $keychainConfig;
    private $IKService;

    public function __construct(){
        $this->IKService = app('amuz.keychain');
        $this->keychainConfig = $this->IKService->getConfig();
    }

    public function index()
    {
        $title = '통합 키체인';

        // set browser title
        XeFrontend::title($title);

        // load css file
        XeFrontend::css(Plugin::asset('assets/style.css'))->load();

        $unique_keys = $this->IKService->getUniqueKeys();
        if($unique_keys == null){
            $message = '등록요청된 키가 없습니다.';
            return XePresenter::make('integrated_keychain::views.blank', ['message' => $message]);
        }

        return XePresenter::make('integrated_keychain::views.index', [
            'key_chains' => $this->IKService->getKeyChains(),
            'unique_keys' => $unique_keys,
            'config' => $this->keychainConfig,
        ]);
    }

    public function updateConfig(Request $request){
        $unique_keys = $this->IKService->getUniqueKeys();
        if($unique_keys == null) return redirect()->back()->with('alert', ['type' => 'failed', 'message' => '키 등록이 정상적이지 않습니다.']);

        $configs = [];
        foreach($unique_keys as $key_id => $unique_key) $configs[$key_id] = $request->get($key_id);

        $this->keychainConfig->set('keychain',$configs);
        \app('xe.config')->modify($this->keychainConfig);

        return redirect()->route('manage.integrated_keychain.index')->with(
            ['alert' => ['type' => 'success', 'message' => xe_trans('xe::saved')]]
        );
    }
}
