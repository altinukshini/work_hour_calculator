<?php

    require_once 'functions.php';
    require_once 'Classes/PHPExcel/IOFactory.php';

    //  Read Excel workbook
    $inputFileName = 'data/example-table2.xlsx';

    try {
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);
    } catch(Exception $e) {
        die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
    } 

    $sheet = $objPHPExcel->getSheet(0); 
    $highestRow = $sheet->getHighestRow();

    $highestColumn = $sheet->getHighestColumn();

    $count = 0;

    for($row = 2; $row <= $highestRow; $row++) {
        $start =  $sheet->getCell('B' . $row)->getValue();
        $start = PHPExcel_Style_NumberFormat::toFormattedString($start, "YYYY-MM-DD H:mm:ss");
        $end =  $sheet->getCell('C' . $row)->getValue();
        $end = PHPExcel_Style_NumberFormat::toFormattedString($end, "YYYY-MM-DD H:mm:ss");
        $status = $sheet->getCell('D' . $row)->getValue();
        $assigne =  $sheet->getCell('E' . $row)->getValue();

        if ($status == "closed") {
            $seconds = calculateWorkHours($start,$end);

            $x["TOTAL"][] = $seconds;
            $x[$assigne][] = $seconds;

        }

    }

?>

<html>
<header>
    <title>Work hour calculator</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <link rel="stylesheet" href="style/css/bootstrap.min.css"  type="text/css"/>


    <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
    <script src="style/js/bootstrap.js"></script>

</header>
<style> 
    body{
        color: #444;
        margin:0;
        padding:0;
    }
    .container{
        width:90%;
    }
    body, .container, .table{
        font-size:0.9em;
    }
    h3{
        color:#1E73BE;
    }
    h2{
        color: #444;
    }
    .total-col {
        width: 33.333%;
        float: left;
    }
</style>
<body>
    
<div class="container">
    <div class="row">

        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:20px;">
            <h1>Work hour calculator</h1>
            <hr style="border-color:#ebebeb;">

            <?php
            foreach(array_keys($x) as $key) {

                $total = array_sum($x[$key]);
                $average = (int)(array_sum($x[$key]) / count($x[$key]));
                $max = max($x[$key]);
                $min = min($x[$key]);

                if ($key == "TOTAL") {
                ?>

                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <h3>Total</h3>
                        <b>Closed: <span style="color:#1E73BE;font-size:14px;"><?php echo count($x[$key]); ?> tickets.</span></b> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; <b>Total working hours: </b> <?php echo format_time($total); ?><br /><br />
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <!-- <div class="row"> -->
                            <div class="total-col col-sm-12 col-md-3 col-lg-3">
                                <b>Average: </b>
                                <br /><?php echo format_time($average); ?>
                            </div>
                            <div class="total-col col-sm-12 col-md-3 col-lg-3">
                                <b>Max: </b>
                                <br /><?php echo format_time($max); ?>
                            </div>
                            <div class="total-col col-sm-12 col-md-3 col-lg-3">
                                <b>Min:</b>
                                <br /><?php echo format_time($min); ?>
                            </div>
                        <!-- </div> -->
                    </div>
                    <br style="clear:both;">
                    <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:40px;clear:both;">
                        <h3>Per assignee</h3>
                        <table class="table table-hover table-responsive">
                          <thead>
                            <tr>
                              <th>Assignee</th>
                              <th>Closed tickets</th>
                              <th>Total working hours</th>
                              <th>Average working hours</th>
                              <th>Max working hours</th>
                              <th>Min working hours</th>
                            </tr>
                          </thead>
                          <tbody>
                        <?php 
                } else { ?>
                            <tr>
                              <td><?php echo $key; ?></td>
                              <td><span style="color:#1E73BE;font-size:14px;"><?php echo count($x[$key]); ?></span></b> </span></b></td>
                              <td><?php echo format_time($total); ?></td>
                              <td><?php echo format_time($average); ?></td>
                              <td><?php echo format_time($max); ?></td>
                              <td><?php echo format_time($min); ?></td>
                            </tr>
                        

                <?php
                }

            }
            ?>
                          </tbody>
                        </table>
                    </div>

        </div>
    </div>
    <!-- close row -->
</div>

</body>
</html>




