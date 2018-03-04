<style>
   th{
        font-weight: bold;font-size: 16px;text-align: center;
    }
    td{text-align: center}
</style>
<div class="content">
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12">

                <div class="card">
                    <div class="card-header" data-background-color="red">
                        <h4 class="title">Dumbways</h4>
                        <p class="category" id="tgl_table">
                            <span class="curmonth"> </span>
                        </p>	
                    </div>
                    <div class="card-content table-responsive">
                        <table class="table" id="table">
                            <thead class="text-primary">
                            <th>NO</th>
                            <th>USERNAME</th>
                            <th>TITLE</th>
                            <th >COMMENT</th>
                        

                            </thead>
                            <tbody id="body_list">

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            
        </div>
        
        
    </div>
</div>
 <script>
       $(document).ready(function () {

       cetakbro();
    }); 
        
    function cetakbro(){
         var string = '';

         $.getJSON("assets/php/controller.php?action=posts", function (data) {
        for (var i = 0; i < data.length; i++) {
            var rank = i + 1;
          // console.log(data[0][0]);
           // console.log(data);
            
            string += "<tr>\\n\
                                            <td>" + rank + "</td>\
                                            <td>" + data[0][0] + "</td>\
                                            <td>" + data[i][1] + "</td>\
                                            <td>" + data[i][2] + "</td>\
                                        </tr>";
                                            }
//       
   
     $('#body_list').html(string);
      });
    }
    
    </script>