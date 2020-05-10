<!DOCTYPE html>
<html lang="en">
<head>
  <title>Question Example</title>
  <?php require 'includes/header.php' ?>
</head>
<body>

<div class="jumbotron text-center p-3 ">
  <h3>Question</h3>
  <p>Days</p>
</div>
<div class="container">

    <div class="row">
        <div class='col-12'>
            <div class="form-group">
              <label for="title">Title</label>
              <input type="text" class="form-control form-control" name="title" id="title" aria-describedby="Title" placeholder="Title">
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
              <label for="fromdate">From Date</label>
              <input type="date" class="form-control"  name="fromdate" id="fdate" aria-describedby="FromDate" placeholder="From Date">
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
              <label for="todate">To Date</label>
              <input type="date" class="form-control" name="todate" id="tdate" aria-describedby="ToDate" placeholder="To Date">
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label for="status">Status</label>
                <select class="custom-select" name="status" id="status">
                    <option value="1">Enable</option>
                    <option value="0">Disable</option>
                </select>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <label for="question">Question</label>
                <textarea class="form-control" name="question" id="question" rows="3"></textarea>
            </div>
            <h3>Ans</h3>
        </div>
        
        <div class="col-12" id="ansdetails">
            
        </div>
    </div>
    
    <div class="d-flex justify-content-around mb-4" >
                <button type="button" id="add" class="btn btn-warning">Add</button>
                <button type="button" id="submit" class="btn btn-success">Submit</button>
            </div>        
</div>

<!--$data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');-->

<script>
    const $summernote = $('#question'), $submit = $('#submit'), $fdate = $('#fdate'), $tdate = $('#tdate'), $status = $('#status'), $title = $('#title'), $ansdetails = $('#ansdetails'), $add = $('#add'), $inputs = $('input[name="ansbox[]"]');
    let $checkbox = 0;

    $(function() {

        const addRow = ($this) => {
            $this.append(
                `<div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="checkbox" style="left:unset;opacity:unset" name="anscheck" aria-label="Ans">
                        </div>
                    </div>
                    <input type="text" name="ansbox[]" class="form-control" aria-label="AnsBox">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove">Remove</button>
                    </div>
                </div>`
            );
        };

        const removeRow = ($this) => {
            $this.closest('.input-group').remove();
        };

        function currentDate(i = ''){
            let today = new Date();

            if(i == 'add'){
                today.setDate(today.getDate() + 1);
            }
            
            return today.getFullYear()+'-'+ ( String(today.getMonth() + 1).padStart(2, 0) )+'-'+( String(today.getDate()).padStart(2, 0) ); 
        }
        function defaultValue(){
            $fdate.val(currentDate());
            $tdate.val(currentDate('add'));
            $status.val(1);
            $title.val('');

            $ansdetails.empty();
            addRow($ansdetails);
            addRow($ansdetails);
        }
        $summernote.summernote();
        
        $fdate.val( currentDate('') );
        $tdate.val( currentDate('add') );

        function getAns(){
            let $inputs = $('input[name="ansbox[]"]');
            if($inputs.length != 1 ){
                let arrayAns =new Array(), arrayOption = new Array();
                $inputs.each(function(i){
                    arrayAns[i] = {};
                    if($('input[name=anscheck]').eq(i).prop('checked') == true){
                        arrayOption.push( i );
                    }
                    arrayAns[i]['answer'] = $(this).val().trim();
                });
                return   [ JSON.stringify(arrayAns), arrayOption.join(',') ];
            } else {
                return ['', $inputs.eq(0).val()];
            }
        }

        $submit.click(function(e){
            let question = $summernote.summernote('code'), arrayFilling = new Array(), title = $title.val(), choose, $inputs = $('input[name="ansbox[]"]');
            
            if($inputs.length != 1){
                $checkbox =  $( 'input[name="anscheck"]:checked' ).length;
                if($checkbox == 1){
                    choose = 'radio';
                } else {
                    choose = 'checkbox';
                }
            } else {
                choose = 'text';
                $checkbox = 1;
            }
            
            if($checkbox == 0 || title == '' || $inputs.length == 0){
                alert('please fill details');
                return false;
            }

            arrayFilling = getAns();

            $.ajax({
                url : 'details.php',
                type : 'post',
                beforeSend : function(){
                    $submit.prop('disabled', true);
                },
                data : {
                    type : 'insertQuestion',
                    choose : choose,
                    question : encodeURIComponent(question),
                    fdate : $fdate.val(),
                    tdate : $tdate.val(),
                    status : $status.val(),
                    title : encodeURIComponent($title.val()),
                    ans : encodeURIComponent(arrayFilling[0]),
                    option : encodeURIComponent(arrayFilling[1]),
                }
            }).always(function(){
                defaultValue();
                alert('added successfully');
                window.location.href = 'index.php';
                $submit.delay(300).prop('disabled', false);
            });
        });
        $add.click(function(e){
            addRow($ansdetails);
        });
        $(document).on('click', '.remove', function(e){
            removeRow($(this));
        });
        defaultValue();
    });
</script>

</body>
</html>
