@extends('layouts.app')

@section('content')
    <div id="lookup-dialog-modal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lookup-dialog-title">Pilih Data</h5>
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                        <i class="ph-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="lookup-dialog-filter"></div>
                    <table class="table table-bordered table-hover w-100 display" id="lookup-dialog-datatable">
                        <thead class="text-bg-light"></thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include($data['content'], $data)
@endsection

@isset($data['plugins'])
    @if(is_array($data['plugins']))
        @foreach($data['plugins'] as $p)
            @include('layouts.components.' . $p)
        @endforeach
    @endif
@endif
