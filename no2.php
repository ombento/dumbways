<html>
    <head>
        <title></title>
        <style>
            table, th, td {
                border: 1px solid black;
            }
            th{
                font-weight: bold;font-size: 14px;text-align: center;
            }
        </style>
    </head>
    <body>
        <table class="table" cellspacing="0" width="100%">
                                    <thead class="">
                                    <th>NO</th>
                             
                                    <th>NAMA</th>
                                    <th>TELEPON</th>
                                    <th >HOBI</th>
                                    <th>ALAMAT</th>
                                    </thead>
                                    <tbody id="body" style="text-align: center">

                                    </tbody>
                                </table>
    </body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
    <script>
       $(document).ready(function () {

       cetakbro('Beni','0881212112','Main,kerja,motret', 'Jl.sana sini aja');
    }); 
        
    function cetakbro(nama,tlp,hobi,alamat){

	for (var i = 0; i < 1; i++) {
            var rank = 1;
          // console.log(data[0][0]);
           // console.log(data);
            var string ='';
            string += "<tr>\\n\
                                            <td>" + rank + "</td>\
                                            <td>" + nama + "</td>\
                                            <td>" + tlp + "</td>\
                                            <td>" + hobi + "</td>\
                                            <td>" + alamat + "</td>\
                                        </tr>";
                                            }
//       
   
     $('#body').html(string);
        
    }
    </script>
</html>
