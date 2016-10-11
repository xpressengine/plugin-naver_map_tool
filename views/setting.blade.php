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
                <h3 class="panel-title">상세 설정</h3>
            </div>
        </div>
        <div class="panel-collapse collapse in">
            <form method="post" action="{{ route('settings.plugin.emoticon.setting', $instanceId) }}">
                {{ csrf_field() }}
                <div class="panel-body">

                    {{--<div class="panel">--}}
                        {{--<div class="panel-heading">--}}
                            {{--<div class="pull-left">--}}
                                {{--<h4 class="panel-title">기본설정</h4>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="panel-body">--}}
                            {{--<div class="row">--}}
                                {{--<div class="col-sm-6">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<div class="clearfix">--}}
                                            {{--<label>설정1</label>--}}
                                        {{--</div>--}}
                                        {{--<input type="text" class="form-control" name="var1" value="">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="col-sm-6">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<div class="clearfix">--}}
                                            {{--<label>설정2</label>--}}
                                        {{--</div>--}}
                                        {{--<input type="text" class="form-control" name="var2" value="">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>사용 권한</label>
                                        <div class="well">
                                            {!! uio('permission', $permArgs['use']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        {{--</div>--}}
                    {{--</div>--}}
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