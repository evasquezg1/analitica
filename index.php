<!DOCTYPE html>
<html>
<head>
	<title>Consulta de archivos</title>
	<link href="http://paxzupruebas.com/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

	
</head>
<body>

	<div class="container" style="min-height:500px;">
        <div class="container">     
        	<h2>Consultar archivos</h2>  
            <div class="row">                          
	            <div class="col-md-3">
	                <form class="form-horizontal" method="post" onsubmit="return false">
	                    <div class="form-group">
	                        <label class="control-label">Fecha a consultar : </label>
	                        <input class="form-control col-xs-1" id="fecha" type="date" required="required" min="2019-07-01" autofocus>
	                    </div>
	                    <div class="form-group">
	                        <label class="control-label"></label>
	                        <button type="submit" name="obtener" id="obtener" class="btn btn-success">
	                        	Obtener
	                        </button>
	                    </div>
	                </form>
	            </div>
	        </div>
	        <div class="row mostrar" style="display:none;">
	        	<div class="col-md-3">
	        		<button class="btn btn-info" id="totalF" data-toggle="modal" data-target="#totalFormatos">Ver cantidad total de formatos</button>
	        	</div><br><br><br>
	        	<div class="col-md-12">
	        		<div class="dataTable_wrapper">                                
                        <table id="dataTable" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>                
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Formato</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
	        	</div>
	        </div>

	        <div class="modal fade" id="totalFormatos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  	<div class="modal-dialog" role="document">
			    	<div class="modal-content">
				      	<div class="modal-header">
					        <h5 class="modal-title" id="exampleModalLabel">Formatos de archivo </h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						        <span aria-hidden="true">&times;</span>
				        	</button>
				      	</div>
				      	<div class="modal-body formatos">
				        	<div class="dataTable_wrapper">                                
		                        <table id="dataTableC" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
		                            <thead>
		                                <tr>                
		                                    <th>Formato</th>
		                                    <th>Cantidad</th>
		                                    <th>Fecha</th>
		                                </tr>
		                            </thead>
		                            <tbody>
		                            </tbody>
		                        </table>
		                    </div>
				      	</div>
				      	<div class="modal-footer">
				        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				      	</div>
			    	</div>
			  	</div>
			</div>

        </div>
    </div>

	<script src="http://paxzupruebas.com/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="http://paxzupruebas.com/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="http://paxzupruebas.com/bower_components/metisMenu/dist/metisMenu.min.js"></script>
    <!-- DataTables JavaScript -->
    <script src="http://paxzupruebas.com/bower_components/datatables/media/js/jquery.dataTables.js"></script>

    <script src="https://cdn.datatables.net/rowreorder/1.0.0/js/dataTables.rowReorder.js"></script>
    <link href="https://cdn.datatables.net/rowreorder/1.0.0/css/rowReorder.dataTables.css" type="text/css" rel="stylesheet">

    <script src="http://paxzupruebas.com/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="http://paxzupruebas.com/dist/js/sb-admin-2.js"></script>

	<script type="text/javascript">
		$("#totalF").on('click', function(){
			var fecha = $("#fecha").val();

			$.ajax({
	    		url: 'service.php',
	    		type: 'post',
	    		data: {
	    			task: 'consultarFormatos',
	    			fecha: $("#fecha").val()
	    		}
	    	}).done(function(data){
	    		var data = JSON.parse(data);
	    		var total = 0;

	    		for(var i in data){

	    			total += parseInt(data[i].total);

	    			data[i].nombre_extension = '<center>'+data[i].nombre_extension+'</center>';

		    		var rowIndex = $('#dataTableC').dataTable().fnAddData([
	                    data[i].nombre_extension,
	                    data[i].total,
	                    data[i].fecha_consulta
	                ]);

	                var row = $('#dataTableC').dataTable().fnGetNodes(rowIndex);
	                $(row).attr('id', data[i].id_archivo);
            	}
            	$("#exampleModalLabel").html('Formatos de archivo ('+total+')');
	    	});
		});

    	$("#obtener").on('click', function(){

    		var fecha = $("#fecha").val();

    		if(fecha!=''){
    			$(this).html('<img src="http://paxzupruebas.com/loader.gif" width="50%">');
	    		
	    		$.ajax({
		    		url: 'service.php',
		    		type: 'post',
		    		data: {
		    			task: 'consultarFecha',
		    			fecha: $("#fecha").val()
		    		}
		    	}).done(function(data){
		    		var data = JSON.parse(data);

		    		for(var i in data){

		    			data[i].nombre_extension = '<center>'+data[i].nombre_extension+'</center>';

			    		var rowIndex = $('#dataTable').dataTable().fnAddData([
		                    data[i].id_archivo,
		                    data[i].nombre_archivo,
		                    data[i].nombre_extension,
		                    data[i].fecha_consulta
		                ]);

		                var row = $('#dataTable').dataTable().fnGetNodes(rowIndex);
		                $(row).attr('id', data[i].id_archivo);
	            	}
	            	$("#obtener").html('Obtener');
	            	$(".mostrar").fadeIn();
		    	});


		    }
    	});
    </script>
</body>
</html>