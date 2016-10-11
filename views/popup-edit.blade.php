<style>
    #container {margin: 0 auto; width: 500px; margin-top:15px;}
    #mapWrapper { width: 500px; height: 500px;}
    #content {width: 100%; height: 80px;}
</style>
<!-- TODO:: api key 백엔드에서 받아야함 -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />

<div id="container">
    <div id="mapWrapper"></div>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <label for="content">Marker 내용 <small class="text-primary">(HTML사용 가능)</small></label>
                <textarea id="content" placeholder="마커에 표시할 내용을 입력하세요." ></textarea>
            </div>
            <div class="form-group">
                <label for="useFullHorizontal">가로 넓이</label>
                <input type="number" id="hSize" pattern="[0-2000]" min="0" max="10000" />&nbsp;<small class="text-measure">px</small>
                |
                <label for="useFullHorizontal"><small class="text-success">가로 넓이 100% 유지</small></label>
                <input type="checkbox" id="checkFullWidth" />
            </div>
            <div class="form-group">
                <label for="vSize">세로 넓이</label>
                <input type="number" id="vSize" pattern="[0-2000]" min="0" max="10000" />&nbsp;<small class="text-measure">px</small>
            </div>
        </div>
        <div class="panel-footer clearfix">
            <div class="pull-left">
                <span style="color:#6d6d6d;line-height:30px">지도를 클릭하면 마커 위치변경이 가능합니다.</span>
            </div>
            <div class="pull-right">
                <button type="button" id="btnAppendToEditor" class="btn btn-primary">에디터에 넣기</button>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">

    (function() {

        var defaultText = '입력하세요';
        var text = '', mapWidth = 0, mapHeight = 0;
        var selfObj;
        var map, marker, infowindow;

        return {
            init: function() {
                selfObj = this;

                selfObj.bindEvent();
            },
            bindEvent: function() {
                $(window).on('load', selfObj.preventReloading);
                $(window).on('load', selfObj.resetValue);
                $('#content').on('keyup', selfObj.setContent);
                $('#checkFullWidth').on('change', selfObj.checkFullWidth);
                $('#btnAppendToEditor').on('click', function() {
                    if(selfObj.isValid()) {
                        selfObj.appendToEditor();
                    }
                });
            },
            preventReloading: function() {
                if(!self.$targetDom) {
                    alert('팝업을 재실행 하세요.');
                    self.close();
                }
            },
            resetValue: function() {
                var zoom = self.$targetDom.data('zoom');
                var lat = self.$targetDom.data('lat');
                var lng = self.$targetDom.data('lng');
                var text = self.$targetDom.data('text');
                var width = self.$targetDom.data('width');
                var height = self.$targetDom.data('height');

                var myLatLng = new naver.maps.LatLng(lat, lng);

                map = new naver.maps.Map(document.getElementById('mapWrapper'), {
                    center: new naver.maps.LatLng(lat, lng),
                    zoom: parseInt(zoom) || 10
                });

                marker = new naver.maps.Marker({
                    position: myLatLng,
                    map: map
                });

                infowindow = new naver.maps.InfoWindow({
                    content: text
                });

                infowindow.open(map, marker);

                if(parseInt(width) === 100 && width.substr(-1) === '%') {
                    $('#checkFullWidth').prop('checked', true).trigger('change');
                }

                $('#hSize').val(parseInt(width));
                $('#vSize').val(parseInt(height));
                $('#content').val(text);

                naver.maps.Event.addListener(map, 'click', function(event) {
                    marker.setPosition(event.latlng);
                    infowindow.open(map, marker);
                });

            },
            setContent: function(e) {
                var $this = $(this);

                text = $this.val();

                if($this.val() === '') {
                    infowindow.setContent(defaultText);
                }else {
                    infowindow.setContent(text);
                }
            },
            appendToEditor: function() {
                var lat = marker.getPosition().lat();
                var lng = marker.getPosition().lng();
                var text = $('#content').val();

                var editorWindow = self.targetEditor.window.$;

                var width = $('#hSize').val() + $('#hSize').parent().find('.text-measure').text();
                var height = $('#vSize').val() + $('#vSize').parent().find('.text-measure').text();
                var zoom = map.getZoom();

                var parentWin = opener;
                var childWin = self;

                $targetDom.empty().attr({
                    'data-width': width,
                    'data-height': height,
                    'data-text': text,
                    'data-lat': lat,
                    'data-lng': lng,
                    'data-zoom': zoom
                }).data({
                    'width': width,
                    'height': height,
                    'text': text,
                    'lat': lat,
                    'lng': lng,
                    'zoom': zoom
                }).css({
                    width: width,
                    height: height
                }).naverMapRender({
                    win: editorWindow,
                    callback: function(target) {
                        var $btn = $('<button type="button" class="btnEditMap" style="position:absolute;z-index:1;left:0;top:0">Edit</button>').on('click', function() {
                            var cWindow = parentWin.open(parentWin.naverToolURL.get('edit_popup'), 'editPopup', "width=750,height=930,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no");

                            $(cWindow).on('load', function() {
                                cWindow.targetEditor = childWin.targetEditor;
                                cWindow.$targetDom = $(target);
                            });
                        });

                        $(target).prepend($btn);
                        childWin.close();
                    }
                });

            },
            checkFullWidth: function() {
                var $this = $(this);

                if($this.prop('checked')) {
                    $('#hSize').prop('disabled', true);
                    $('#hSize').val(100);
                    $('#hSize').parent().find('.text-measure').text('%');
                }else {
                    $('#hSize').prop('disabled', false);
                    $('#hSize').val('');
                    $('#hSize').parent().find('.text-measure').text('px');
                }
            },
            isValid: function() {
                if($('#content').val() === '') {
                    alert('Marker 표시 내용을 입력하세요.');
                    return false;
                }else if($('#hSize').val() === '') {
                    alert('가로 넓이를 입력하세요.');
                    return false;
                }else if($('#vSize').val() === '') {
                    alert('세로 넓이를 입력하세요.');
                    return false;
                }

                return true;
            }
        };
    })().init();


</script>