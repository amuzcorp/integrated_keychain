@section('page_title')
    <h2>통합 키체인</h2>
@endsection

@section('page_description')
    <small>여러 플러그인이 API키를 중복 저장하지 않도록 통합관리를 도와줍니다.</small>
@endsection

<form method="post" action="{{route('manage.integrated_keychain.updateConfig')}}">
    <input type="hidden" name="_token" value="{{csrf_token()}}" />
    <div class="keychain_container">
        @foreach($key_chains as $tab => $groups)
        <div class="panel">
            <div class="panel-heading">
                <h3>{{ $tab }}</h3>
            </div>
            <div class="panel-body">
                @foreach($groups as $group => $keychain)
                <div class="panel">
                    <div class="panel-heading">
                        <h4>{{ $group }}</h4>
                    </div>
                    <div class="panel-body">
                        @foreach($keychain as $key_id => $unique_key)
                            {{ uio(array_get($unique_key,'_type','formText'), array_get($unique_key,'_args')) }}
                            <small class="requester">
                                요청한 플러그인 :
                                @foreach($unique_key['requester'] as $pluginId => $requester)

                                    <button type="button" class="xe-badge {{ array_get($requester, 'how') != null ? 'xe-btn-primary-outline' : 'xe-btn-success-outline' }}" data-toggle="popover" data-popover-content="#popover_{{$key_id}}" data-placement="bottom">
                                    {{ array_get($requester,'title') }}
                                        @if(array_get($requester, 'how') != null)
                                            <i class="xi-help-o"></i>
                                        @endif
                                    </button>

                                    @if(array_get($requester, 'how') != null)
                                    <div id="popover_{{$key_id}}" style="display: none;">
                                        <div class="popover-body">
                                            <div class="setting-box__popover-content">
                                                {{ array_get($requester, 'how') }}
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                @endforeach
                            </small>
                        @endforeach
                    </div>
                    <div class="panel-footer">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary">{{xe_trans('xe::save')}}</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</form>

<script>

    $(function () {

        $("[data-toggle=popover]").popover({
            html : true,
            content: function() {
                var content = $(this).attr("data-popover-content");
                return $(content).children(".popover-body").html();
            },
            title: function() {
                var titleResult = '';
                var title = $(this).attr("data-popover-content");
                var titleLength = $(title).children(".popover-heading").length;

                if(titleLength > 0) {
                    titleResult = $(title).children(".popover-heading").html();
                } else {
                    titleResult = '';
                }

                return titleResult;
            }
        });
    });
</script>
