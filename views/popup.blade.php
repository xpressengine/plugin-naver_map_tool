<style>
    #container {margin: 0 auto; width: 500px; margin-top:15px;}
    #mapWrapper { width: 500px; height: 500px;}
    #content {width: 100%; height: 80px;}
</style>

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

        var _generateUUID = function() {
            var d = new Date().getTime();
            if(window.performance && typeof window.performance.now === "function"){
                d += performance.now(); //use high-precision timer if available
            }
            var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = (d + Math.random()*16)%16 | 0;
                d = Math.floor(d/16);
                return (c=='x' ? r : (r&0x3|0x8)).toString(16);
            });
            return uuid;
        };

        return {
            init: function() {
                selfObj = this;

                var latLng = new naver.maps.LatLng('{{ $config->get('lat') }}', '{{ $config->get('lng') }}');

                var mapOptions = {
                    center: latLng,
                    zoom: parseInt('{{ $config->get('zoom') }}')
                };

                map = new naver.maps.Map('mapWrapper', mapOptions);

                marker = new naver.maps.Marker({
                    map: map,
                    position: latLng
                });

                infowindow = new naver.maps.InfoWindow({
                    content: defaultText
                });

                infowindow.open(map, marker);

                selfObj.bindEvent();
            },
            bindEvent: function() {
                naver.maps.Event.addListener(map, 'click', function(event) {
                    marker.setPosition(event.latlng);
                    infowindow.open(map, marker);
                });

//                naver.maps.Event.addListener(marker, 'click', function() {
//                    infowindow.open(map, marker);
//                });

                $(window).on('load', selfObj.preventReloading);
                $('#content').on('keyup', selfObj.setContent);
                $('#checkFullWidth').on('change', selfObj.checkFullWidth);
                $('#btnAppendToEditor').on('click', function() {
                    if(selfObj.isValid()) {
                        selfObj.appendToEditor();
                    }
                });
            },
            preventReloading: function() {
                if(!self.appendToolContent) {
                    alert('팝업을 재실행 하세요.');
                    self.close();
                }
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

                var editorDoc = self.targetEditor.document.$;
                var editorWindow = self.targetEditor.window.$;
                var uuid = _generateUUID();
                var width = $('#hSize').val() + $('#hSize').parent().find('.text-measure').text();
                var height = $('#vSize').val() + $('#vSize').parent().find('.text-measure').text();
                var zoom = map.getZoom();

                var dom = [
                    '<div xe-tool-id="editortool/navermap@navermap"',
                        ' id="navermap_' + uuid + '"',
//                        ' contenteditable="true" data-navermap',
                        ' data-navermap',
                        ' data-width="' + width + '"',
                        ' data-height="' + height + '"',
                        ' data-text="' + text + '"',
                        ' data-lat="' + lat + '"',
                        ' data-lng="' + lng + '"',
                        ' data-zoom="' + zoom + '"',
                        ' style="width:' + width + ';height:' + height + '"></div>'
                ].join('\n');

                var parentWin = opener;
                var childWin = self;

                appendToolContent(dom, function() {
                    $(editorDoc.getElementById('navermap_' + uuid)).naverMapRender({
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

                        }
                    });
                    self.close();
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