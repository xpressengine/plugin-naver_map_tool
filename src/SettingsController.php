<?php
/**
 * Created by PhpStorm.
 * User: seungman
 * Date: 2016. 9. 22.
 * Time: 오후 5:35
 */

namespace Xpressengine\Plugins\NaverMapTool;

use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Permission\PermissionSupport;
use XePresenter;
use XeConfig;

class SettingsController extends Controller
{
    use PermissionSupport;

    public function getSetting($instanceId)
    {
        $permArgs = $this->getPermArguments(NaverMapTool::getKey($instanceId), 'use');

        return XePresenter::make('naver_map_tool::views.setting', [
            'instanceId' => $instanceId,
            'permArgs' => $permArgs
        ]);
    }

    public function postSetting(Request $request, $instanceId)
    {
        $this->permissionRegister($request, NaverMapTool::getKey($instanceId), 'use');

        return redirect()->route('settings.plugin.naver_map_tool.setting', $instanceId);
    }

    public function getGlobal()
    {
        $config = XeConfig::getOrNew('naver_map_tool');

        return XePresenter::make('naver_map_tool::views.global', ['config' => $config]);
    }

    public function postGlobal(Request $request)
    {
        XeConfig::set('naver_map_tool', $request->only(['key', 'lat', 'lng', 'zoom']));

        return redirect()->back();
    }
}