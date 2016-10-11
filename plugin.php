<?php
namespace Xpressengine\Plugins\NaverMapTool;

use XeFrontend;
use XePresenter;
use Route;
use Xpressengine\Http\Request;
use Xpressengine\Plugin\AbstractPlugin;

class Plugin extends AbstractPlugin
{
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
        // implement code

        Route::fixed(
            $this->getId(),
            function () {
                Route::get(
                    '/popup/create',
                    [
                        'as' => 'naver_map_tool::popup',
                        'uses' => function (Request $request) {

                            $title = 'Naver map tool for editor';

                            // set browser title
                            XeFrontend::title($title);

                            // load css file
//                            XeFrontend::css($this->asset('assets/style.css'))->load();

                            //header, footer 제거
                            \XeTheme::selectBlankTheme();

                            $config = \XeConfig::getOrNew('naver_map_tool');

                            XeFrontend::js([
                                'https://openapi.map.naver.com/openapi/v3/maps.js?clientId=' . $config->get('key'),
                                $this->asset('assets/naverMapRenderer.js?key=' . $config->get('key'))
                            ])->appendTo('head')->load();

                            // output
                            return XePresenter::make('naver_map_tool::views.popup', ['config' => $config]);

                        }
                    ]
                );

                Route::get(
                    '/popup/edit',
                    [
                        'as' => 'naver_map_tool::popup.edit',
                        'uses' => function (Request $request) {

                            $title = 'Naver map tool for editor';

                            // set browser title
                            XeFrontend::title($title);

                            // load css file
                            XeFrontend::css($this->asset('assets/style.css'))->load();

                            //header, footer 제거
                            \XeTheme::selectBlankTheme();

                            $config = \XeConfig::getOrNew('naver_map_tool');

                            XeFrontend::js([
                                'https://openapi.map.naver.com/openapi/v3/maps.js?clientId=' . $config->get('key'),
                                $this->asset('assets/naverMapRenderer.js?key=' . $config->get('key'))
                            ])->appendTo('head')->load();

                            // output
                            return XePresenter::make('naver_map_tool::views.popup-edit', ['config' => $config]);

                        }
                    ]
                );
            }
        );

        Route::settings($this->getId(), function () {
            Route::get('setting', ['as' => 'settings.plugin.naver_map_tool.global', 'uses' => 'SettingsController@getGlobal']);
            Route::post('setting', ['as' => 'settings.plugin.naver_map_tool.global', 'uses' => 'SettingsController@postGlobal']);

            Route::get('setting/{instanceId}', ['as' => 'settings.plugin.naver_map_tool.setting', 'uses' => 'SettingsController@getSetting']);
            Route::post('setting/{instanceId}', ['as' => 'settings.plugin.naver_map_tool.setting', 'uses' => 'SettingsController@postSetting']);
        }, ['namespace' => __NAMESPACE__]);
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

        parent::activate($installedVersion);
    }

    /**
     * 플러그인을 설치한다. 플러그인이 설치될 때 실행할 코드를 여기에 작성한다
     *
     * @return void
     */
    public function install()
    {
        // implement code

        parent::install();
    }

    /**
     * 해당 플러그인이 설치된 상태라면 true, 설치되어있지 않다면 false를 반환한다.
     * 이 메소드를 구현하지 않았다면 기본적으로 설치된 상태(true)를 반환한다.
     *
     * @param string $installedVersion 이 플러그인의 현재 설치된 버전정보
     *
     * @return boolean 플러그인의 설치 유무
     */
    public function checkInstalled($installedVersion = null)
    {
        // implement code

        return parent::checkInstalled($installedVersion);
    }

    /**
     * 플러그인을 업데이트한다.
     *
     * @param string|null $installedVersion 현재 XpressEngine에 설치된 플러그인의 버전정보
     *
     * @return void
     */
    public function update($installedVersion = null)
    {
        // implement code

        parent::update($installedVersion);
    }

    /**
     * 해당 플러그인이 최신 상태로 업데이트가 된 상태라면 true, 업데이트가 필요한 상태라면 false를 반환함.
     * 이 메소드를 구현하지 않았다면 기본적으로 최신업데이트 상태임(true)을 반환함.
     *
     * @param string $currentVersion 현재 설치된 버전
     *
     * @return boolean 플러그인의 설치 유무,
     */
    public function checkUpdated($currentVersion = null)
    {
        return true;
    }

    public function getSettingsURI()
    {
        return route('settings.plugin.naver_map_tool.global');
    }
}
