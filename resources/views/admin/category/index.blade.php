@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="row page-header">
                <h1>Categories <small>Management Area</small></h1>
                <a href="{{route('categoryAdd')}}" class="btn btn-success">New Category</a>
            </div>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($categoryCollection as $category)
                <tr>
                    <td>{{$category->id}}</td>
                    <td>{{$category->name}}</td>
                    <td>
                        <a href="{{route('categoryEdit', ['id' => $category->id])}}"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>&nbsp;
                        <a href="#"><span class="glyphicon glyphicon-trash" aria-hidden="true" data-toggle="modal" data-target="#deleteConfirmationModal" data-whatever="{{route('categoryDelete', ['id' => $category->id])}}|{{ $category->name }}"></span></a>&nbsp;
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {!! $categoryCollection->render() !!}
    </div>

    <!-- Deleting Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Deleting Confirmation</h4>
                </div>
                <div class="modal-body">
                    <p>Category to be deleted: <strong><span id="itemNameDestination"></span></strong></p>
                    <p>Are you sure ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <a class="btn btn-danger" href="#">Delete</a>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script type="text/javascript">
        $(function() {
            $('#deleteConfirmationModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var params = button.data('whatever').split("|"); // Extract info from data-* attributes
                var modal = $(this);
                console.log(params);
                modal.find('.modal-footer a').attr('href', params[0]);
                modal.find('.modal-body span#itemNameDestination').text(params[1]);
            })
        });
    </script>
@endsection