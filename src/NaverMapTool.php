<?php
/**
 * NaverMapTool.php
 *
 * PHP version 7
 *
 * @category    NaverMapTool
 * @package     Xpressengine\Plugins\NaverMapTool
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Plugins\NaverMapTool;

use App\Facades\XeFrontend;
use Illuminate\Contracts\Auth\Access\Gate;
use Symfony\Component\DomCrawler\Crawler;
use Xpressengine\Config\ConfigManager;
use Xpressengine\Editor\AbstractTool;
use Xpressengine\Permission\Instance;

/**
 * Class NaverMapTool
 *
 * @category    NaverMapTool
 * @package     Xpressengine\Plugins\NaverMapTool
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
 */
class NaverMapTool extends AbstractTool
{
    protected $configs;

    protected $gate;

    public function __construct(ConfigManager $configs, Gate $gate, $instanceId)
    {
        parent::__construct($instanceId);

        $this->configs = $configs;
        $this->gate = $gate;
    }

    public function initAssets()
    {
        $config = $this->configs->getOrNew('naver_map_tool');

        XeFrontend::html('naver_map_tool.load_url')->content("
        <script>
            (function() {
            
                var _url = {
                    popup: '".route('naver_map_tool::popup')."',      
                    edit_popup: '".route('naver_map_tool::popup.edit')."'
                };
            
                var URL = {
                    get: function (type) {
                        return _url[type];                 
                    }
                };
                
                window.naverToolURL = URL;
            })();
        </script>
        ")->load();
        XeFrontend::js([
            'https://openapi.map.naver.com/openapi/v3/maps.js?clientId=' . $config->get('key'),
            asset($this->getAssetsPath() . '/naverMapTool.js'),
            asset($this->getAssetsPath() . '/naverMapRenderer.js?key=' . $config->get('key'))
        ])->load();
    }

    public function getIcon()
    {
        return asset($this->getAssetsPath() . '/icon.png');
    }

    public static function getInstanceSettingURI($instanceId)
    {
        return null;
//        return route('settings.plugin.google_map_tool.setting', $instanceId);
    }

    public static function getKey($instanceId)
    {
        return static::getId() .  '.' . $instanceId;
    }

    public function compile($content)
    {
        $config = $this->configs->getOrNew('naver_map_tool');

        XeFrontend::js([
            'https://openapi.map.naver.com/openapi/v3/maps.js?clientId=' . $config->get('key'),
            asset($this->getAssetsPath() . '/naverMapRenderer.js?key=' . $config->get('key'))
        ])->load();

        $crawler = $this->createCrawler($content);
        $crawler->filter('*[xe-tool-id="editortool/navermap@navermap"]')->each(function (Crawler $node, $i) {
            $dom = $node->getNode(0);
            $script = $dom->ownerDocument->createElement('script');
            $txt = $dom->ownerDocument->createTextNode('$(function() { $("#' . $node->attr('id') . '").naverMapRender();})');
            $script->appendChild($txt);
            $dom->appendChild($script);

            $node->add($dom);
        });

        return $crawler->getNode(0)->ownerDocument->saveHTML($crawler->getNode(0));
        
    }
    private function getAssetsPath()
    {
        return str_replace(base_path(), '', realpath(__DIR__ . '/../assets'));
    }

    private function createCrawler($content)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($content);

        return $crawler;
    }
}
