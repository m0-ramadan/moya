<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>Faq</h5>

            <a class="btn btn-success" href="{{route('admin.faq.create')}}">Add </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="basic-1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Item Order</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($faqs as $key=>$faq)
                        <tr>
                            <td> {{++$key}}       </td>
                            <td>{{$faq->question}} </td>
                            <td>{{$faq->answer}} </td>
                            <td>{{$faq->item_order}} </td>

                            <td>
                               <button class="btn btn-success"> <a  href="{{route('admin.faq.edit',[$faq->id])}}"><i class="fa fa-edit text-white"></i>  </a> </button>
                                <form method="post" action="{{route('admin.faq.destroy',$faq->id)}}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-primary"  ><i class="fa fa-trash-o"></i></button>
                                </form>

                            </td>

                        </tr>


                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>