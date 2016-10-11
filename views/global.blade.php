@if ($config->get('key'))
{{
XeFrontend::js([
    'https://openapi.map.naver.com/openapi/v3/maps.js?clientId=' . $config->get('key')
])->load()
}}
@endif

@section('page_title')
    <h2>Editor 네이버 지도 설정</h2>
@endsection

@section('page_description')
    <small>네이버 지도 설정페이지 입니다.</small>
@endsection

@section('content_bread_crumbs')

@endsection

<div class="panel-group" role="tablist" aria-multiselectable="true">
    <div class="panel">
        <div class="panel-heading">
            <div class="pull-left">
                <h3 class="panel-title">전역 설정</h3>
            </div>
        </div>
        <div class="panel-collapse collapse in">
            <form method="post" action="{{ route('settings.plugin.naver_map_tool.global') }}">
                {{ csrf_field() }}
                <div class="panel-body">

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="clearfix">
                                    <label>API Key</label>
                                </div>
                                <input type="text" class="form-control" name="key" value="{{ $config->get('key') }}">
                            </div>
                        </div>
                        <div class="col-sm-11">
                            <div class="form-group">
                                <div class="clearfix">
                                    <label>
                                        기본지도 설정
                                        <small>지도가 보여질 최초 좌표와 줌 레벨을 설정합니다.</small>
                                    </label>
                                </div>
                                @if ($config->get('key'))
                                    <div id="map-wrapper" style="height: 400px;width:100%"></div>
                                @else
                                        <div class="panel">
                                            <div class="panel-body">
                                                API Key 등록 후 설정할 수 있습니다.
                                            </div>
                                        </div>
                                @endif
                                <input type="hidden" name="lat" value="{{ $config->get('lat', '37.566535') }}">
                                <input type="hidden" name="lng" value="{{ $config->get('lng', '126.97796919999996') }}">
                                <input type="hidden" name="zoom" value="{{ $config->get('zoom', 10) }}">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="panel-footer">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary">저장</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@if ($config->get('key'))
<script type="text/javascript">
    $(function () {

        var lat = $('input[name="lat"]').val(),
            lng = $('input[name="lng"]').val(),
            zoom = $('input[name="zoom"]').val();

        var mapOptions = {
            center: new naver.maps.LatLng(lat, lng),
            zoom: parseInt(zoom)
        };

        var map = new naver.maps.Map('map-wrapper', mapOptions);

        var marker = new naver.maps.Marker({
            map: map,
            position: new naver.maps.LatLng(lat, lng)
        });

//        var infowindow = new naver.maps.InfoWindow({
//            content: contentString
//        });

        naver.maps.Event.addListener(map, "center_changed", function(e) {
            marker.setPosition(map.getCenter());

            $('input[name="lat"]').val(map.getCenter().lat());
            $('input[name="lng"]').val(map.getCenter().lng());
            $('input[name="zoom"]').val(map.getZoom());
        });

//        infowindow.open(map, marker);

    });
</script>
@endif