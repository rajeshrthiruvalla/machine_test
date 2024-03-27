<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css"> --}}
    <link href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.css">
    <style>
        .color{
            height:35px;
        }
        .prev-image{
            width:100%;
            aspect-ratio : 1 / 1;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card-header">
                    Product form
                  </div>
                  <div class="card-body">
                    <form id="product_form">
                        <div class="mb-3">
                            <input type="hidden" name="id" id="id" value="0">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" id="title" >
                        </div>
                        <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description"></textarea>
                        </div>
                        <div class="mb-3" id="varient_grid">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text">color</label>
                                        <input type="color" name="colors[]" class="form-control color">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text">size</label>
                                        <input type="text" class="form-control" name="sizes[]">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-success" type="button" onclick="addVarient()">Add +</button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" name="image" id="image"/>
                        </div>
                        <div class="mb-3">
                            <div class="row" id="file_preview">
                            </div>
                        </div>
                        <button type="submit" id="submit" class="btn btn-primary">Save</button>
                        <button type="reset" id="refresh" onclick="refreshData()" class="btn btn-warning">Referesh</button>
                    </form>
                  </div>
                </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="container">
                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> --}}
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  table=new DataTable('#example',{
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{!! route('product_data_table') !!}",
        columns: [
           {data: 'DT_RowIndex', name: 'id'},
            { data: 'title', name: 'title' },
            { data: 'description', name: 'description' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'delete', name: 'delete', orderable: false, searchable: false }
        ]
    });

    function addVarient()
    {
        $('#varient_grid').append(` <div class="row">
                                <div class="col-md-2">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text">color</label>
                                        <input type="color" name="colors[]" class="form-control color">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text">size</label>
                                        <input type="text" class="form-control" name="sizes[]">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-success" type="button" onclick="addVarient()">Add +</button>
                                    <button class="btn btn-danger" type="button" onclick="$(this).closest('.row').remove()">Remove -</button>
                                </div>
                            </div>`);
    }


</script>
<script>
    $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': `{{ csrf_token() }}`
                    }
                });
    $(document).ready(function() {
        $('#image').change(function() {
            var fileInput = document.getElementById('image');
            var file = fileInput.files[0];
            if (file) {
                var formData = new FormData();
                formData.append('file', file);

                $.ajax({
                    url: 'product_file_upload', // Replace with your upload endpoint
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response.status)
                        {
                          var url=`{{url('/')}}`;
                           $('#file_preview').html(`<div class="col-md-3">
                                                      <img src="${url}/storage/product${response.image}" class="prev-image"/>
                                                      <input type="hidden" name="file" value="${response.image}"/>
                                                    </div>`);
                        }else{

                            Swal.fire({
                                        icon: "error",
                                        title: "Oops...",
                                        text: response.message
                                    });

                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error uploading file:', error);
                        // You can handle error response here
                    }
                });
            } else {
                Swal.fire({
                                        icon: "error",
                                        title: "Oops...",
                                        text: 'No file chosen'
                                    });
            }
        });

        $('#product_form').submit(function(e){
                e.preventDefault();

                var data= new FormData(this);

            var setting={
                url:'{{ route("products.store")}}',
                    dataType:'json',
                    type:'post',
                    data:data,
                    processData: false,
                    contentType: false,
                    success:function(response){
                    if(response.status)
                    {
                        Swal.fire(response.message);
                         table.draw();
                         $('#refresh').click();
                    }else
                    {
                        Swal.fire({
                                                icon: "error",
                                                title: "Oops...",
                                                text: response.message
                                            });
                    }
                    },
                    error: function(xhr) {
                        $(".preloader").hide();
                console.log(xhr.responseText); // this line will save you tons of hours while debugging
                // do something here because of error
            }
                };
                var id=$('#id').val();
                if(id>0)
                {
                setting.url='{{ url("products")}}/'+id;
                data.append('_method','PUT');
                }
                    $.ajax(setting);
            });
     });
     function refreshData()
    {
               $('#id').val(0);
               $('#submit').html('Save');
               $('#file_preview').empty();
               $('#varient_grid').html(`<div class="row">
                                <div class="col-md-2">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text">color</label>
                                        <input type="color" name="colors[]" class="form-control color">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text">size</label>
                                        <input type="text" class="form-control" name="sizes[]">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-success" type="button" onclick="addVarient()">Add +</button>
                                </div>
                            </div>`);
    }
    function editData(id)
    {


        jQuery.ajax({
            url:'{{url("products")}}/'+id+'/edit',
            type:"get",
            dataType:'json',
            data:{
                "id":id
            },
            success:function(response){
                var master=response.data;
                for(x in master)
                {
                    if($('#'+x).length)
                       $('#'+x).val(master[x]);
                }
               $('#submit').html('Update');
               var variations=response.data.variations;
               var url=`{{url('/')}}`;
            $('#file_preview').html(`<div class="col-md-3">
                                        <img src="${url}/storage/product${master.file}" class="prev-image"/>
                                        <input type="hidden" name="file" value="${master.file}"/>
                                    </div>`);
                  $('#varient_grid').empty();
                variations.forEach(function(item,index){
                  $('#varient_grid').append(` <div class="row">
                                <div class="col-md-2">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text">color</label>
                                        <input type="color" name="colors[]" class="form-control color" value="${item.color}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text">size</label>
                                        <input type="text" class="form-control" name="sizes[]" value="${item.size}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-success" type="button" onclick="addVarient()">Add +</button>
                                    ${index>0?`<button class="btn btn-danger" type="button" onclick="$(this).closest('.row').remove()">Remove -</button>`:``}
                                </div>
                            </div>`);
                });
            },
             error: function(xhr) {
         console.log(xhr.responseText); // this line will save you tons of hours while debugging
        // do something here because of error
       }
        })
    }
    function deleteData(id)
    {
        Swal.fire({
  title: "Do you want to delete?",
  showCancelButton: true,
  confirmButtonText: "Delete",
}).then((result) => {
    if (result.isConfirmed) {

        $.ajax({
            url:"{{url('products')}}/"+id,
            type:"delete",
            dataType:'json',
            data:{
                "id":id
            },
            success:function(response){
                if(response.status)
                {
                    Swal.fire(response.message);
                         table.draw();
                          $('#refresh').click();
                }
                else
                {
                    Swal.fire({
                                                icon: "error",
                                                title: "Oops...",
                                                text: response.message
                                            });
               }
            },
             error: function(xhr) {
         console.log(xhr.responseText); // this line will save you tons of hours while debugging
        // do something here because of error
       }
        })
          }
            });
    }

</script>
</body>
</html>
