<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['emplogin'])==0)
    {   
header('location:index.php');
}
else{
if(isset($_POST['apply']))
{
$empid=$_SESSION['eid'];
 $leavetype=$_POST['leavetype'];
$fromdate=$_POST['fromdate'];  
$todate=$_POST['todate'];
$description=$_POST['description'];  
$CL=$_POST['CL'];
$PL=$_POST['PL'];
$emailid=$_POST['EmailId'];
$status=0;
$isread=0;
$LeaveCount=$todate - $fromdate +1;
$x=0;

if($fromdate > $todate){
                $error=" ToDate should be greater than FromDate ";
           }

if($leavetype=='CL')
    {if($CL<=0)
        {$error="CL Cannot Be Applied";
     $x=1;           
    }
    if($LeaveCount>$CL)
    {
        $x=1;
        $error=$LeaveCount." CL Leaves not available.";
    }
}
if($leavetype=='PL')
    {if($PL<=0)
       { $error="PL Cannot Be Applied";
        $x=1;
   }
   if($LeaveCount>$PL)
    {
        $x=1;
        $error=$LeaveCount." PL Leaves not available.";
    }

}

if($x==0)
{
$sql="INSERT INTO tblleaves(LeaveType,ToDate,FromDate,Description,Status,IsRead,empid,LeaveCount) VALUES(:leavetype,:todate,:fromdate,:description,:status,:isread,:empid,:LeaveCount)";
$query = $dbh->prepare($sql);
$query->bindParam(':leavetype',$leavetype,PDO::PARAM_STR);
$query->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
$query->bindParam(':todate',$todate,PDO::PARAM_STR);
$query->bindParam(':description',$description,PDO::PARAM_STR);
$query->bindParam(':status',$status,PDO::PARAM_STR);
$query->bindParam(':isread',$isread,PDO::PARAM_STR);
$query->bindParam(':empid',$empid,PDO::PARAM_STR);
$query->bindParam(':LeaveCount',$LeaveCount,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();



if($lastInsertId)
     {
        if($leavetype=='CL')
        {
        $CL=$CL-$LeaveCount;
        $data = [
        'cl' => $CL,
        'email' => $emailid
        ];
        $sql="UPDATE tblemployees SET CL=:cl WHERE EmailId=:email AND CL>0";
        $query = $dbh->prepare($sql);
        $query->bindParam(':CL',$CL,PDO::PARAM_STR);
        $query->execute($data);
     
        $msg="Leave applied successfully";
    }
    else
     {
        $PL=$PL-$LeaveCount;
        $data = [
        'pl' => $PL,
        'email' => $emailid
        ];
        $sql="UPDATE tblemployees SET PL=:pl WHERE EmailId=:email AND PL>0";
        $query = $dbh->prepare($sql);
        $query->bindParam(':PL',$PL,PDO::PARAM_STR);
        $query->execute($data);
     
        $msg="Leave applied successfully";
}

}



else 
{
$error="Something went wrong. Please try again";
}
}
}
    ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Employe | Apply Leave</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.min.css"/>
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet"> 
        <link href="assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="assets/css/custom.css" rel="stylesheet" type="text/css"/>
  <style>
        .errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
        </style>
 


    </head>
    <body background="assets/images/background.jpg">
  <?php include('includes/header.php');?>
            
       <?php include('includes/sidebar.php');?>
   <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title">Apply for Leave</div>
                    </div>
                    <div class="col s12 m12 l8">
                        <div class="card">
                            <div class="card-content">
                                <form id="example-form" method="post" name="addemp">
                                    <div>
                                        <h3>Apply for Leave</h3>
                                        <section>
                                            <div class="wizard-content">
                                                <div class="row">
                                                    <div class="col m12">
                                                        <div class="row">
     <?php if($error){?><div class="errorWrap"><strong>ERROR </strong>:<?php echo htmlentities($error); ?> </div><?php } 
                else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>




<?php 
$eid=$_SESSION['emplogin'];
$sql = "SELECT * from  tblemployees where EmailId=:eid";
$query = $dbh -> prepare($sql);
$query -> bindParam(':eid',$eid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{               ?> 
 <div class="input-field col  s12">
Email Id<label for="EmailId"></label>
<input  name="EmailId" id="EmailId" value="<?php echo htmlentities($result->EmailId);?>" type="text" autocomplete="off" readonly required> 
</div>

<?php }}?>





 <div class="input-field col  s3">
<select  name="leavetype" autocomplete="off">
<option value="">Select leave type...</option>
<?php $sql = "SELECT  LeaveType from tblleavetype";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{   ?>                                            
<option value="<?php echo htmlentities($result->LeaveType);?>"><?php echo htmlentities($result->LeaveType);?></option>$sql2 = "SELECT  LeaveType from tblleavetype";

<?php }} ?>
</select>
</div>

<?php 
$eid=$_SESSION['emplogin'];
$sql = "SELECT * from  tblemployees where EmailId=:eid";
$query = $dbh -> prepare($sql);
$query -> bindParam(':eid',$eid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{               ?> 
 <div class="input-field col  s3">
<label for="CL">&nbsp &nbsp CL Available</label>
<input  name="CL" id="CL" value="<?php echo htmlentities($result->CL);?>" type="text" autocomplete="off" readonly required> 
</div>


<div class="input-field col m6 s3">
<label for="PL">&nbsp &nbsp PL Available</label>
<input id="PL" name="PL" value="<?php echo htmlentities($result->PL);?>"  type="text" readonly required>
</div> 
<?php }}?>

<div class="input-field col m6 s12">
<label for="fromdate">From  Date</label>
<input placeholder="" id="mask1" name="fromdate" class="masked" type="text" data-inputmask="'alias': 'date'" required>
</div>
<div class="input-field col m6 s12">
<label for="todate">To Date</label>
<input placeholder="" id="mask1" name="todate" class="masked" type="text" data-inputmask="'alias': 'date'" required>
</div>
<div class="input-field col m12 s12">
<label for="birthdate">Description</label>    

<textarea id="textarea1" name="description" class="materialize-textarea" length="500" required></textarea>
</div>
</div>
      <button type="submit" name="apply" id="apply" class="waves-effect waves-light btn indigo m-b-xs">Apply</button>                                             

                                                </div>
                                            </div>
                                        </section>
                                     
                                    
                                        </section>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div class="left-sidebar-hover"></div>
        
        <!-- Javascripts -->
        <script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="assets/js/alpha.min.js"></script>
        <script src="assets/js/pages/form_elements.js"></script>
          <script src="assets/js/pages/form-input-mask.js"></script>
                <script src="assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>
    </body>
</html>
<?php } ?> 