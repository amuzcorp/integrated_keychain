<?php
namespace Amuz\XePlugin\IntegratedKeychain;

use Route;
use XeInterception;
use Amuz\XePlugin\IntegratedKeychain\IntegratedKeychainService;
use Xpressengine\Plugin\AbstractPlugin;

class Plugin extends AbstractPlugin
{
    public function register(){
        $app = app();

        // DynamicFactoryService
        $app->singleton(IntegratedKeychainService::class, function () {
            $proxyHandler = XeInterception::proxy(IntegratedKeychainService::class);

            return new $proxyHandler();
        });
        $app->alias(IntegratedKeychainService::class, 'amuz.keychain');
    }
    /**
     * 이 메소드는 활성화(activate) 된 플러그인이 부트될 때 항상 실행됩니다.
     *
     * @return void
     */
    public function boot()
    {
        // implement code

        $this->route();
    }

    protected function route()
    {
        Route::settings(self::getId(), function () {
            Route::get('/', ['as' => 'manage.integrated_keychain.index', 'uses' => 'Controller@index', 'settings_menu' => 'setting.integrated_keychain']);
            Route::post('/', ['as' => 'manage.integrated_keychain.updateConfig', 'uses' => 'Controller@updateConfig']);

        }, ['namespace' => 'Amuz\XePlugin\IntegratedKeychain']);

        \XeRegister::push('settings/menu', 'setting.integrated_keychain', [
            'title' => '통합 키체인',
            'description' => '서드파티 플러그인의 API키를 통합관리하도록 도와줍니다.',
            'display' => true,
            'ordering' => 500
        ]);
    }

    /**
     * 플러그인이 활성화될 때 실행할 코드를 여기에 작성한다.
     *
     * @param string|null $installedVersion 현재 XpressEngine에 설치된 플러그인의 버전정보
     *
     * @return void
     */
    public function activate($installedVersion = null)
    {
        // implement code
    }

    /**
     * 플러그인을 설치한다. 플러그인이 설치될 때 실행할 코드를 여기에 작성한다
     *
     * @return void
     */
    public function install()
    {
        // implement code
    }

    /**
     * 해당 플러그인이 설치된 상태라면 true, 설치되어있지 않다면 false를 반환한다.
     * 이 메소드를 구현하지 않았다면 기본적으로 설치된 상태(true)를 반환한다.
     *
     * @return boolean 플러그인의 설치 유무
     */
    public function checkInstalled()
    {
        // implement code

        return parent::checkInstalled();
    }

    /**
     * 플러그인을 업데이트한다.
     *
     * @return void
     */
    public function update()
    {
        // implement code
    }

    /**
     * 해당 플러그인이 최신 상태로 업데이트가 된 상태라면 true, 업데이트가 필요한 상태라면 false를 반환함.
     * 이 메소드를 구현하지 않았다면 기본적으로 최신업데이트 상태임(true)을 반환함.
     *
     * @return boolean 플러그인의 설치 유무,
     */
    public function checkUpdated()
    {
        // implement code

        return parent::checkUpdated();
    }

    public function getSettingsURI()
    {
        return route('manage.integrated_keychain.index');
    }
}
